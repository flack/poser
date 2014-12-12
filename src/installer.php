<?php
/**
 * @package openpsa.globposer
 * @author CONTENT CONTROL http://www.contentcontrol-berlin.de/
 * @copyright CONTENT CONTROL http://www.contentcontrol-berlin.de/
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */

namespace openpsa\globposer;

use Composer\Script\Event;

class installer
{

    /**
     * Links package resources to appropriate places and creates
     * the required directories inside the project directory
     *
     * @param Event $event The event we're called from
     */
    public static function setup(Event $event)
    {
        $linker = new linker($event->getIO());
        $linker->link('globposer');
    }
}