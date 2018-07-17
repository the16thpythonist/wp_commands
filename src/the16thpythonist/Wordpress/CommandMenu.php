<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 17.07.18
 * Time: 07:57
 */

namespace the16thpythonist\Wordpress;

/**
 * Class CommandMenu
 *
 *
 *
 * @package the16thpythonist\Wordpress
 */
class CommandMenu
{
    /**
     * Registers the new admin menu with wordpress
     *
     * CHANGELOG
     *
     * Added 17.07.2018
     *
     * @see CommandMenuRegistration
     *
     * @since 0.0.0.0
     */
    public static function register() {
        /*
         * Because the whole registration is a rather big task it has been moved to a separate class.
         */
        $registration = new CommandMenuRegistration();
        $registration->register();
    }
}