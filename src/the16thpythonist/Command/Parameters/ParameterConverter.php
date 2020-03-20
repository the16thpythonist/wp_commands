<?php


namespace the16thpythonist\Command\Parameters;

use the16thpythonist\Command\Types\StringType;


/*
 * public interface of ParameterConverter:
 *
 *      __construct(array $params)
 *      getParameters()
 *      getParametersAssociative()
 */

/**
 * Class ParameterConverter
 *
 * This class is used to convert the array specification of command parameters to the universal Parameter objects.
 *
 * BACKGROUND
 *
 * A new command can be created by subclassing the abstract "Command" base class and implementing a custom "run" method.
 * But since most commands need some sort of input parameters to provide useful functionality, there needs to be a
 * mechanism to define which parameters a command expects, so that these can be passed in for the execution.
 * This way of specifying parameters is done by creating a default property "params" on this new child class. This
 * array consists of key value pairs, where the key is the string name of the parameter and the value being the
 * settings for it.
 *
 * THE PROBLEM
 *
 * An additional problem arises since there are multiple possible formats for how this "params" array can be defined.
 * Taking every possible format under consideration, each point in the code where the command parameters are being
 * processed has to contain if cases and differing code of how to handle these formats. This would lead to a tight
 * coupling to the format specifics and changing or introducing a new one would be a nightmare.
 * Thus the "Parameter" class has been designed, which is supposed to be the only representation of a parameter
 * in the code internally.
 * This is also where this class comes in it is the responsibility of the "ParameterConverter" class to accept a
 * "params" array and convert it into a list/array of Parameter objects. Thus this class implements the recognition
 * of which format has been passed and the individual conversions into a Parameter object. This creates a much looser
 * coupling since this class will thus be the only one, which has to know about the format specifics and changing one
 * format would only require a change within the implementation of this class, while its public interface's
 * functionality can remain the same.
 *
 * EXAMPLE
 *
 * Consider the following example. The params array has been acquired from its original definition and now these
 * parameters are to be processed further and for that they shall be converted into an array of Parameter objects
 *
 * ```php
 * $params = [
 *      "param1"        => "default",
 *      "param2"        => "100"
 * ];
 *
 * $parameter_converter = new ParameterConverter($params);
 * $parameters = $parameter_converter->getParameters();
 * ```
 *
 * CHANGELOG
 *
 * Added 19.03.2020
 *
 * @package the16thpythonist\Command\Parameters
 */
class ParameterConverter
{
    const FORMAT_BASIC = 'basic';
    const FORMAT_EXTENDED = 'extended';

    protected $params;

    /**
     * ParameterConverter constructor.
     *
     * DESIGN CHOICES
     *
     * So there are two design choices I want to talk about:
     * 1) This is not a static class: To use the functionality of this class a new object instance has to be created.
     * Usually I would not have done this, because the functionality provided by this class does not really need a
     * instance to be created. It could have been done in a more functional manner using a static class. I didnt do
     * it however because I thought the impact on the performance was marginal and that static classes are bad
     * practice, when you are committed to OOP.
     * 2) $params is an argument for the constructor: it would have been a more idiomatic design to not have it be a
     * parameter of the constructor and instead pass it in through a "set" method. For one thing, this would have
     * enabled the reuse of the objects and furthermore classes with a rather slim constructor are easier to test as
     * there are not so many potential error sources already within the constructor.
     * I didnt do it with this class however for the ease of implementation. Using a "set" method I would have had to
     * implement the whole thing with throwing an error in case the a get was called before set and so on.
     *
     * CHANGELOG
     *
     * Added 19.03.2020
     *
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * Returns a list with all Parameter objects for the specified parameters
     *
     * CHANGELOG
     *
     * Added 19.03.2020
     *
     * @return array
     */
    public function getParameters(): array
    {
        $parameters_associative = $this->getParametersAssociative();
        return array_values($parameters_associative);
    }

    /**
     * Returns an assoc array with the keys being the parameter names and the values the according Parameter objects
     *
     * CHANGELOG
     *
     * Added 19.03.2020
     *
     * @return array
     */
    public function getParametersAssociative(): array
    {
        $parameters = [];
        foreach ($this->params as $name => $value) {
            $format = $this->getFormat($name, $value);
            if ($format == self::FORMAT_BASIC) {
                $parameters[$name] = $this->parameterFromBasic($name, $value);
            }
            if ($format == self::FORMAT_EXTENDED) {
                $parameters[$name] = $this->parameterFromExtended($name, $value);
            }
        }
        return $parameters;
    }

    // HELPER FUNCTIONS
    // ****************

    /**
     * Given the parameter name and its value from the params array returns a string which indicated the format of pair
     *
     * This function takes one key value pair from a "params" array and then return a string identifier, which tells
     * in which supported format the key value pair was defined in. If if was the basic format it will return the
     * string constant FORMAT_BASIC (aka "basic") for extended format the constant FORMAT_EXTENDED (aka "extended").
     *
     * IMPLEMENTATION
     *
     * Right now the implementation is very crude and thus now very stable. Right now a Basic format is just identified
     * by the value being a string and the extended format by the value being an array. This works just fine when the
     * params array is correctly defined, but for the case of the array of an extended format value having the wrong
     * keys for example this check would already fail.
     * To make this more robust one would have to validate the data within a extended format value as well...
     *
     * CHANGELOG
     *
     * Added 19.03.2020
     *
     * @param string $name
     * @param $value
     * @return string
     */
    protected function getFormat(string $name, $value): string
    {
        if (is_array($value)) {
            return self::FORMAT_EXTENDED;
        } else if (is_string($value)) {
            return self::FORMAT_BASIC;
        } else {
            throw new \TypeError("The values for the params array must be either string or array!");
        }
    }

    // SPECIALIZED CONVERSIONS
    // ***********************
    // A simple way of achieving a common parameter type signature with all these conversion methods would be to
    // have it be (string $name, array $params). By having the second argument being the whole params array, but that
    // would have conflicted the law of demeter, which is to never pass in more data than is actually needed by the
    // function!

    /**
     * Creates a Parameter object from the key value pair of a basic params format.
     *
     * CHANGELOG
     *
     * Added 19.03.2020
     *
     * @param string $key The name of the parameter
     * @param string $default The string default value of the parameter
     * @return Parameter
     */
    protected function parameterFromBasic(string $key, string $default) {
        return new Parameter(
            $key,
            $default,
            StringType::class,
            true
        );
    }

    /**
     * Creates a Parameter object from the key value pair of an extended params format
     *
     * CHANGELOG
     *
     * Added 19.03.2020
     *
     * @param string $key The name of the parameter
     * @param array $settings The assoc array defining the settings for this parameter
     * @return Parameter
     */
    protected function parameterFromExtended(string $key, array $settings) {
        return new Parameter(
            $key,
            $settings['default'],
            $settings['type'],
            $settings['optional']
        );
    }
}