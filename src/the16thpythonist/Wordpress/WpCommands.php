<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 19.11.18
 * Time: 14:26
 */

namespace the16thpythonist\Wordpress;

use Log\LogPost;
use the16thpythonist\Command\Command;

/**
 * Class WpCommands
 *
 * The facade for the whole package
 *
 * @package the16thpythonist\Wordpress
 */
class WpCommands
{
    public static $REGISTRATION;

    /**
     * CHANGELOG
     *
     * Added 19.11.2018
     */
    public static function register() {
        // Using the registration object to register all functionality in wordpress
        $registration = new WpCommandsRegistration();
        $registration->register();

        // Saving the registration object for possible later use
        self::$REGISTRATION = $registration;
    }

    /**
     * Returns an array of LogPost object, where each log was the output of a previously executed Command.
     * The Logs in the array will be sorted by date and in a descending order, which means, that those Commands
     * issued most recently will be the first items in the list.
     *
     * CHANGELOG
     *
     * Added 04.12.2018
     *
     * Changed 17.03.2019
     * Moved the actual implementation to a static method of the WpCommandsRegistration class because that class also
     * needs the method and we need to avoid a circular dependency
     *
     * @param int $count    The max(!) amount of LogPost objects to be in the returned array
     * @return array
     */
    public static function getCommandLogs(int $count=-1) {
        return WpCommandsRegistration::getCommandLogs($count);
    }
}