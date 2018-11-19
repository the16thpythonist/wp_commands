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
     * Changed 15.08.2018
     * Added an additional Registration object "CommandDashboardRegistration", which creates the dashboard widget,
     * that display the logs posts for the most recently executed commands.
     *
     * Changed 19.11.2018
     * Removed the registration for the separate admin menu page.
     * Only doing things via the dashboard widget for now.
     *
     * @see CommandMenuRegistration
     *
     * @since 0.0.0.0
     */
    public static function register() {

        $dashboard_registration = new CommandDashboardRegistration();
        $dashboard_registration->register();
    }
}