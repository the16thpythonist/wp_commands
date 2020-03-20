<?php

namespace the16thpythonist\Command\Types;

use phpDocumentor\Reflection\Types\Mixed_;

/**
 * Class ParameterType
 *
 * This is an abstract class, which provides the basic skeleton for a parameter type. A parameter type class is used
 * for the definition of command parameters. One specific parameter type class, which inherits from this base class
 * represents the type of a command parameter, which is required to be supplied during the command invoking.
 *
 * Since the command parameters are supplied by the front end using just text input boxes and then passed to the
 * backend through the _GET array and an AJAX call, the parameters in their original form will all just be string
 * values.
 * It is the ParameterType classes responsibility to provide the mechanisms to convert a given string value into the
 * data type, which is described by them.
 *
 * This conversion will have to be implemented by every individual child class of this abstract base class within the
 * "apply" method. This static method will then be applied to the string parameter to return the new data type.
 *
 * EXAMPLE
 *
 * ```php
 * // Assuming we have a subclass IntType, which will require an integer value
 * $parameter = "12"
 * IntType::apply($parameter); // 12
 * ```
 *
 * CHANGELOG
 *
 * Added 16.03.2020
 *
 * @package the16thpythonist\Command\Types
 */
abstract class ParameterType
{
    /**
     * This is an abstract method which will have to be implemented by the subclasses.
     *
     * This method accepts a string value and attempts to convert this value into the data type, which is the described
     * by the individual ParameterType child class.
     * In case the string value cannot be converted due to format issues, this method should raise an exception.
     *
     * EXAMPLE
     * Consider the example of a child class, which describes a integer type value "IntType"
     *
     * ```php
     * $valid = "100"
     * IntType::apply($valid); // 100
     *
     * $invalid = "Hello"
     * IntType::apply($invalid); // raises Exception
     * ```
     *
     * @param string $parameter
     * @return mixed
     */
    abstract public static function apply(string $parameter);

    /**
     * This is an abstract method which will have to be implemented by the subclasses.
     *
     * this method accepts a value of the very type, which is described by the individual ParameterType child class.
     * This value will then be converted (back) into a string and returned.
     * This is basically the inverse operation to the actual "apply" operation of the class.
     *
     *  EXAMPLE
     * Consider the example of a child class, which describes a integer type value "IntType"
     *
     * ```php
     * $valid = 100
     * IntType::unapply($valid); // "100"
     *
     * $invalid = "text"
     * IntType::apply($invalid); // throws Exception
     * ```
     *
     * @param $value
     * @return string
     */
    abstract public static function unapply($value): string;

    /**
     * This is an abstract method which will have to be implemented by the subclass.
     *
     * This method accepts a value and checks, whether the type of this value conforms with the type, which is
     * described by the individual ParameterType child class.
     *
     * EXAMPLE
     * Consider the example fo a child class, which describes an integer type value "IntType"
     *
     * ```php
     * $int = 100;
     * IntType::check($int); // true
     *
     * $string = "hello";
     * IntType::check($string);
     * ```
     *
     * @param mixed $value
     * @return bool
     */
    abstract public static function check($value): bool ;

    /**
     * This is an abstract method which will have to be implemented by the subclass.
     *
     * This method returns the string name of the type on which it is called. For a sublcass "StringType" that would be
     * "string" for example or for "IntType" it would be "int"
     *
     * @return string
     */
    abstract public static function getName(): string;
}