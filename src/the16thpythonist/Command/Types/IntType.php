<?php


namespace the16thpythonist\Command\Types;

/**
 * Class IntType
 *
 * extends the abstract base class "ParameterType". See its documentation for the basic information on type classes.
 *
 * This class represents a int type value requirement for a command parameter.
 * It may be used to check whether a given value is a int or be used to convert a string parameter into an int
 * value.
 *
 * EXAMPLE
 *
 * ```php
 * $parameter = "100";
 * $int = IntType::apply($parameter); // 100
 * IntType::check($int) // true
 * ```
 *
 * CHANGELOG
 *
 * Added 16.03.2020
 *
 * @package the16thpythonist\Command\Types
 */
class IntType extends ParameterType
{
    const NAME = 'int';

    /**
     * Converts the given string parameter into an int value, if possible
     *
     * NOTE This method will throw a TypeError in case the passed string is not a valid string representation of an
     * integer value
     *
     * EXAMPLE
     *
     * ```php
     * $parameter = "100";
     * IntType::apply("100"); // 100
     * ```
     *
     * @param string $parameter
     * @return int|mixed
     */
    public static function apply(string $parameter)
    {
        if (is_numeric($parameter)) {
            return intval($parameter);
        } else {
            $message = sprintf("Tried to apply IntType on the parameter '%s'", $parameter);
            throw new \TypeError($message);
        }
    }

    public static function unapply($value): string
    {
        if (static::check($value)) {
            return sprintf("%s", $value);
        } else {
            $message = sprintf("Cannot unapply IntType on value, which is not type '%s'", static::NAME);
            throw new \TypeError($message);
        }
    }

    /**
     * Whether or not the given value is an int
     *
     * EXAMPLE
     *
     * ```php
     * IntType::check("hello world"); // false
     * IntType::check(13.2); // false
     * IntType::check(100); // true
     * ```
     *
     * CHANGELOG
     *
     * Added 16.03.2020
     *
     * @param mixed $value
     * @return bool
     */
    public static function check($value): bool
    {
        return is_int($value) && !is_string($value);
    }

    public static function getName(): string
    {
        return static::NAME;
    }
}