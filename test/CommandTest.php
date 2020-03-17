<?php

use PHPUnit\Framework\TestCase;

use the16thpythonist\Command\Command;
use the16thpythonist\Command\CommandNamePocket;
use the16thpythonist\Command\CommandReference;

use the16thpythonist\Command\Types\StringType;
use the16thpythonist\Command\Types\IntType;
use the16thpythonist\Command\Types\CSVType;

use the16thpythonist\Wordpress\TestCommand;


class CommandTest extends TestCase
{
    public static $TEST_COMMAND_NAME = "test_command";

    // TESTING THE REGISTRATION PROCESS
    // ********************************

    /**
     * Tests the protected static function "registerCommand" of the TestCommand class
     */
    public function testRegisterCommand(): void
    {
        // So usually the "register" command would be used for the registration of a command. But this method would
        // also perform the registration within wordpress, using wordpress specific methods. Since this is standard
        // PHPUnit with wordpress not loaded, we cannot do that.

        // The register method itself only invokes two sub methods, namely "registerCommand" and "registerWordpress".
        // Since the wordpress registration is not necessary for testing the basic command functionality, we will only
        // invoke "registerCommand" here (it only uses standard PHP functions).

        // The problem now is that "registerCommand" is a protected function and due to the nature of protected
        // functions it cannot be simply invoked from this scope. We use a workaround with "ReflectionClass" to make a
        // temporarily public version of this method. This is what the code below is actually about.
        // "getProtectedMethod" will return an object of type "ReflectionMethod", which can be invoked with an array of
        // args. As the argument to the registration we supply the command name.
        $registerCommand = static::getProtectedMethod('registerCommand');
        $registerCommand->invokeArgs(null, [static::$TEST_COMMAND_NAME]);

        // The Command registration is supposed to add the command to the static associative array CommandNamePocket
        // and the static list CommandReference. So we will be checking if that has properly happened.
        $this->assertTrue(CommandNamePocket::contains(static::$TEST_COMMAND_NAME));
    }

    /**
     * If "unregisterCommand" method unregisters from the CommandNamePocket
     */
    public function testUnregisterCommand(): void
    {
        $unregisterCommand = static::getProtectedMethod('unregisterCommand');
        $unregisterCommand->invokeArgs(null, []);

        // The Command registration is supposed to add the command to the static associative array CommandNamePocket
        // and the static list CommandReference. So we will be checking if that has properly been removed again!
        $this->assertFalse(CommandNamePocket::contains(static::$TEST_COMMAND_NAME));
    }

    // TESTING INDIVIDUAL FUNCTIONS
    // ****************************

    /**
     * basic usage of the "lazyInstance" command
     */
    public function testLazyInstance(): void
    {
        $test_command = TestCommand::lazyInstance();

        $this->assertInstanceOf(TestCommand::class, $test_command);
    }

    /**
     * If "getName" returns the name correctly
     */
    public function testGetName(): void
    {
        $name = TestCommand::getName();

        $this->assertEquals(self::$TEST_COMMAND_NAME, $name);
    }

    // INFO
    // The "params" array is defined by a subclass of the Command base class to provide a list of parameters/arguments
    // which the command expects, when it is being invoked.

    /**
     * If the "isParamsExtendedFormat" method can detect a simple extended format properly.
     */
    public function testIsParamsExtendedFormat(): void
    {
        // The Extended format for defining the params array refers to having the argument names be the keys of the
        // array and the values being associative arrays, which contain multiple settings such as the default value
        // and the expected type.
        $params = [
            'param1'        => [
                'optional'  => false,
                'type'      => StringType::class,
                'default'   => ''
            ],
            'param2'        => [
                'optional'  => true,
                'type'      => StringType::class,
                'default'   => ''
            ]
        ];

        $isParamsExtendedFormat = $this->getProtectedMethod('isParamsExtendedFormat');
        $is_extended = $isParamsExtendedFormat->invokeArgs(null, [$params]);

        $this->assertTrue($is_extended);
    }

    /**
     * If the "isParamsExtendedFormat" method can properly return false for a basic format params array.
     */
    public function testIsParamsExtendedFormatWithBasicFormat(): void
    {
        // The basic format for defining the params array refers to having the argument names be the keys of the array
        // and the values being the string default values in case these arguments where not provided.
        $params = [
            'param1'        => 'null',
            'param2'        => 'test'
        ];

        $isParamsExtendedFormat = $this->getProtectedMethod('isParamsExtendedFormat');
        $is_extended = $isParamsExtendedFormat->invokeArgs(null, [$params]);

        $this->assertFalse($is_extended);
    }

    /**
     * If the "isParamsExtendedFormat" method throws parse error, when a mixing of the formats is attempted.
     */
    public function testIsParamsExtendedFormatWithMixedFormat(): void
    {
        $params = [
            'param1'        => 'null',
            'param2'        => [
                'optional'  => false,
                'type'      => StringType::class,
                'default'   => ''
            ]
        ];

        $isParamsExtendedFormat = $this->getProtectedMethod('isParamsExtendedFormat');

        $this->expectException(ParseError::class);
        $isParamsExtendedFormat->invokeArgs(null, [$params]);
    }

