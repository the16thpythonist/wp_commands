<?php


namespace the16thpythonist\Command\Parameters;

use the16thpythonist\Command\Parameters\Parameter;
use the16thpythonist\Command\Parameters\ParameterConverter;
use ReflectionClass;

use function foo\func;

/*
 * public interface of CommandParameterInspection:
 *
 *      __construct($command_class)
 *      allParameterNames()
 *      isOptional($parameter_name)
 *      getDefaultValue($parameter_name, $as_string)
 *      getTypeClass($parameter_name, $as_string)
 */

class CommandParameterInspection
{
    public $command_class;
    protected $parameter_converter;
    protected $params;
    protected $parameters;

    public function __construct(string $command_class)
    {
        $this->command_class = $command_class;
        $this->params = self::getParamsArray($command_class);
        $this->parameter_converter = new ParameterConverter($this->params);
        $this->parameters = $this->parameter_converter->getParametersAssociative();
    }

    public function allParameterNames(): array
    {
       return array_keys($this->parameters);
    }

    public function isOptional(string $parameter_name): bool
    {
        return $this->parameters[$parameter_name]->optional;
    }

    public function getDefault(string $parameter_name, bool $as_string=false)
    {
        if ($as_string) {
            $type = $this->parameters[$parameter_name]->type;
            return call_user_func([$type, 'unapply'], [$this->parameters[$parameter_name]->default]);
        } else {
            return $this->parameters[$parameter_name]->default;
        }
    }

    public function getType(string $parameter_name, bool $as_string=false): string
    {
        if ($as_string) {
            $type = $this->parameters[$parameter_name]->type;
            return call_user_func([$type, 'getName'], []);
        } else {
            return $this->parameters[$parameter_name]->type;
        }
    }

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