<?php


namespace the16thpythonist\Command\Types;

/**
 * Class CSVType
 *
 * extends the abstract base class "ParameterType". See its documentation for the basic information on type classes.
 *
 * This class represents a special variant of an array-type parameter.
 * It may be used to convert a parameter which has to format of comma separated values into an array of strings, using
 * the commas in the original string as the delimiters for the individual array elements.
 *
 * EXAMPLE
 *
 * ```php
 * $parameter = "hello,my,dear";
 * $array = IntType::apply($parameter); // array("hello", "my", "dear");
 * CSVType::check($array) // true
 * ```
 *
 * CHANGELOG
 *
 * Added 16.03.2020
 *
 * @package the16thpythonist\Command\Types
 */
class CSVType extends ParameterType
{
    /**
     * Converts a given string parameter into an array of strings
     *
     * NOTE the special case of a string being passed, which does not contain any comma. The result will be an array
     * with just one element. That element being the original string.
     *
     * EXAMPLE
     *
     * ```php
     * $parameter = "hello,my,dear";
     * $array = IntType::apply($parameter); // array("hello", "my", "dear");
     * ```
     *
     * CHANGELOG
     *
     * Added 16.03.2020
     *
     * @param string $parameter
     * @return array|mixed
     */
    public static function apply(string $parameter)
    {
        return str_getcsv($parameter);
    }

    /**
     * Whether or not the given value is of the array type.
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
        return is_array($value);
    }
}