    /**
     * If the "isParamsExtendedFormat" method throws parse error, when an invalid format is provided
     */
    public function testIsParamsExtendedFormatWithInvalidFormat(): void
    {
        // In this case it would be invalid to use any other data type but int or an array as values of the params array
        $params = [
            'param1'        => 'null',
            'param2'        => 10
        ];

        $isParamsExtendedFormat = $this->getProtectedMethod('isParamsExtendedFormat');

        $this->expectException(ParseError::class);
        $isParamsExtendedFormat->invokeArgs(null, [$params]);
    }

    // INFO
    // When processing the basic argument array, it is attempted to extract all the arguments from the given args array
    // and all those that cannot be found there are being provided as the default values

    /**
     * If the "processArgsBasic" method can process a simple args array
     */
    public function testProcessArgsBasic(): void
    {
        $params = [
            'param1'        => 'default',
            'param2'        => 'default'
        ];

        $args = [
            'param1'        => '1',
            'param2'        => '2'
        ];

        $processArgsBasic = $this->getProtectedMethod('processArgsBasic');
        $processed_args = $processArgsBasic->invokeArgs(null, [$params, $args]);

        $expected = $args;
        $this->assertSame($expected, $processed_args);
    }

    /**
     * If the "processArgsBasic" method also works when one of the args is missing and the default is required.
     */
    public function testProcessArgsBasicWithDefaults(): void
    {
        $params = [
            'param1'        => 'default',
            'param2'        => 'default'
        ];

        $args = [
            'param1'        => '1',
        ];

        $processArgsBasic = $this->getProtectedMethod('processArgsBasic');
        $processed_args = $processArgsBasic->invokeArgs(null, [$params, $args]);

        $expected = [
            'param1'        => '1',
            'param2'        => 'default'
        ];
        $this->assertSame($expected, $processed_args);
    }

    // INFO
    // When processing an extended params array, all the string values from the given args array are converted into
    // the desired data type by using the "apply" method of the given ParameterType subclass on the string. If they
    // are marked as optional and not provided in the args array, the default is used. If they are required and not
    // provided that will throw an error.

    /**
     * If the "processArgsExtended" method works on the most basic extended params array.
     */
    public function testProcessArgsExtended(): void
    {
        $params = [
            'param1'        => [
                'optional'  => false,
                'type'      => StringType::class,
                'default'   => ''
            ],
            'param2'        => [
                'optional'  => false,
                'type'      => IntType::class,
                'default'   => 10
            ],
            'param3'        => [
                'optional'  => false,
                'type'      => CSVType::class,
                'default'   => []
            ]
        ];

        $args = [
            'param1'        => 'Test',
            'param2'        => '100',
            'param3'        => '10,20'
        ];

        $processArgsExtended = $this->getProtectedMethod('processArgsExtended');
        $processed_args = $processArgsExtended->invokeArgs(null, [$params, $args]);

        $expected = [
            'param1'        => 'Test',
            'param2'        => 100,
            'param3'        => ['10', '20']
        ];
        $this->assertSame($expected, $processed_args);
    }

    /**
     * If the "processArgsExtended" method properly throws an error, when a non-optional parameter is not provided.
     */
    public function testProcessArgsExtendedWithRequiredArgumentWhichIsNotGiven():void
    {
        $params = [
            'param1'        => [
                'optional'  => false,
                'type'      => StringType::class,
                'default'   => ''
            ]
        ];

        $args = [];

        $processArgsExtended = $this->getProtectedMethod('processArgsExtended');

        $this->expectException(ArgumentCountError::class);
        $processed_args = $processArgsExtended->invokeArgs(null, [$params, $args]);
    }

    /**
     * If the "processArgsExtended" properly substitutes the default value for not provided optional parameters.
     */
    public function testProcessArgsExtendedOptionalParameterDefault(): void
    {
        $params = [
            'param1'        => [
                'optional'  => true,
                'type'      => StringType::class,
                'default'   => 'default'
            ]
        ];

        $args = [];

        $processArgsExtended = $this->getProtectedMethod('processArgsExtended');
        $processed_args = $processArgsExtended->invokeArgs(null, [$params, $args]);

        $expected = [
            'param1'        => 'default'
        ];

        $this->assertSame($expected, $processed_args);
    }

    /**
     * If "argsFromGET" works
     */
    public function testArgsFromGET(): void
    {
        $params = [
            'param1'        => 'default',
            'param2'        => 'default',
        ];

        $_GET = [
            'param1'        => '1',
            'something'     => 100,
            'else'          => 1.2,
            'param2'        => '2',
            'setting2'      => 'Test'
        ];

        $argsFromGET = $this->getProtectedMethod('argsFromGET');
        $args = $argsFromGET->invokeArgs(null, [$params]);

        $expected = [
            'param1'        => '1',
            'param2'        => '2'
        ];
        $this->assertSame($expected, $args);
    }

