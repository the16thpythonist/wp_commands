<?php

use PHPUnit\Framework\TestCase;

use the16thpythonist\Wordpress\WpCommandsRegistration;

use the16thpythonist\Wordpress\TestCommand;

use the16thpythonist\Command\Testing\Testcase\CommandTestCase;

// NOTE:
// The "WpCommandsRegistration" class, which is tested here is a class which mainly contains wordpress related code,
// which cannot be properly tested with standard PHPUnit. So the only code tested here is the functionality, which
// could be decoupled from the wordpress code.

class WpCommandsRegistrationTest extends CommandTestCase
{
    public $ACCESS_CLASS = WpCommandsRegistration::class;

    /**
     * If "getCommandNameFromLogName" properly converts the log name to command name
     */
    public function testGetCommandNameFromLogName() {
        $log_name = "Command: my_command";

        $command_name = $this->invokeStaticProtectedMethod('getCommandNameFromLogName', [$log_name]);

        $expected = "my_command";
        $this->assertSame($expected, $command_name);
    }

    /**
     * If "validateCommandRegistered" will not throw any errors if the command is registered
     */
    public function testValidateCommandRegistered() {
        // Since this is a subclass of CommandTestCase, the TestCommand class has been registered in the setUp
        // fixture before this test case, so naturally the method should not do anything
        $this->invokeStaticProtectedMethod('validateCommandRegistered', [$this->TEST_COMMAND_NAME]);
        $this->assertTrue(true);
    }

}