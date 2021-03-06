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
 * This class is used as part of the mechanism, which enabled the registration of the background commands.
 * For further information of the actual background commands see the "Command" class
 *
 * PROBLEM
 * Each new Command is being made available via AJAX by calling a static method "register", which takes the name of
 * the command to be displayed to the user as a parameter. This name is supposed to be stored as an attribute of that
 * specific class from that point on so it can be used as the name for the corresponding log file for example. But the
 * way static fields work makes it impossible to save the value to a child class of Command only from within a static
 * method.
 *
 * ```php
 * // NewCommand inherits from "Command" base class.
 * // This is how the command is supposed to be made available to the wordpress system. By calling a static(!) method.
 * NewCommand::register('new_command')
 * // The problem now is, that this static method would have to internally set a static attribute "name" of the class
 * // to save the name string given to it... But this doesnt work. Since new command is a child class setting the name
 * // attribute would set a new value for the "Command" base class and so each successive call to register in the
 * // different child classes would just override the value within the parent class.
 * ```
 *
 * Thus a different solution for somehow saving the names of the new commands has to be found...
 * That is where this static class comes in. instead of saving the name in the class itself it will just be stored
 * EXTERNALLY. Within this class to be percise. This is a static class, which will act as a sort of associative array,
 * where the command classes can "rent" a save place for their names and then always access them here, whenever they
 * need it.
 *
 * This class allows other classes to store a string value to a key, which is the class name of the class, which wants
 * to store the key...
 * Since that sounds very complicated here is an example.
 *
 * ```php
 * // This class is "John". The class wants to store a string for later use, but it does not want to store it within
 * // as one of its own attributes, instead it wants it to be stored externally
 * class John {
 *      private static function storeString() {
 *          // "static::class" will return the string name of the class "John". From now on the string will be saved
 *          // in the pocket, and the owner will identify it by its class name!
 *          // It is important to note, that static::class will return the name of the CHILD CLASS. This is an important
 *          // fact to circumvent the problem stated above.
 *          // "put" is the method, which registers the key value pair in the associative array.
 *          CommandNamePocket::put("The string I want to store", static::class);
 *
 *          // From now on the string is saved within the static class "CommandNamePocket" and it can be accessed at a
 *          // later time.
 *          // the "pick" method accesses the value from the associative array.
 *          $my_string = CommandNamePocket::pick(static::class);
 *      }
 * }
 * ```
 *
 * The implementation of this class is very straight forward. it is literally just an associative array in a static
 * field.
 *
 * CHANGELOG
 *
 * Added 09.08.2018
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
     * CHANGELOG
     *
     * Added ?
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
     * CHANGELOG
     *
     * Added ?
     *
     * @param string $class the name of the class for which the command name is to be retrieved
     * @return string
     */
    public static function pick(string $class): string {
        return self::$names[$class];
    }

    /**
     * Removes a command from the pocket given its class name.
     *
     * CHANGELOG
     *
     * Added 17.03.2020
     *
     * @param string $class
     */
    public static function withdraw(string $class): void
    {
        unset(self::$names[$class]);
    }

    /**
     * Returns the command class name given its corresponding string command name
     *
     * DESIGN CHOICE
     *
     * Adding the functionality to make a "reverse lookup" using the command name as the key to retrieve the class name
     * came only later. So this function has the worst imaginable implementation: It just searches the array.
     * This could be done much better by adding an additional assoc array as a property, which just stores the key
     * value pairs in reverse, this way even the reverse lookup would be O(1) instead of O(n).
     * But for now this impl. should be OK as long as it doesnt affect the performance visibly.
     *
     * CHANGELOG
     *
     * Added 17.03.2020
     *
     * @param string $name
     * @return string
     */
    public static function getClass(string $name): string {
        foreach (self::$names as $class => $value) {
            if ($value == $name) {
                return $class;
            }
        }
        $message = sprintf("corresponding class for the command name '%s' cannot be found", $name);
        throw new \Error();
    }

    /**
     * Returns whether or not the assoc array contains the given value (command name)
     *
     * CHANGELOG
     *
     * Added 16.03.2020
     *
     * @param string $name
     * @return bool
     */
    public static function contains(string $name): bool
    {
        return in_array($name, self::$names);
    }

    /**
     * Returns whether or not the assoc array contains the given key (class name)
     *
     * CHANGELOG
     *
     * Added 16.03.2020
     *
     * @param string $class
     * @return bool
     */
    public static function hasKey(string $class): bool
    {
        return array_key_exists($class, self::$names);
    }

}