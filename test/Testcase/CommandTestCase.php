<?php


namespace the16thpythonist\Command\Testing\Testcase;

use the16thpythonist\Wordpress\TestCommand;

class CommandTestCase extends AccessTestCase
{
    public $TEST_COMMAND_CLASS = TestCommand::class;
    public $TEST_COMMAND_NAME = 'test_command';

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
    protected function registerTestCommand(): void
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
        $registerCommand = static::getProtectedMethod($this->TEST_COMMAND_CLASS, 'registerCommand');
        $registerCommand->invokeArgs(null, [$this->TEST_COMMAND_NAME]);;
    }

    /**
     * @throws ReflectionException
     */
    protected function unregisterTestCommand(): void
    {
        // For a more detailed explanation see the comments of the "registerTestCommand" method.
        $unregisterCommand = static::getProtectedMethod($this->TEST_COMMAND_CLASS, 'unregisterCommand');
        $unregisterCommand->invokeArgs(null, []);
    }
}