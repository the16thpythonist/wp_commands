<?php


namespace the16thpythonist\Command\Parameters;

use ReflectionClass;

/*
 * public interface of CommandParameterInspection:
 *
 *      __construct($command_class)
 *      allParameterNames()
 *      isOptional($parameter_name)
 *      getDefaultValue($parameter_name, $as_string)
 *      getTypeClass($parameter_name, $as_string)
 */

/**
 * Class CommandParameterInspection
 *
 * This class provides information about the parameter specification of a command class.
 *
 * BACKGROUND
 *
 * A new command can be created by subclassing the abstract "Command" base class and implementing a custom "run" method.
 * But since most commands need some sort of input parameters to provide useful functionality, there needs to be a
 * mechanism to define which parameters a command expects, so that these can be passed in for the execution.
 * This way of specifying parameters is done by creating a default property "params" on this new child class. This
 * array consists of key value pairs, where the key is the string name of the parameter and the value being the
 * settings for it.
 *
 * THE PROBLEM
 *
 * The whole parameter situation is a little bit tricky on the programming side. Now most of the functionality for a
 * Command is actually implemented within the "Command" abstract base class itself. This is to spare the user from
 * unnecessary boilerplate code. The only things the user has to do for creating a new command is to make a sub class
 * of Command, implement the abstract method "run" and define the default value for the object property "params".
 * Now the whole "params" thing is something that cannot be implemented within the command base class. The abstract
 * base class has no direct way of accessing the default value of a object property of its child classes. And besides
 * that it is also none of its concerns. To access this property by just the class name, live object inspection
 * functionality has to be used.
 *
 * And this is exactly what this class does. This class accepts the class name of a Command sub class, then uses
 * code inspection to get the value for the params array and then wraps additional methods, which allow to get
 * information about the various parameters of this command...
 *
 * EXAMPLE
 *
 * So consider the following example where the Command sub class TestCommand is known and the application needs to
 * know the full list of its parameters without creating an instance of this command.
 *
 * ```php
 * $parameter_inspection = new CommandParameterInspection(TestCommand::class);
 * $parameters = $parameter_inspection->allParameterNames();
 * ```
 *
 * CHANGELOG
 *
 * Added 19.03.2020
 *
 * @package the16thpythonist\Command\Parameters
 */
class CommandParameterInspection
{
    public $command_class;
    protected $parameter_converter;
    protected $params;
    protected $parameters;

    /**
     * CommandParameterInspection constructor.
     *
     * CHANGELOG
     *
     * Added 19.03.2020
     *
     * @param string $command_class
     * @throws \ReflectionException
     */
    public function __construct(string $command_class)
    {
        $this->command_class = $command_class;
        $this->params = self::getParamsArray($command_class);
        $this->parameter_converter = new ParameterConverter($this->params);
        $this->parameters = $this->parameter_converter->getParametersAssociative();
    }

    /**
     * Returns a list with all the string parameter names for the subject command class.
     *
     * CHANGELOG
     *
     * Added 19.03.2020
     *
     * @return array
     */
    public function allParameterNames(): array
    {
       return array_keys($this->parameters);
    }

    /**
     * Returns whether or not the parameter is optional for a given parameter name
     *
     * CHANGELOG
     *
     * Added 19.03.2020
     *
     * @param string $parameter_name
     * @return bool
     */
    public function isOptional(string $parameter_name): bool
    {
        return $this->parameters[$parameter_name]->optional;
    }

    /**
     * Returns the default value for a given parameter name
     *
     * In case the additional flag "as_string" is false this method will return the actual default value, whatever
     * type this may be (could be anything from string to int or array...). If it is true, this default value will be
     * converted into an appropriate string first (by using the ParameterType "unapply" method).
     *
     * CHANGELOG
     *
     * Added 19.03.2020
     *
     * @param string $parameter_name
     * @param bool $as_string
     * @return mixed
     */
    public function getDefault(string $parameter_name, bool $as_string=false)
    {
        if ($as_string) {
            $type = $this->parameters[$parameter_name]->type;
            return call_user_func_array([$type, 'unapply'], [$this->parameters[$parameter_name]->default]);
        } else {
            return $this->parameters[$parameter_name]->default;
        }
    }

    /**
     * Returns the type for a given parameter name
     *
     * In case the additional flag "as_string" is false this method will return the string class name of the
     * ParameterType object, which describes the type. If the flag is true a human readable string name of the type
     * will be returned.
     *
     * CHANGELOG
     *
     * Added 19.03.2020
     *
     * @param string $parameter_name
     * @param bool $as_string
     * @return string
     */
    public function getType(string $parameter_name, bool $as_string=false): string
    {
        if ($as_string) {
            $type = $this->parameters[$parameter_name]->type;
            return call_user_func([$type, 'getName'], []);
        } else {
            return $this->parameters[$parameter_name]->type;
        }
    }

    // ToDO: Validate, that the parameter actually exists in the params array

    // HELPER METHODS
    // **************

    /**
     * Returns the "params" array of the given command class
     *
     * The "params" array is an object property, which has to be defined by all the subclasses of "Command" to specify
     * which parameters the commands expect for their execution.
     *
     * EXAMPLE
     *
     * ```php
     * class TestCommand extends Command {
     *      public $params = ["param1" => "default"];
     *      protected function run($args) { return; }
     * }
     *
     * CommandParameterInspection::getParamsArray(TestCommand::class); // ["param1" => "default"]
     * ```
     *
     * CHANGELOG
     *
     * Added 19.03.2020
     *
     * @param string $command_class
     * @throws \ReflectionException
     * @return array
     */
    protected static function getParamsArray(string $command_class): array
    {
        $reflection_class = new ReflectionClass($command_class);
        $default_properties = $reflection_class->getDefaultProperties();
        return $default_properties['params'];
    }
}