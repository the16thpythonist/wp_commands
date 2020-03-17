<?php

namespace the16thpythonist\Command\Testing\Testcase;

use PHPUnit\Framework\TestCase;

use ReflectionClass;
use ReflectionMethod;

class AccessTestCase extends TestCase implements Accessible
{
    public $ACCESS_CLASS;

    public static function getProtectedMethod(string $class, string $name): ReflectionMethod
    {
        $class = new ReflectionClass($class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

    public function invokeProtectedMethod($object, string $name, array $args=[])
    {
        $method = $this->getProtectedMethod($this->ACCESS_CLASS, $name);
        return $method->invokeArgs($object, $args);
    }

    public function invokeStaticProtectedMethod(string $name, array $args=[])
    {
        $method = $this->getProtectedMethod($this->ACCESS_CLASS, $name);
        return $method->invokeArgs(null, $args);
    }
}