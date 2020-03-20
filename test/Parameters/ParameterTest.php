<?php

use the16thpythonist\Command\Testing\Testcase\AccessTestCase;

use the16thpythonist\Command\Parameters\Parameter;

use the16thpythonist\Command\Types\StringType;
use the16thpythonist\Command\Types\IntType;


class ParameterTest extends AccessTestCase
{
    public $ACCESS_CLASS = Parameter::class;

    // THE CONSTRUCTION

    /**
     * If a basic construction of an object instance works
     */
    public function testConstruction(): void
    {
        $parameter = new Parameter('param1', 'default', StringType::class, true);
        $this->assertEquals('param1', $parameter->name);
    }

}