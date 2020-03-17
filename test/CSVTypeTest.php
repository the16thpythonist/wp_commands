<?php

use PHPUnit\Framework\TestCase;

use the16thpythonist\Command\Types\CSVType;


class CSVTypeTest extends TestCase
{
    // TESTING THE "APPLY" FUNCTION
    // ****************************

    /**
     * Basic functionality of apply
     */
    public function testApply(): void
    {
        $parameter = "test1,test2,test3";
        $array = CSVType::apply($parameter);

        $expected = ["test1", "test2", "test3"];
        $this->assertEquals($expected, $array);
    }

    /**
     * If the apply method turns a CSV string of numeric values into a list of strings (and not numerals)
     */
    public function testApplyIntegerList(): void
    {
        $parameter = "1,2,3";
        $array = CSVType::apply($parameter);

        $expected = ["1","2","3"];
        $this->assertSame($expected, $array);
    }

    /**
     * If a string without commas will be converted correctly into an array with single element.
     */
    public function testApplyNoCommas(): void
    {
        $parameter = "hello world";
        $array = CSVType::apply($parameter);

        $expected = ["hello world"];
        $this->assertEquals($expected, $array);
    }

    // TESTING THE CHECK FUNCTION
    // **************************

    /**
     * Basic functionality of check to detect array type.
     */
    public function testCheck(): void
    {
        // Tests if an array is checked as true
        $value = ["test1", "test2", "test3"];
        $this->assertTrue(CSVType::check($value));
    }
}