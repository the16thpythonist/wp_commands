<?php

use PHPUnit\Framework\TestCase;

use the16thpythonist\Command\CommandNamePocket;

class CommandNamePocketTest extends TestCase
{
    public function testPut(): void
    {
        CommandNamePocket::put('test_command', 'TestCommand');

        $this->assertTrue(in_array('test_command', CommandNamePocket::$names));
        // Cleaning up
        unset(CommandNamePocket::$names['TestCommand']);
    }

    // NOTE: all the following tests rely on the correct function of the "put" method.

    public function testPick(): void
    {
        CommandNamePocket::put('test_command', 'TestCommand');

        $command_name = CommandNamePocket::pick('TestCommand');
        $this->assertEquals('test_command', $command_name);
        // Cleaning up
        unset(CommandNamePocket::$names['TestCommand']);
    }

    public function testWithdraw() {
        CommandNamePocket::put('test_command', 'TestCommand');

        $this->assertTrue(in_array('test_command', CommandNamePocket::$names));
        CommandNamePocket::withdraw('TestCommand');
        $this->assertFalse(in_array('test_command', CommandNamePocket::$names));
    }

    public function testContains(): void
    {
        CommandNamePocket::put('test_command', 'TestCommand');

        $this->assertTrue(CommandNamePocket::contains('test_command'));
        // Cleaning up
        unset(CommandNamePocket::$names['TestCommand']);
    }
}