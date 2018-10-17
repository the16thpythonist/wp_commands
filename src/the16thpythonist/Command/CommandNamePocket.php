<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 09.08.18
 * Time: 10:39
 */

namespace the16thpythonist\Command;

/**
 * Class CommandNamePocket
 * @package the16thpythonist\Command
 *
 * CHANGELOG
 *
 * Added 09.08.2018
 *
 * This Problem:
 * Each new Command is being made available via AJAX by calling a static method "register", which takes the name of
 * the command to be displayed to the user as a parameter. This name is supposed to be stored as an attribute of that
 * specific class from that point on so it can be used as the name for the corresponding log file for example. But the
 * way static fields work makes it impossible to save the value to a child class of Command only from within a static
 * method.
 *
 * That is where this static class comes in. instead of saving the name in the class itself it will just be stored
 * in a "pocket" instead and be associated with the child class name during registration, so whenever the command wants
 * to know its assigned name it will access the pocket with its class name and get the correct value.
 *
 * @since 0.0.0.1
 */
class CommandNamePocket
{
    /**
     * @var array Associative array with class names as keys and strings as values
     */
    public static $names = array();

    /**
     * Stores the name of a command in the pocket associated with the class name
     *
     * @param string $name  the string name of the command to be saved
     * @param string $class the sub class name of the Command child, which wants to save their name
     */
    public static function put(string $name, string $class) {
        self::$names[$class] = $name;
    }

    /**
     * Retrieves the name of a command given the class name as a key
     *
     * @param string $class the name of the class for which the command name is to be retrieved
     * @return string
     */
    public static function pick(string $class): string {
        return self::$names[$class];
    }

}