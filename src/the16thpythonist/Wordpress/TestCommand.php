<?php


namespace the16thpythonist\Wordpress;

use Log\VoidLog;

use the16thpythonist\Command\Command;

use the16thpythonist\Command\Types\IntType;
use the16thpythonist\Command\Types\StringType;
use the16thpythonist\Command\Types\CSVType;

/**
 * Class TestCommand
 *
 * This class extends the abstract base class Command.
 *
 * @package the16thpythonist\Wordpress
 */
class TestCommand extends Command
{
    public $params = [
        'int_arg'           => [
            'optional'      => false,
            'type'          => IntType::class,
            'default'       => 100
        ],
        'string_arg'        => [
            'optional'      => true,
            'type'          => StringType::class,
            'default'       => 'Hello World!'
        ],
        'array_arg'         => [
            'optional'      => false,
            'type'          => CSVType::class,
            'default'       => []
        ]
    ];

    public static $LOG_CLASS = VoidLog::class;

    public function __construct()
    {
        // "VoidLog" is a type of logging class, which implements the logging interface, but which acts as more of a
        // stub for testing. All messages logged to a VoidLog will just be discarded and not saved anywhere.
        parent::__construct(static::$LOG_CLASS);
    }

    protected function run(array $args){
        foreach ($args as $name => $value) {
            $message = sprintf(
                "The argument '%s' is of the type '%s' and has the value '%s'",
                $name,
                $this->params[$name]['type'],
                var_export($value, true)
            );
            $this->log->info($message);
        }
    }
}