<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 19.11.18
 * Time: 14:27
 */

namespace the16thpythonist\Wordpress;

/**
 * Class WpCommandsRegistration
 *
 * The registration object setting up the whole package functionality in wordpress
 *
 * CHANGELOG
 *
 * Added 19.11.2018
 *
 * @package the16thpythonist\Wordpress
 */
class WpCommandsRegistration
{
    /**
     * WpCommandsRegistration constructor.
     *
     * CHANGELOG
     *
     * Added 19.11.2018
     */
    public function __construct()
    {

    }

    /**
     *
     * CHANGELOG
     *
     * Added 19.11.2018
     */
    public function register() {
        // Registering the Command Menu
        CommandMenu::register();
    }


}