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


}