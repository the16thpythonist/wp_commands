<?php

namespace the16thpythonist\Command\Testing\Testcase;

use ReflectionMethod;

interface Accessible
{
    public static function getProtectedMethod(string $class, string $name): ReflectionMethod;

    public function invokeProtectedMethod($object, string $name, array $args);

    public function invokeStaticProtectedMethod(string $name, array $args);
}