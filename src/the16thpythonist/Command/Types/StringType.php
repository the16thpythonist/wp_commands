<?php


namespace the16thpythonist\Command\Types;


use phpDocumentor\Reflection\Types\Mixed_;

/**
 * Class StringType
 *
 * extends the abstract base class "ParameterType". See its documentation for the basic information on type classes.
 *
 * This class represents a string type value requirement for a command parameter.
 * It may be used to check whether a given value is a string or be used to convert a string parameter into a string
 * value.
 *
 * EXAMPLE
 *
 * ```php
 * $parameter = "hello";
 * $string = StringType::apply($parameter); // "hello"
 * StringType::check($string) // true
 * ```
 *
 * IMPLEMENTATION
 *
 * Since all the parameters are string values anyways, the implementation of this class is very simple, it will merely
 * return the very value which was given to it.
 *
 * CHANGELOG
 *
 * Added 16.03.2020
 *
 * @package the16thpythonist\Command\Types
 */
class StringType extends ParameterType
{
    public static function apply(string $parameter): string
    {
        return $parameter;
    }

    public static function check($value): bool
    {
        return is_string($value);
    }
}