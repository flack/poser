<?php
/**
 * @package openpsa.poser
 * @author CONTENT CONTROL http://www.contentcontrol-berlin.de/
 * @copyright CONTENT CONTROL http://www.contentcontrol-berlin.de/
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */

namespace openpsa\poser;

class exception extends \Exception
{
    public static function global_command_unsupported()
    {
        return new static("You are trying to use poser with Composer's home directory. Why would you do that?");
    }

    public static function nonexistent_target($target_path)
    {
        return new static('Cannot link to nonexistent path ' . $target_path);
    }

    public static function php_error($linkname)
    {
        $error = error_get_last();
        return new static('could not link ' . basename($linkname) . ' to ' . dirname($linkname) . ': ' . $error['message']);
    }

    public static function shell_error($output)
    {
        return new static('Failed to link ' . basename($linkname) . ' to ' . dirname($linkname) . ":\n" . implode("\n", $output));
    }
}
