<?php
/**
 * @package openpsa.globposer
 * @author CONTENT CONTROL http://www.contentcontrol-berlin.de/
 * @copyright CONTENT CONTROL http://www.contentcontrol-berlin.de/
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */

namespace openpsa\globposer;

use Composer\Console\Application as base_application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Factory;
use Symfony\Component\Console\Input\StringInput;

class application extends base_application
{
    private $readonly_behavior;

    private $binfiles = null;

    /**
     * @inheritDoc
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        if (   $this->getCommandName($input) != 'global'
            && $this->binfiles === null) // This is to prevent infinite recursion
        {
            $input = new StringInput('global ' . $input);
            $this->binfiles = $this->list_binfiles();
        }

        $result = parent::doRun($input, $output);

        if (is_array($this->binfiles))
        {
            $new_files = $this->list_binfiles();
            $added = array_diff($new_files, $this->binfiles);
            $removed = array_diff($this->binfiles, $new_files);

            $linker = new linker($this->io, Factory::createConfig()->get('home') . '/vendor/bin');
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

    private function list_binfiles()
    {
        $files = array();
        $composer_home = Factory::createConfig()->get('home') . '/vendor/bin';
        if (!is_dir($composer_home))
        {
            return $files;
        }

        $iterator = new \DirectoryIterator($composer_home);
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