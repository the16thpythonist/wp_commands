<?php


namespace the16thpythonist\Command;

use the16thpythonist\Command\CommandNamePocket;

use the16thpythonist\Command\Parameters\CommandParameterInspection;

/*
 * public interface of CommandFacade
 *
 *      registerCommand(string $command_class, string $command_name): void
 *      unregisterCommand(string $command_class): void
 *      isClassRegistered(string $command_class):
 *      isCommandRegistered($command_name)
 *      allRegisteredCommands()
 *
 *      getCommandClass(string $command_name): string
 *      getCommandParameters(string $command_name): array
 *      getCommandParameterDefaultValues(string $command_name): array
 *      getCommandParameterTypes(string $command_name): array
 */

/**
 * Class CommandFacade
 *
 * This class provides a facade for interacting with the business logic side of the Command system
 *
 * BACKGROUND
 *
 * The "wp-commands" package roughly consists of two overall parts: The first part is the actual business logic. The
 * classes which include the abstract "Command" class, which has to be sub classed to create a new executable command.
 * The other part handles most of the wordpress specific operations, such as registering all the components on the right
 * wordpress hooks. It also contains the HTML, CSS and JS code to be used on the actual front end web page.
 *
 * THE PROBLEM
 *
 * Aside from some minor things the business logic doesnt have to know anything about how the wordpress specifics are
 * registered and so on, but the wordpress part needs to know about the business logic to integrate its functionality
 * into the front end user interfaces. So far so good with the rather loose coupling.
 * But there still is a problem, when the wordpress part needs to make to many assumptions about the internal structure
 * of the business logic. If this front end part uses all the different classes in different locations of the code,
 * then the overall package becomes very vulnerable to changes or extensions of the business logic. Changing one class
 * or method would also have to be changed in various locations of the wordpress code, thus creating tight coupling
 * again.
 *
 * That is where this class comes in. The facade will be the only class providing access to the business logic for the
 * wordpress part of the package. All access to any business functionality will be done through this single interface.
 * This obviously means that the facade still has to make various assumptions about the internal business logic. But
 * when there are changes in the business logic, the implications of those changes only have to be considered within
 * the methods of the facade, leaving the wordpress code untouched.
 *
 * CHANGELOG
 *
 * Added 19.03.2020
 *
 * @package the16thpythonist\Command
 */
class CommandFacade
{
    /**
     * Returns the string class name of a command sub class given the command name under which it was registered.
     *
     * EXAMPLE
     *
     * ```php
     * TestCommand::register('test_command');
     *
     * // Later
     * $command_facade = new CommandFacade();
     * $command_facade->getCommandClass('test_command') // TestCommand::class
     * ```
     *
     * CHANGELOG
     *
     * Added 19.03.2020
     *
     * @param string $command_name
     * @return string
     */
    public function getCommandClass(string $command_name): string
    {
        return CommandNamePocket::getClass($command_name);
    }

    /**
     * Returns a list of all the parameter names for a registered command name
     *
     * CHANGELOG
     *
     * Added 19.03.2020
     *
     * @param string $command_name
     * @return array
     * @throws \ReflectionException
     */
    public function getCommandParameters(string $command_name): array
    {
        $parameter_inspection = $this->getCommandParameterInspection($command_name);
        return $parameter_inspection->allParameterNames();
    }

    /**
     * Returns an assoc array with parameter name keys and string default values for a registered command name
     *
     * CHANGELOG
     *
     * Added 19.03.2020
     *
     * @param string $command_name
     * @return array
     * @throws \ReflectionException
     */
    public function getCommandParameterDefaultValues(string $command_name): array
    {
        $parameter_inspection = $this->getCommandParameterInspection($command_name);
        $names = $parameter_inspection->allParameterNames();
        $default_values = [];
        foreach ($names as $name) {
            $default_values[$name] = $parameter_inspection->getDefault($name, true);
        }
        return $default_values;
    }

