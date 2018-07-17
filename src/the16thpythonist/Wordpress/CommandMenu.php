<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 17.07.18
 * Time: 07:57
 */

namespace the16thpythonist\Wordpress;

class CommandMenu
{
    public static function register() {
        $registration = new CommandMenuRegistration();
        $registration->register();
    }
}