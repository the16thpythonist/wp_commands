<?php

use PHPUnit\Framework\TestCase;

use the16thpythonist\Command\Types\StringType;

class StringTypeTest extends TestCase
{
    // THE "apply" METHOD

    /**
     * If the "apply" function works correctly
     */
    public function testApply(): void
    {
        $parameter = "test";
        $string = StringType::apply($parameter);
        $this->assertEquals($parameter, $string);
    }

    // THE "unapply" METHOD

    /**
     * If the "unapply" function works correctly
     */
    public function testUnapply(): void
    {
        $value = "test";
        $string = StringType::unapply($value);

        $expected = "test";
        $this->assertEquals($expected, $string);
    }

    /**
     * If "unapply" will throw an error for a non string value
     */
    public function testUnapplyWithNonStringValue(): void
    {
        $value = 100;

        $this->expectException(TypeError::class);
        StringType::unapply($value);
    }

    // THE "check" METHOD

    /**
     * If "check" is true for a string value
     */
    public function testCheck(): void
    {
        $value = "100";
        $this->assertTrue(StringType::check($value));
    }

    /**
     * If "check" is false for a non string value
     */
    public function testCheckWithNonStringValue(): void
    {
        $value = 100;
        $this->assertFalse(StringType::check($value));
    }
}