    /**
     * Returns an assoc array with parameter name keys and string type names values for a registered command name.
     *
     * CHANGELOG
     *
     * Added 19.03.2020
     *
     * Changed 21.04.2020
     * This method didnt have a return method; Added it.
     *
     * @param string $command_name
     * @return array
     * @throws \ReflectionException
     */
    public function getCommandParameterTypes(string $command_name): array
    {
        $parameter_inspection = $this->getCommandParameterInspection($command_name);
        $names = $parameter_inspection->allParameterNames();
        $types = [];
        foreach ($names as $name) {
            $types[$name] = $parameter_inspection->getType($name, true);
        }
        return $types;
    }

    /**
     * Returns an assoc array with the parameter names as keys and the boolean value of whether or not is optional as v.
     *
     * CHANGELOG
     *
     * Added 21.04.2020
     *
     * @param string $command_name
     * @return array
     * @throws \ReflectionException
     */
    public function getCommandParameterOptionality(string $command_name): array
    {
        $parameter_inspection = $this->getCommandParameterInspection($command_name);
        $names = $parameter_inspection->allParameterNames();
        $optionality = [];
        foreach ($names as $name) {
            $optionality[$name] = $parameter_inspection->isOptional($name);
        }
        return $optionality;
    }

    /**
     * Returns assoc array with parameter names as keys and assoc info array as values.
     *
     * The associative arrays, which are the values for all the parameter names contain the following keys:
     * - name: The string name of the parameter
     * - type: The string name of the type, which the parameter expects
     * - default: The string default value for the parameter
     * - optional: The boolean value of whether or not this parameter is optional
     *
     * DEV. INFO
     *
     * This method was created, because I realized, that it is quite bad design to only provide the individual
     * information about type, default value and optionality in separate associative arrays. For this a new
     * Inspection object would have to be created three times as well as three loops etc. So this method now provides
     * all the information without having to expose the Inspection object to the outside scope.
     * My initial concerns with providing all data at once was, that I would either do it with the inspection object,
     * which would introduce complexity and which is subject to future changes, or I would have to change this method
     * too often if i just names it "...ParameterInfo". But now I named it with an additonal "extended" and for the sake
     * of backwards compatibility I will leave this method as it is. If at any point additional information is being
     * added to parameter inspection, I will add another method and call it "AdvancedInfo" or smth...
     *
     * CHANGELOG
     *
     * Added 21.04.2020
     *
     * Changed 22.04.2020
     * Added the additional key "name" to the returned assoc array.
     *
     * @param string $command_name
     * @return array
     * @throws \ReflectionException
     */
    public function getCommandParameterExtendedInfo(string $command_name): array
    {
        $parameter_inspection = $this->getCommandParameterInspection($command_name);
        $names = $parameter_inspection->allParameterNames();
        $info_map = [];
        foreach ($names as $name) {
            $_info = [
                'name'          => $name,
                'type'          => $parameter_inspection->getType($name, true),
                'default'       => $parameter_inspection->getDefault($name, true),
                'optional'      => $parameter_inspection->isOptional($name)
            ];

            $info_map[$name] = $_info;
        }
        return $info_map;
    }

    /**
     * Returns the name of a command based on the name of its log file
     *
     * CHANGELOG
     *
     * Added 19.03.2020
     *
     * @param string $log_name
     * @return array
     */
    public function getCommandFromLogName(string $log_name): string
    {
        return str_replace(Command::$LOG_PREFIX, '', $log_name);
    }

    /**
     * Returns the string, which is used as the prefix for command log files
     *
     * CHANGELOG
     *
     * Added 20.03.2020
     *
     * @return string
     */
    public function getCommandLogPrefix(): string
    {
        return Command::$LOG_PREFIX;
    }

    /**
     *
     * CHANGELOG
     *
     * Added 23.03.2020
     *
     * @return array
     */
    public function registeredCommands(): array
    {
        return array_values(CommandNamePocket::$names);
    }

    // HELPER FUNCTIONS
    // ****************

    /**
     * Returns the CommandParameterInspection object for the command identified by its registered name.
     *
     * CHANGELOG
     *
     * Added 19.03.2020
     *
     * @param string $command_name
     * @return CommandParameterInspection
     * @throws \ReflectionException
     */
    protected function getCommandParameterInspection(string $command_name): CommandParameterInspection
    {
        $command_class = $this->getCommandClass($command_name);
        return new CommandParameterInspection($command_class);
    }
}