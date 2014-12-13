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
}
