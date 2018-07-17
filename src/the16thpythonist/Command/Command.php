<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 20.06.18
 * Time: 10:42
 */

namespace the16thpythonist\Command;

use Log\LogInterface;

abstract class Command
{

    abstract protected function run(array $args);

    private $log;
    static public $name;
    public $params;

    public function __construct(LogInterface $log_controller)
    {
        $this->log = $log_controller;
    }

    /**
     * Starts the commands as a background process on the server
     *
     * This method sends an AJAX GET request to the server on which the PHP is running itself, which in turn triggers
     * a new PHP/Wordpress instance to spawn on the server, that is handling the actual content of the run method, if
     * the Commmand class was registered.
     *
     * @param array $args The arguments to be passed to the command
     * @return void
     *
     * @access public
     */
    public function start(array $args=array()) {
        // The AJAX URL to send the request to
        $ajax_url = admin_url('admin-ajax.php');

        // Adding the argument, which specifies, which actual ajax action to execute as the start varition of this
        // very command object itself
        $command_name = 'start_' . static::$name;
        $args['action'] = $command_name;
        wp_remote_get($ajax_url, $args);
    }

    /**
     * Runs the actual run method with the passed parameters from the $_GET array
     *
     * This method first extract all the possible parameters from the $_GET array, that have been passed by the AJAX
     * call. Then these parameters are passed to the call of the actual "run" method, which has been implemented by a
     * child class to "Command"
     *
     * @return void
     * @access public
     */
    public function runWrapped() {
        $args = array();
        foreach ($this->params as $param => $default) {
            if (array_key_exists($param, $_GET)) {
                $args[$param] = $_GET[$param];
            }
        }
        // Logging an error message, in case the amount of arguments found in the $_GET array does not match the number
        // of arguments expected by the comment
        $expected_number_args = count(array_keys($this->params));
        $given_number_args = count(array_keys($args));
        if ($expected_number_args != $given_number_args) {
            $this->log->warning('Command expected ' . $expected_number_args . ' args, but only received ' . $given_number_args);
        }

        // Using the $params array as a basis, as that contains all the default values for the parameters and if values
        // for those parameters have been passed with the AJAX call, then the default values will be overridden with
        // the actual ones.
        $args = array_replace($this->params, $args);

        // Finally calling the actual run method, which contains the actual command to be executed
        $this->run($args);
    }


    /**
     * Registers the static methods to be called when the according ajax request are made
     *
     * Based on the command name given, two variations of this name will be made 'start_{name}' and 'update_{name}'
     * and these will be registered as action names for wordpress ajax requests, with the two static methods of this
     * class being the targets of those ajax calls.
     *
     * CHANGELOG
     *
     * Added 22.06.2018
     *
     * Changed 17.07.2018
     * The the start command name is now additionally appended to the static array within the CommandReference class,
     * so that there is always a available list of commands that are registered and ready to be called.
     *
     * @param string $name The command name under which this command class it to be callable by ajax requests
     * @return void
     *
     * @static
     * @access public
     */
    static public function register(string $name) {
        static::$name = $name;

        $start_command_name = 'start_' . $name;
        $update_command_name = 'update_' . $name;
        // Register the 'ajax_start' static method to a wordpress ajax action
        add_action('wp_ajax_' . $start_command_name, array(static::class, 'ajaxStart'));
        // Register the 'ajax_update' static method to a wordpress ajax action
        //add_action('wp_ajax_' . $update_command_name, array(static::class, 'ajaxUpdate'));

        /*
         * Adding the command to a static container, which will contain a list of all the command names registered
         * during the runtime of a PHP instance.
         */
        CommandReference::addCommand($start_command_name);
    }

    /**
     * executes the already wrapped run method of a newly created command object
     *
     * This method was registered with wordpress to be called when the "start" variation of the command, which is
     * described by this object, was called by an AJAX request. Creates a new Command object and calls the run method
     * to be executed. This whole process will be in the background of the actual main PHP process, because by being
     * called by an AJAX request to the server this method will be run in an entirely new instance of wordpress
     *
     * @return void
     *
     * @static
     * @access private
     */
    static private function ajaxStart() {
        // Creates a new command object from the sub class this method has been called from
        $command_class = static::class;
        $command = new $command_class();

        /* @var $command Command */
        $command->runWrapped();
    }
}