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
 * EXAMPLE
 *
 * ```php
 * NewCommand extends Command {
 *
 *      protected function run(array $args) {
 *          // Implement your background task here
 *      }
 * }
 * NewCommand::register("new_command");
 * // From that point on the command will be accesible in the admin panel also, under the name "new_command"
 * ```
 *
 * It works by registering a new wordpress ajax action for each command in such a way, that the code given in the run
 * method will be executed in the ajax call. This way it can be called as a background task from the admin panel,
 * because an Ajax request spawns a new instance of Wordpress/PHP.
 *
 * A background task can be dispatched from the actual
 * code itself as well though, by simple creating a new instance of the class and calling the start method (with the
 * appropriate args array, if necessary)
 *
 * EXAMPLE
 *
 * ```
 * $command = NewCommand();
 * // This will not be blocking. It will create a new AJAX request to run separately
 * $command->start();
 * ```
 *
 * LOGGING
 *
 *
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
     * EXAMPLE:
     * In this example we will create a new child class, which will implement the abstract base class "Command".
     * This child class will override the implementation for the "run" method. The code executed by the command in the
     * background will simply write the string "Hello World" into the log of the command.
     *
     * ```php
     * class HelloCommand extends Command {
     *      protected function run(array $args) {
     *          $this->log->info("Hello World!");
     *      }
     * }
     * ```
     *
     * CHANGELOG:
     *
     * Changed 15.03.2020
     * Added the example to the documentation
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
     *                          exception will be thrown.
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
     * Changed the way the logging worked to use the LofPost object from the "wp-pi-logging" package
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
     * This method can be used to start a background command programmatically from within the actual PHP code (Just to
     * be clear, this method is not necessary for the front end functionality of the command mechanism).
     * To start a command from within the code a new object instance of that specific command has to be created and
     * then the "start" method has to be called on it.
     *
     * EXAMPLE
     *
     * ```php
     * $command = new SpecificCommand();
     *
     * // This will NOT be a blocking call. In the end it will just dispatch the command to run as a separate instance
     * // on the sever. Thus it will also not give a return or any way to check the execution of the command directly.
     * $command->start();
     * ```
     *
     * IMPLEMENTATION
     *
     * This method sends an AJAX GET request to the server on which the PHP is running itself, which in turn triggers
     * a new PHP/Wordpress instance to spawn on the server, that is handling the actual content of the run method, if
     * the Commmand class was registered.
     *
     * CHANGELOG
     *
     * Added 22.06.2018
     *
     * Changed 15.03.2020
     * Added the Documentation
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
     * This method is the actual method that is being executed, when a command execution is issued from the front end.
     * The "run" method, which was implemented by the child class will be called within this method along the way ->
     * it is being wrapped. But this method does some other things to setup correct execution of the command.
     *
     * IMPLEMENTATION
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
     * Changed 13.11.2918
     * Fixed command parameter support
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
            $this->log->warning(sprintf('Command expected "%s" args, but only received "%s"', $expected_number_args, $given_number_args));

            // In case the wrong amount was put in, the command execution terminates
            $this->log->stop();
            return;

        } else {
            $this->log->info(sprintf('Got the expected number of args "%s"', $given_number_args));

            // parameter array only copied, if correct amount
            // Using the $params array as a basis, as that contains all the default values for the parameters and if values
            // for those parameters have been passed with the AJAX call, then the default values will be overridden with
            // the actual ones.
            $args = array_replace($this->params, $args);
        }

        // Finally calling the actual run method, which contains the actual command to be executed
        $this->log->info('Starting command...');

        // Actually running the custom code in the command
        $this->run($args);

        $this->log->info('Command ended');
        $this->log->stop();
    }

    /**
     * Returns the array of arguments for the command call.
     *
     * Each command can define a set of arguments which it needs for the execution. These arguments have to be passed
     * to the server by the frontend somehow. If a command is being invoked in a AJAX call these argument values are
     * being passed as values of the _GET array.
     * This method will extract all the relevant values from the _GET array and return them as an associative array,
     * where the string name of the argument is the key and the value passed from _GET is the value to the pair.
     *
     * EXAMPLE
     *
     * ```php
     * $multiply_command->argumentsFromGET // array('a' => '2', 'b' => '4')
     * ```
     *
     * CHANGELOG
     *
     * Added 15.03.2020
     */
    private function argumentsFromGET(){
        $args = array();
        foreach ($this->params as $param => $default) {
            // When a command is being invoked from the front end, the arguments to this command call are being passed
            // to the server via the _GET assoc array.
            // "$this->params" is an associative array, whose keys represent the names of the arguments, which are
            // expected by the command.
            if (array_key_exists($param, $_GET)) {
                $args[$param] = $_GET[$param];
            }
        }

        return $args;
    }

    /**
     * Gets the name, that was assigned to the Command, when calling 'register'
     *
     * EXAMPLE
     *
     * ```php
     * NewCommand::register('new_command')
     *
     * // Later:
     * $command = new NewCommand();
     * $name = $command->getName(); // 'new_command'
     * ```
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
        // The "CommandNamePocket" class is basically a static class, which acts as an associative array. During the
        // "register" method for the command the string name of the command has been saved in it, with the key being
        // the class name of this very class.
        // So the command name has been saved externally rather than in a class attribute. You can read up the Problem
        // which led to this decision in the Doc of the "CommandNamePocket" class.
        // It is just important, that the "pick" method will return the command name, that has been associated with this
        // class.
        return CommandNamePocket::pick(static::class);
    }

    // REGISTERING AND UNREGISTERING THE COMMAND
    // *****************************************

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
     * Changed 13.11.2018
     * Saving the class name and chosen command name as key value pairs to the static arrays name_pocket and
     * class_pocket.
     *
     * Changed 16.03.2020
     * Moved the functionality of this method into two separate methods: "registerWordpress" is now responsible to
     * register the command for wordpress by adding the ajax action hook and "registerCommand" is not responsible
     * to register the command within the command name pocket and the command reference...
     *
     * @param string $name The command name under which this command class it to be callable by ajax requests
     * @return void
     *
     * @static
     * @access public
     */
    public static function register(string $name) {
        static::registerWordpress($name);
        static::registerCommand($name);
    }

    protected static function registerCommand(string $name): void
    {
        // So this line is weird...
        // This is the place, where the name of the command is being saved to be used later, so one might be asking
        // why not just assign the value to a static attribute of the class? The short answer is because it is not
        // possible for a child class. The long answer can be found in the Documentation for the "CommandNamePocket"
        // class.
        // This class basically works like a static associative array, where the name of the command is saved externally
        // The name is being associated with the CLASS NAME of this very class. It will be able to be accessed later on
        // by this class name as well.
        CommandNamePocket::put($name, static::class);

        // Adding the command to a static container, which will contain a list of all the command names registered
        // during the runtime of a PHP instance.
        CommandReference::addCommand($name);
    }

    protected static function registerWordpress(string $name): void
    {
        add_action('wp_ajax_' . $name, array(static::class, 'ajaxStart'));
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
     * Changed 16.03.2020
     * Moved the creation of the object instance to the method "lazyInstance()" and using this now.
     *
     * @return void
     *
     * @static
     * @access private
     */
    public static function ajaxStart() {
        $command = static::lazyInstance();
        $command->runWrapped();
    }

    /**
     * Returns an object instance for the specific CHILD class, on which this method is being invoked on.
     *
     * EXAMPLE
     * Consider a child class "TestCommand" which extends "Command":
     *
     * ```php
     * // test_command will be of type "TestCommand" rather than "Command" (which you would have assumed, because this
     * // method is implemented in the "Command" base class.
     * $test_command = TestCommand::lazyInstance();
     * ```
     *
     * CHANGELOG
     *
     * Added 16.03.2020
     */
    public static function lazyInstance() {
        return new static();
    }

    /**
     * Creates a new callable Command, that only executes the given callable function
     *
     * This method is based on an extremely dirty hack.
     * A new class is being created, that extends the Command class by dynamic code execution using 'eval'. This class
     * contains a run method, which will only execute the given callable and do nothing else.
     * Since the callable itself cannot be passed to the dynamic code directly it is stored in a static transfer class
     * 'CommandFunctionTransfer'. The callable is then extracted from this static class within the dynamic code and
     * executed. The dynamic code also calls the 'register' method of the new class with the given name
     *
     * CHANGELOG
     *
     * Added 15.08.2018
     *
     * @since 0.0.0.4
     *
     * @param string $name      the name of the function. This string in all upper characters will serve as the
     *                          name of the temp dynamic class as well.
     * @param callable $func    The callable object to be used as a command. Has to have to parameter: $args and $log
     */
    public static function fromCallable(string $name, callable $func) {
        /*
         * The Command class is created by dynamic code execution, thus no values can be passed into the dynamic code
         * directly. Here the callable function is being put into a static transfer object, which is also know from the
         * scope inside the dynamic code. Inside the dynamic code the callable is simply extracted again.
         */
        CommandFunctionTransfer::$callable = $func;
        // The command name will also be used as the class name of the dynamic class
        $class_name = strtoupper($name);
        $code_lines = array(
            'namespace the16thpythonist\Command;',
            'class %s extends Command {',
            'protected function run(array $args){',
            '$f = CommandFunctionTransfer::$callable;',
            '$f($args, $this->log);',
            '}',
            '}',
            '%s::register("%s");'
        );
        $code = implode('', $code_lines);
        eval(sprintf($code, $class_name, $class_name, $name));
    }



}