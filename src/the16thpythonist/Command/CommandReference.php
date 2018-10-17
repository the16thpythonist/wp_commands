<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 17.07.18
 * Time: 08:15
 */

namespace the16thpythonist\Command;


/**
 * Class CommandReference
 *
 * The command functionality is supposed to work like this: In the admin panel there is a special menu for these kinds
 * of background commands. In this menu there will be some sort of a form and that form is supposed to contain a
 * selection menu with all the available commands, that can be called. Creating this selection however requires to know
 * which commands exactly are available at that moment. This is what this class is for. It acts as a static container,
 * to which all the commands add their name during their registration with wordpress, to signal that they are
 * available for calling. When building the menu page with the selection the internal array of this class will contain
 * all the command names currently registered and ready to use.
 *
 * CHANGELOG
 *
 * Added 17.07.2018
 *
 * @since 0.0.0.0
 *
 * @package the16thpythonist\Command
 */
class CommandReference
{
    public static $COMMANDS;

    /**
     * Adds a new command name to the list of registered commands
     *
     * The strings actually added by this method are not supposed to be the method names per se, but the exact string
     * names of the 'action' property required to call the specific AJAX method of wordpress
     *
     * CHANGELOG
     *
     * Added 17.07.2018
     *
     * @since 0.0.0.0
     *
     * @param string $name the command name to be added to the list
     */
    public static function addCommand(string $name) {
        static::$COMMANDS[] = $name;
    }

    /**
     * Returns the internal static array of all the command names that have been registered up to this point
     *
     * CHANGELOG
     *
     * Added 17.07.2018
     *
     * @since 0.0.0.0
     *
     * @return array contains all the string ajax action names for the commands, that have been registered.
     */
    public static function getCommands(): array {
        return static::$COMMANDS;
    }

}