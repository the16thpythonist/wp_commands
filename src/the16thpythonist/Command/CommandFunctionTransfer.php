<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 15.08.18
 * Time: 17:06
 */

namespace the16thpythonist\Command;

/**
 * Class CommandFunctionTransfer
 *
 * This is a static class used to store a value.
 *
 * The problem is the following: A callable type value has to be passed from a certain scope into the code of a dynamic
 * code exection via 'eval'. This does not work directly as the dynamic code doesnt have access to variables from the
 * scope that called it.
 * The solution is to have the outer scope put the value into a static transfer container, that is known from both
 * scopes and then have the dynamic code retrieve it from there
 *
 * @package the16thpythonist\Command
 */
class CommandFunctionTransfer
{
    /**
     * @var callable This will contain the callable value, that is to be used as a command.
     */
    public static $callable;
}