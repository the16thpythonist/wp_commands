<?php

use PHPUnit\Framework\TestCase;

use the16thpythonist\Command\Types\IntType;

class IntTypeTest extends TestCase
{
    // THE "apply" METHOD

    /**
     * If "apply" converts a string to an int correctly
     */
    public function testApply(): void
    {
        $parameter = "12";
        $int = IntType::apply($parameter);

        $this->assertEquals(12, $int);
    }

    // THE "check" METHOD

    /**
     * If "check" returns true for a int
     */

    public function testCheck():void
    {
        // When the check method is invoked with an integer value it obviously needs to return true.
        // This is the most basic check of functionality.
        $int = 12;
        $this->assertTrue(IntType::check($int));
    }

    /**
     * If "check" returns false for a string
     */
    public function testCheckWithString(): void
    {
        // When the check method for IntType is invoked for a string that obviously needs to return false.
        $string = "hell0";
        $this->assertFalse(IntType::check($string));
    }

    /**
     * If "check" returns false even when a valid string rep of a numeric value is passed
     */
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

    // THE "unapply" METHOD

    /**
     * If "unapply" correctly converts an int into a string
     */
    public function testUnapply(): void
    {
        $value = 100;
        $string = IntType::unapply($value);

        $expected = "100";
        $this->assertSame($expected, $string);
    }

    /**
     * If "unapply" throws error for string (non-int) value
     */
    public function testUnapplyWithStringValue(): void
    {
        $value = "100";

        $this->expectException(TypeError::class);
        IntType::unapply($value);
    }
}