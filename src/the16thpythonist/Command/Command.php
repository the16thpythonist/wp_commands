<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 20.06.18
 * Time: 10:42
 */

namespace the16thpythonist\Command;

use Log\LogPost;
use Log\LogInterface;


/**
 * Class Command
 *
 * This is a abstract base class, that represents a Command, that can be executed by the package.
 * To create a new, callable command, this class has to be subclassed. The child class only has to implement a single
 * additional protected method "run" which will contain the actual code to be executed in the background.
 * Additionally, after the class has been written, the static "register" method of that child class has to be called
 * with the desired name for the command, so that Wordpress will execute it during start up.
 *
 * Example:
 *
 * NewCommand extends Command {
 *
 *      protected function run(array $args) {
 *          // Implement your background task here
 *      }
 * }
 * NewCommand::register("new_command");
 * // From that point on the command will be accesible in the admin panel also, under the name "new_command"
 *
 * It works by registering a new wordpress ajax action for each command in such a way, that the code given in the run
 * method will be executed in the ajax call. This way it can be called as a background task from the admin panel,
 * because an Ajax request spawns a new instance of Wordpress/PHP. A background task can be dispatched from the actual
 * code itself as well though, by simple creating a new instance of the class and calling the start method (with the
 * appropriate args array, if necessary)
 *
 * Example:
 *
 * $command = NewCommand();
 * // This will not be blocking. It will create a new AJAX request to run separately
 * $command->start();
 *
 * CHANGELOG
 *
 * Added 22.06.2018
 *
 * @since 0.0.0.0
 *
 * @package the16thpythonist\Command
 */
abstract class Command
{
    /**
     * This method has to be implemented.
     *
     * The run method will have to contain the actual logic of the command, that is supposed to be executed.
     *
     * @param array $args the array of arguments passed to the command
     * @return mixed
     */
    abstract protected function run(array $args);

    /**
     * @var string  This is the string, that is being displayed in front of the title of the log file, that is being
     *              created for each new command
     */
    public static $LOG_PREFIX = 'Command';

    /**
     * @var LogInterface $log   the logging object for the command. Can be any object that suffices the LogInterface
     */
    public $log;

    /**
     * @var string $name        the name of the command. This attribute will be set after the child class to this
     *                          abstract class has been registered, using the static function "register". This
     *                          function takes a single parameter "name", which will then be used as the command name
     *                          and saved in this attribute.
     */
    static public $name;

    /**
     * @var array               this attribute can be modified in the child class to specify which parameters the
     *                          command absolutely expects. Simply putting the key names of the parameters in this array
     *                          will prompt a check if these parameters are contained in the ajax call. If not an
     *                          excpetion will be thrown.
     */
    public $params = array();

    /**
     * Command constructor.
     *
     * CHANGELOG
     *
     * Added 22.06.2018
     *
     * Changed 17.07.2018
     * Changed the way the logging worked to use he LofPost object from the "wp-pi-logging" package
     *
     * Changed 09.08.2018
     * The name of the command is now not longer saved as the static name field (didnt work due to the way static
     * fields work). The name is now retrieved by the 'getName' method. Also using sprintf for Command name formatting
     *
     * Changed 14.08.2018 - 0.0.0.3
     * The title of the log post that is being created for this command is now not hardcoded anymore. The static field
     * LOG_PREFIX is being used as the first part of the title and the separated with a colon is the name of the
     * command, that was executed.
     *
     * @see Log/LogPost
     * @see Log/LogInterface
     *
     * @since 0.0.0.0
     *
     * @param string $log_class a class that implements the Log/LogInterface interface. DEFAULT: Log/LogPost
     */
    public function __construct($log_class=LogPost::class)
    {
        $this->log = new $log_class(NULL, sprintf('%s: %s', self::$LOG_PREFIX, $this->getName()));
        $this->log->start();
    }

    /**
     * Starts the commands as a background process on the server
     *
     * This method sends an AJAX GET request to the server on which the PHP is running itself, which in turn triggers
     * a new PHP/Wordpress instance to spawn on the server, that is handling the actual content of the run method, if
     * the Commmand class was registered.
     *
     * CHANGELOG
     *
     * Added 22.06.2018
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
     * CHANGELOG
     *
     * Added 22.06.2018
     *
     * Changed 17.07.2018
     * Added logging support
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
        $this->log->info('Starting command...');
        $this->run($args);

        $this->log->info('Command ended');
        $this->log->stop();
    }

    /**
     * Gets the name, that was assigned to the Command, when calling 'register'
     *
     * CHANGELOG
     *
     * Added 09.08.2018
     *
     * @since 0.0.0.1
     *
     * @return string
     */
    public function getName() {
        return CommandNamePocket::pick(static::class);
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
     * Changed 14.08.2018
     * Removed the static name field of the class being overwritten with the new name, instead the name is being added
     * to the static class "CommandNamePocket", with the class name being the key for retrieving the name later on.
     *
     * @param string $name The command name under which this command class it to be callable by ajax requests
     * @return void
     *
     * @static
     * @access public
     */
    static public function register(string $name) {
        //static::$name = $name;
        CommandNamePocket::put($name, static::class);

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
     * CHANGELOG
     *
     * Added 22.06.2018
     *
     * @return void
     *
     * @static
     * @access private
     */
    static public function ajaxStart() {
        $command_class = static::class;
        $command = new $command_class();
        $command->runWrapped();
    }
}