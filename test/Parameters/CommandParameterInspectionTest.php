<?php

use the16thpythonist\Command\Testing\Testcase\CommandTestCase;

use the16thpythonist\Command\Types\CSVType;
use the16thpythonist\Command\Types\IntType;
use the16thpythonist\Command\Types\StringType;

use the16thpythonist\Wordpress\TestCommand;

use the16thpythonist\Command\Parameters\CommandParameterInspection;
use the16thpythonist\Command\Parameters\Parameter;

class CommandParameterInspectionTest extends CommandTestCase
{
    public $ACCESS_CLASS = CommandParameterInspection::class;

    // THE "getParamsArray" METHOD

    /**
     * If "getParamsArray" works correctly
     */
    public function testGetParamsArray(): void
    {
        $params = $this->invokeStaticProtectedMethod('getParamsArray', [TestCommand::class]);

        $expected = [
            'int_arg'           => [
                'optional'      => false,
                'type'          => IntType::class,
                'default'       => null
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
        $this->assertSame($params, $expected);
    }

    // THE CONSTRUCTION

    public function testConstruction(): void
    {
        $parameter_inspection = new CommandParameterInspection(TestCommand::class);
        $this->assertEquals(TestCommand::class, $parameter_inspection->command_class);
    }

    // THE "allParameterNames" METHOD

    public function testAllParameterNames(): void
    {
        $parameter_inspection = new CommandParameterInspection(TestCommand::class);
        $name = $parameter_inspection->allParameterNames();

        $expected = ['int_arg', 'string_arg', 'array_arg'];
        $this->assertEquals($expected, $name);
    }
}