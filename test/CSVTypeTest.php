<?php

use PHPUnit\Framework\TestCase;

use the16thpythonist\Command\Types\CSVType;


class CSVTypeTest extends TestCase
{
    // TESTING THE "APPLY" FUNCTION
    // ****************************

    public function testApply(): void
    {
        // This tests the basic case of a comma separated list of strings to be converted into an array correctly.
        $parameter = "test1,test2,test3";
        $array = CSVType::apply($parameter);

        $expected = ["test1", "test2", "test3"];
        $this->assertEquals($expected, $array);
    }

    public function testApplyIntegerList(): void
    {
        $parameter = "1,2,3";
        $array = CSVType::apply($parameter);

        $expected = ["1","2","3"];
        $this->assertEquals($expected, $array);
    }

    // TESTING THE CHECK FUNCTION
    // **************************

    public function testCheck(): void
    {
        // Tests if an array is checked as true
        $value = ["test1", "test2", "test3"];
        $this->assertTrue(CSVType::check($value));
    }
}