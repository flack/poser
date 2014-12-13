<?php
/**
 * @package openpsa.poser
 * @author CONTENT CONTROL http://www.contentcontrol-berlin.de/
 * @copyright CONTENT CONTROL http://www.contentcontrol-berlin.de/
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */

namespace openpsa\poser;

use Composer\Console\Application as base_application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Factory;
use Composer\Util\Filesystem;

class application extends base_application
{
    private $binfiles = null;

    private $share_dir = '/usr/local/share/poser';

    /**
     * @inheritDoc
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        if ($this->getCommandName($input) == 'global')
        {
            throw exception::global_command_unsupported();
        }
        $fs = new Filesystem;
        $fs->ensureDirectoryExists($this->share_dir);
        chdir($this->share_dir);
        $output->writeln('<info>Changed current directory to ' . $this->share_dir . '</info>');
        $vendor_bin = $this->share_dir . '/' . Factory::createConfig()->get('bin-dir');
        $this->binfiles = $this->list_binfiles($vendor_bin);

        $result = parent::doRun($input, $output);

        if (   is_array($this->binfiles)
            && is_dir($vendor_bin))
        {
            $new_files = $this->list_binfiles($vendor_bin);
            $added = array_diff($new_files, $this->binfiles);
            $removed = array_diff($this->binfiles, $new_files);

            $linker = new linker($this->io, $vendor_bin);
            foreach ($added as $file)
            {
                $linker->link($file);
            }
            foreach ($removed as $file)
            {
                $linker->unlink($file);
            }
        }
        return $result;
    }

    private function list_binfiles($vendor_bin)
    {
        $files = array();
        if (!is_dir($vendor_bin))
        {
            return $files;
        }

        $iterator = new \DirectoryIterator($vendor_bin);
        foreach ($iterator as $child)
        {
            if (   $child->getType() !== 'dir'
                && is_executable($child->getRealPath()))
            {
                $files[] = $child->getBasename();
            }
        }
        return $files;
    }
}