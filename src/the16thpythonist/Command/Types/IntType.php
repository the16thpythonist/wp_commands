<?php


namespace the16thpythonist\Command\Types;


class IntType extends ParameterType
{
    public static function apply(string $parameter)
    {
        if (is_numeric($parameter)) {
            return intval($parameter);
        } else {
            $message = sprintf("Tried to apply IntType on the parameter '%s'", $parameter);
            throw new \TypeError($message);
        }
    }

    public static function check($value): bool
    {
        return is_int($value) && !is_string($value);
    }
}