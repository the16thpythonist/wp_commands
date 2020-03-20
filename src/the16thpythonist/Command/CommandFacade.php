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

class CommandFacade
{
    public function getCommandClass(string $command_name): string
    {
        return CommandNamePocket::getClass($command_name);
    }

    public function getCommandParameters(string $command_name): array
    {
        $parameter_inspection = $this->getCommandParameterInspection($command_name);
        return $parameter_inspection->allParameterNames();
    }

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

    public function getCommandParameterTypes(string $command_name): array
    {
        $parameter_inspection = $this->getCommandParameterInspection($command_name);
        $names = $parameter_inspection->allParameterNames();
        $types = [];
        foreach ($names as $name) {
            $types[$name] = $parameter_inspection->getType($name, true);
        }
    }

    public function getCommandFromLogName(string $log_name): array
    {
        return str_replace(Command::$LOG_PREFIX, '', $log_name);
    }

    // HELPER FUNCTIONS
    // ****************

    public function getCommandParameterInspection(string $command_name): CommandParameterInspection
    {
        $command_class = $this->getCommandClass($command_name);
        return new CommandParameterInspection($command_class);
    }
}