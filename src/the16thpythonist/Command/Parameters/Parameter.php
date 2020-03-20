<?php


namespace the16thpythonist\Command\Parameters;

/*
 * public interface of Parameter:
 *
 *      __construct(string $name, $default, string $type, bool $optional)
 */

use the16thpythonist\Command\Types\StringType;

/**
 * Class Parameter
 *
 * This class represents the specification (settings) for a parameter of a command.
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
 * This very class presents a simplification for the process of working with these parameter definitions. The problem is
 * that this "params" array can be defined in different formats and without an intermediate wrapping of this array one
 * would have to make a case difference in all the places where this array is being used within the code and then based
 * on these cases have different implementations for the same feature.
 * This class is designed to be able to be created from all these formats and then provide a common interface for
 * accessing the information contained within these parameter specifications!
 * With this there only needs to be one case statement for converting the arrays into Parameter objects and then all
 * over the code these objects can be used instead.
 *
 * EXAMPLE
 *
 * ```php
 * // Basic format
 * $params = [
 *      'count'         => '10'
 * ]
 * $parameter = Parameter::fromBasicFormat('count', $params['count']);
 *
 * // Extended format
 * $params = [
 *      'count'        => [
 *          'optional'  => true,
 *          'type'      => IntType::class,
 *          'default'   => 10
 * ]
 * $parameter = Parameter::fromExtendedFormat('count', $params['count']);
 * ```
 *
 * CHANGELOG
 *
 * Added 19.03.2020
 *
 * @package the16thpythonist\Command\Parameters
 */
class Parameter
{
    public $name;
    public $optional;
    public $default;
    public $type;

    /**
     * Parameter constructor.
     *
     * DESIGN CHOICES
     *
     * The order in which the arguments of the constructor are defined actually has a purpose. The name and default
     * value for the parameter are required parameters and come up first. This is because those two are the only
     * defining characteristics of the most simple format possible, the Basic params format. The following parameters
     * the type and the optional flag have predefined values. These values are the ones always assumed for the basic
     * format.
     * This layout makes this class extendable. If another, even more advanced, format is to be added to the possible
     * formats than this format will need to be represented by more characteristics which will then in turn have to be
     * added to this constructor as well. Implementing them at the end of the list with default values will not break
     * any existing functionality of this class right away!
     *
     * CHANGELOG
     *
     * Added 19.03.2020
     *
     * @param string $name
     * @param string $type_class
     * @param $default
     * @param bool $optional
     */
    public function __construct(
        string $name,
        $default,
        string $type_class = StringType::class,
        bool $optional = true
    )
    {
        $this->name = $name;
        $this->optional = $optional;
        $this->default = $default;
        $this->type = $type_class;
    }
}