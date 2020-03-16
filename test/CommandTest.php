<?php

use PHPUnit\Framework\TestCase;

use the16thpythonist\Command\Command;

use the16thpythonist\Wordpress\TestCommand;


class CommandTest extends TestCase
{
    // TESTING INDIVIDUAL FUNCTIONS
    // ****************************

    public function testLazyInstance() {
        $registerCommand = $this->getProtectedMethod('registerCommand');
        $registerCommand->invokeArgs(null, ['test_command']);

        $test_command = TestCommand::lazyInstance();

        $this->assertInstanceOf(TestCommand::class, $test_command);
    }

    // UTILITY FUNCTIONS FOR TESTING
    // *****************************

    protected static function getProtectedMethod($name): ReflectionMethod
    {
        $class = new ReflectionClass('TestCommand');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }
}