    /**
     * If the "runWrapped" function works properly
     */
    public function testRunWrapped(): void
    {
        // TestCommand has three parameters, which should be provided for a command call:
        // "int_arg", "string_arg" and "array_arg" (as comma separated values)
        $_GET = [
            'int_arg'       => '100',
            'string_arg'    => 'Hello',
            'array_arg'     => '10,20'
        ];

        $test_command = TestCommand::lazyInstance();
        $exit_code = $test_command->runWrapped();

        // The exit code 0 means that the command has terminated without an error.
        $this->assertSame(0, $exit_code);
    }

    /**
     * If the "getParamsArray" static method work / returns the correct array.
     */
    public function testGetParamsArray(): void
    {
        $params = TestCommand::getParamsArray();

        $test_command = new TestCommand();
        $expected = $test_command->params;

        $this->assertSame($params, $expected);
    }

    /**
     * If invoking a static class method using just the string names of the class and the method work as I assumed.
     */
    public function testInvokingStaticMethodsOnClassString(): void
    {
        $class = CommandNamePocket::getClass(static::$TEST_COMMAND_NAME);
        $name = call_user_func([$class, 'getName']);

        $this->assertSame(static::$TEST_COMMAND_NAME, $name);
    }

    // UTILITY FUNCTIONS FOR TESTING
    // *****************************

    /**
     * registers the TestCommand class.
     *
     * This method, called "setUp" is actually of a special meaning to PHPUnit, it is being called before every(!)
     * single test method to perform some common setting up options.
     *
     * In this case the method registers the TestCommand class by calling its static "registerCommand()" method.
     */
    protected function setUp(): void
    {
        $this->registerTestCommand();
    }

    /**
     * unregisters the TestCommand class
     *
     * This method, called "tearDown" is actually of a special meaning to a TestCase, it is being called after
     * every test method to tear down, what has been set up before.
     *
     * In this case the method calls the "unregisterCommand()" method of the TestCommand class.
     */
    protected function tearDown(): void
    {
        $this->unregisterTestCommand();
    }

    /**
     * @throws ReflectionException
     */
    protected static function registerTestCommand(): void
    {
        // So usually the "register" command would be used for the registration of a command. But this method would
        // also perform the registration within wordpress, using wordpress specific methods. Since this is standard
        // PHPUnit with wordpress not loaded, we cannot do that.

        // The register method itself only invokes two sub methods, namely "registerCommand" and "registerWordpress".
        // Since the wordpress registration is not necessary for testing the basic command functionality, we will only
        // invoke "registerCommand" here (it only uses standard PHP functions).

        // The problem now is that "registerCommand" is a protected function and due to the nature of protected
        // functions it cannot be simply invoked from this scope. We use a workaround with "ReflectionClass" to make a
        // temporarily public version of this method. This is what the code below is actually about.
        // "getProtectedMethod" will return an object of type "ReflectionMethod", which can be invoked with an array of
        // args. As the argument to the registration we supply the command name.
        $registerCommand = static::getProtectedMethod('registerCommand');
        $registerCommand->invokeArgs(null, [static::$TEST_COMMAND_NAME]);;
    }

    /**
     * @throws ReflectionException
     */
    protected static function unregisterTestCommand(): void
    {
        // For a more detailed explanation see the comments of the "registerTestCommand" method.
        $unregisterCommand = static::getProtectedMethod('unregisterCommand');
        $unregisterCommand->invokeArgs(null, []);
    }

    /**
     * Returns the ReflectionMethod object for a given method name of the TestCommand class.
     *
     * Protected methods are by nature designed not to be callable by an outer scope, that is their whole point. While
     * this is a nice feature for the program itself, it is not so nice for testing.
     * During testing one would like to test individual private or protected methods of a class individually as well.
     * But they cannot be invoked directly.
     *
     * This method presents a workaround: By using the ReflectionClass a ReflectionMethod object is acquired for the
     * protected method in question. On this reflection object the accessibility can be set to public for the purpose
     * of testing.
     * But this means, that the method has to be used somewhat differently...
     *
     * EXAMPLE
     *
     * ```
     * $method = static::getProtectedMethod("myMethod"); // type: ReflectionMethod
     * $static_method = static::getProtectedMethod("myStaticMethod");
     *
     * // For instance methods the object on which to execute has to be supplied separately.
     * // Arguments for the method call are supplied in an array
     * $object = new MyClass();
     * $method->invokeArgs($object, ["arg1", "arg2"]);
     *
     * // For static methods, null is supplied instead of the object
     * $static_method->invokeArgs(null, ["arg1"]);
     * ```
     *
     * NOTE: Throws a ReflectionException in case the class name of the method name are invalid...
     *
     * @param $name
     * @return ReflectionMethod
     * @throws ReflectionException
     */
    protected static function getProtectedMethod($name): ReflectionMethod
    {
        $class = new ReflectionClass(TestCommand::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }
}