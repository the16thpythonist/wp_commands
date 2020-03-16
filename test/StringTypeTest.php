<?php

use PHPUnit\Framework\TestCase;

use the16thpythonist\Command\Types\StringType;

class StringTypeTest extends TestCase
{
    public function testApply(): void
    {
        $parameter = "test";
        $string = StringType::apply($parameter);
        $this->assertEquals($parameter, $string);
    }
}