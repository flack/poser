<?php
/**
 * @package openpsa.globposer
 * @author CONTENT CONTROL http://www.contentcontrol-berlin.de/
 * @copyright CONTENT CONTROL http://www.contentcontrol-berlin.de/
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */

namespace openpsa\globposer;

use Composer\IO\IOInterface;

class linker
{
    /**
     *
     * @var IOInterface
     */
    private $io;

    private $system_bin = '/usr/local/bin';

    private $vendor_bin;

    /**
     *
     * @param IOInterface $io Composer IO interface
     */
    public function __construct(IOInterface $io, $vendor_bin)
    {
        $this->vendor_bin = $vendor_bin;
        $this->io = $io;
    }

    public function link($file)
    {
        $linkname = $this->system_bin . '/' . $file;
        $target_path = realpath($this->vendor_bin . '/' . $file);

        if (!file_exists($target_path))
        {
            throw new \Exception('Cannot link to nonexistent path ' . $target_path);
        }

        if (is_link($linkname))
        {
            if (!file_exists(realpath($linkname)))
            {
                $this->io->write('Link in <info>' . $linkname . '</info> points to nonexistent path, removing');
                @unlink($linkname);
            }
            else
            {
                if (   realpath($linkname) !== $target_path
                    && md5_file(realpath($linkname)) !== md5_file($target_path))
                {
                    $this->io->write('Skipping <info>' . basename($target_path) . '</info>: Found Link in <info>' . dirname($linkname) . '</info> to <comment>' . realpath($linkname) . '</comment>');
                }
                return;
            }
        }
        else if (is_file($linkname))
        {
            if (md5_file($linkname) !== md5_file($target_path))
            {
                $this->io->write('Skipping <info>' . basename($target_path) . '</info>: Found existing file in <comment>' . dirname($linkname) . '</comment>');
            }
            return;
        }

        if (!is_writeable(dirname($linkname)))
        {
            if ($this->readonly_behavior === null)
            {
                $this->io->write('Directory <info>' . dirname($linkname) . '</info> is not writeable.');
                $reply = $this->io->ask('<question>Please choose:</question> [<comment>(S)udo</comment>, (I)gnore, (A)bort]', 'S');
                $this->readonly_behavior = strtolower(trim($reply));
            }
            switch ($this->readonly_behavior)
            {
                case 'a':
                    throw new \Exception('Aborted by user command');
                case 'i':
                    $this->io->write('<info>Skipped linking ' . basename($linkname) . ' to ' . dirname($linkname) . '</info>');
                    return;
                case '':
                case 's':
                    exec('sudo ln -s ' . escapeshellarg($target_path) . ' ' . escapeshellarg($linkname), $output, $return);
                    if ($return !== 0)
                    {
                        throw new \Exception('Failed to link ' . basename($linkname) . ' to ' . dirname($linkname));
                    }
                    break;
                default:
                    throw new \Exception('Invalid input');
            }
        }
        else
        {
            if (!@symlink($target_path, $linkname))
            {
                $error = error_get_last();
                throw new \Exception('could not link ' . basename($linkname) . ' to ' . dirname($linkname) . ': ' . $error['message']);
            }
        }
        if ($this->io->isVerbose())
        {
            $this->io->write('Linked <info>' . basename($linkname) . '</info> to <comment>' . dirname($linkname) . '</comment>');
        }
    }

    public function unlink($file)
    {
        $linkname = $this->system_bin . '/' . $file;
        $target_path = $this->vendor_bin . '/' . $file;

        if (is_link($linkname))
        {
            if (   file_exists(realpath($linkname))
                && realpath($linkname) !== $target_path)
            {
                if ($this->io->isVerbose())
                {
                    $this->io->write('Skipping deletion of <info>' . basename($target_path) . '</info>: Found Link in <info>' . dirname($linkname) . '</info> to <comment>' . realpath($linkname) . '</comment>');
                    return;
                }
            }
            $this->io->write('Removing link <info>' . $linkname . '</info>');
            @unlink($linkname);
        }
    }
}
