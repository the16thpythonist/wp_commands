<?php

use PHPUnit\Framework\TestCase;

use the16thpythonist\Command\Types\IntType;

class IntTypeTest extends TestCase
{
    // TESTING THE "APPLY" METHOD
    // *************************

    public function testApply(): void
    {
        $parameter = "12";
        $int = IntType::apply($parameter);

        $this->assertEquals(12, $int);
    }

    // TESTING THE "CHECK" METHOD
    // **************************

    public function testCheck():void
    {
        // When the check method is invoked with an integer value it obviously needs to return true.
        // This is the most basic check of functionality.
        $int = 12;
        $this->assertTrue(IntType::check($int));
    }

    public function testCheckWithString(): void
    {
        // When the check method for IntType is invoked for a string that obviously needs to return false.
        $string = "hell0";
        $this->assertFalse(IntType::check($string));
    }

    public function testCheckWithNumericString(): void
    {
        // "numeric string" refers to a string which contains just a number. Basically the string representation of
        // an integer.

        // This test method was added as the initial implementation of the "check" method used the is_numeric function
        // to check for integers, but this function also returns true for this kind of numeric string, which is not
        // a wanted behaviour
        $string = "12";
        $this->assertFalse(IntType::check($string));
    }
}