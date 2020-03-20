<?php

use the16thpythonist\Command\Testing\Testcase\AccessTestCase;

use the16thpythonist\Command\Types\StringType;
use the16thpythonist\Command\Types\IntType;
use the16thpythonist\Command\Types\CSVType;

use the16thpythonist\Command\Parameters\ParameterConverter;
use the16thpythonist\Command\Parameters\Parameter;

class ParameterConverterTest extends AccessTestCase
{
    public $ACCESS_CLASS = ParameterConverter::class;

    // THE "getFormat" METHOD

    /**
     * If "getFormat" works with the basic format
     */
    public function testGetFormatBasicFormat(): void
    {
        $params_basic = ['param1' => 'default'];

        $parameter_converter = new ParameterConverter([]);
        $format = $this->invokeProtectedMethod(
            $parameter_converter,
            'getFormat',
            ['param1', $params_basic['param1']]
        );

        $this->assertEquals(ParameterConverter::FORMAT_BASIC, $format);
    }

    /**
     * If "getFormat" works with the extended format
     */
    public function testGetFormatExtendedFormat(): void
    {
        $params_extended = [
            'param1'        => [
                'optional'  => true,
                'type'      => StringType::class,
                'default'   => 'default'
            ]
        ];

        $parameter_converter = new ParameterConverter([]);
        $format = $this->invokeProtectedMethod(
            $parameter_converter,
            'getFormat',
            ['param1', $params_extended['param1']]
        );

        $this->assertEquals(ParameterConverter::FORMAT_EXTENDED, $format);
    }

    /**
     * If "getFormat" throws an error for invalid params value
     */
    public function testGetFormatInvalidParamsValue(): void
    {
        $params = ['param1' => 1];

        $parameter_converter = new ParameterConverter([]);
        $this->expectException(TypeError::class);
        $this->invokeProtectedMethod(
            $parameter_converter,
            'getFormat',
            ['param1', $params['param1']]
        );
    }

    // THE "getParametersAssociative" METHOD

    /**
     * If "getParametersAssociative" works as expected
     */
    public function testGetParametersAssociative(): void
    {
        $params = [
            'param1'        => [
                'optional'  => true,
                'type'      => StringType::class,
                'default'   => 'default'
            ],
            'param2'        => [
                'optional'  => true,
                'type'      => CSVType::class,
                'default'   => []
            ],
            'param3'        => [
                'optional'  => false,
                'type'      => IntType::class,
                'default'   => 0
            ]
        ];

        $parameter_converter = new ParameterConverter($params);
        $parameters = $parameter_converter->getParametersAssociative();

        $expected = [
            'param1' => new Parameter('param1', 'default', StringType::class, true),
            'param2' => new Parameter('param2', [], CSVType::class, true ),
            'param3' => new Parameter('param3', 0, IntType::class, false)
        ];
        $this->assertEquals($expected, $parameters);
    }

    // THE "getParameters" METHOD

    /**
     * If "getParameters" works as expected.
     */
    public function testGetParameters(): void
    {
        $params = [
            'param1'        => [
                'optional'  => true,
                'type'      => StringType::class,
                'default'   => 'default'
            ],
            'param2'        => [
                'optional'  => true,
                'type'      => CSVType::class,
                'default'   => []
            ],
            'param3'        => [
                'optional'  => false,
                'type'      => IntType::class,
                'default'   => 0
            ]
        ];

        $parameter_converter = new ParameterConverter($params);
        $parameters = $parameter_converter->getParameters();

        $expected = [
            new Parameter('param1', 'default', StringType::class, true),
            new Parameter('param2', [], CSVType::class, true ),
            new Parameter('param3', 0, IntType::class, false)
        ];
        $this->assertEquals($expected, $parameters);
    }
}