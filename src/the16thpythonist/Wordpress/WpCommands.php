<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 19.11.18
 * Time: 14:26
 */

namespace the16thpythonist\Wordpress;

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
}