<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 19.11.18
 * Time: 14:27
 */

namespace the16thpythonist\Wordpress;

use Log\LogPost;
use the16thpythonist\Command\CommandFacade;

/**
 * Class WpCommandsRegistration
 *
 * The registration object setting up the whole package functionality in wordpress
 *
 * CHANGELOG
 *
 * Added 19.11.2018
 *
 * Changed 20.03.2020
 * Added the protected property $command_facade, which will contain a CommandFacade object during the lifetime of a
 * registration object...
 *
 * @package the16thpythonist\Wordpress
 */
class WpCommandsRegistration
{
    protected $command_facade;
    /**
     * WpCommandsRegistration constructor.
     *
     * CHANGELOG
     *
     * Added 19.11.2018
     */
    public function __construct()
    {
        $this->command_facade = new CommandFacade();
    }

    // REGISTRATION PROCESS
    // ********************

    /**
     *
     * CHANGELOG
     *
     * Added 19.11.2018
     *
     * Changed 04.12.2018
     * Added the AJAX function registrations to be executed as well.
     * Added the JS Script registrations to be executed as well.
     *
     * Changed 28.03.2020
     * Added the stylesheets to be registered here using the new method "registerStylesheets"
     */
    public function register() {
        // Registering the Command Menu
        CommandMenu::register();

        // 04.12.2018
        // Registering all the ajax utilities in wordpress
        $this->registerAjax();
        // Registering all the needed scripts to be linked to each element
        $this->registerScripts();

        // 28.03.2020
        $this->registerStylesheets();
    }

    /**
     * Registers the stylesheets to be added to the header within wordpress
     *
     * CHANGELOG
     *
     * Added 28.03.2020
     */
    public function registerStylesheets() {
        $callable = array($this, 'enqueueStylesheets');
        add_action('admin_enqueue_scripts', $callable);
    }

    /**
     *
     * CHANGELOG
     *
     * Added 28.03.2020
     */
    public function enqueueStylesheets() {
        wp_enqueue_style('wpcommands', plugin_dir_url(__FILE__) . 'command.css');
        wp_enqueue_style('wpcommands-frontend', plugin_dir_url(__FILE__). 'frontend/dist/wpcommandlib.css');
    }

    /**
     * This function hooks the scripts enqueue's into the admin hook AS WELL AS the normal user hook, which means
     * all the utility scripts can be used from any user as well!
     *
     * CHANGELOG
     *
     * Added 04.12.2018
     */
    public function registerScripts() {
        $callable = array($this, 'enqueueScripts');
        add_action('admin_enqueue_scripts', $callable);
        // Avoiding a security issue for now
        //add_action('wp_enqueue_scripts', $callable);
    }

    /**
     * This function calls the 'wp_enqueue_script' utility for each JS script file needed for the package.
     *
     * CHANGELOG
     *
     * Added 04.12.2018
     *
     * Changed 27.03.2020
     * Added the stylesheet for the vue main package and the umd package for the local Vue frontend code
     */
    public function enqueueScripts() {
        wp_enqueue_script('commands-utility', plugin_dir_url(__FILE__) . 'command.js');

        wp_enqueue_script('vue', 'https://unpkg.com/vue');
        wp_enqueue_script('frontend', plugin_dir_url(__FILE__) . 'frontend/dist/wpcommandlib.umd.js');
    }

    /**
     * This function registers all the AJAX utilities within wordpress
     *
     * CHANGELOG
     *
     * Added 04.12.2018
     *
     * Changed 17.03.2020
     * Added the registration of the "wp_ajax_get_command_default_args". This was originally implemented within
     * CommandDashboardRegistration, but it didnt make sense there conceptionally, so it has been moved here.
     * Added the registration of the "wp_ajax_get_command_arg_types", which will return an array of types for the
     * arguments.
     */
    public function registerAjax() {
        // Adding a function which will provide a list of the most recent commands, that have been executed
        add_action('wp_ajax_get_recent_commands', array($this, 'ajaxGetRecentCommands'));

        // 17.03.2020
        add_action('wp_ajax_get_command_default_args', [$this, 'ajaxGetArgumentDefaultValues']);
        add_action('wp_ajax_get_command_arg_types', [$this, 'ajaxGetArgumentTypes']);
    }

    // AJAX CALLBACKS
    // **************

    /**
     * Responds to an AJAX GET request with the action 'get_recent_commands'.
     * The request must contain the following URL parameters:
     * - amount:    The int amount of most recent commands to be returned
     * The response will be an array containing associative arrays with the keys
     * - name: The string name of the command, that was executed
     * - date: The string datetime, when the command was started
     * - log: The permalink url towards the log post, which was created by the command
     *
     * CHANGELOG
     *
     * Added 04.12.2018
     *
     * Changed 05.12.2018
     * Actually used the function for getting the permalink for the log url
     *
     * Changed 20.03.2020
     * Using the $command_facade property now to get the command name based on the log name.
     */
    public function ajaxGetRecentCommands() {
        // The parameters for this function, which will have to be passed with the GET request will have to be
        // the AMOUNT of recent commands to be returned.
        $amount = $_GET['amount'];

        // First we get the logs for all these past commands. Because the info what will be returned for each command
        // will be the name of the command, that was executed, the date when and the link to the log file
        $log_posts = self::getCommandLogs($amount);
        $results = array();
        /** @var LogPost $log_post */
        foreach ($log_posts as $log_post) {

            // Deriving the command name from the name of the log file, because the command log files are created by
            // using exactly the command name and a prefix. We will simply get rid of the prefix
            $log_name = $log_post->subject;
            $command_name = $this->command_facade->getCommandFromLogName($log_name);

            // The url to the log file can simply be generated as a wordpress permalink and we only need the post id
            // of the log to do that!
            // TODO: This is the responsibility of the LOG class!
            $command_log_url = get_edit_post_link($log_post->post_id);

            // The date at which the command was started is even saved as a separate attribute of the LogPost!
            $command_date = $log_post->starting_time;

            $result = array(
                'name'      => $command_name,
                'log'       => $command_log_url,
                'date'      => $command_date
            );
            $results[] = $result;
        }

        // Now finally converting the array to a JSON string to answer the request
        echo json_encode($results);
        wp_die();
    }

    /**
     * Ajax callback for retrieving a list of default values for the arguments of a command
     *
     * CHANGELOG
     *
     * Added 17.03.2020
     *
     * Changed 20.03.2020
     * Using the $command_facade property now to retrieve the list of default parameter values
     */
    public function ajaxGetArgumentDefaultValues() {
        try {
            $name = $_GET['name'];

            $defaults = $this->command_facade->getCommandParameterDefaultValues($name);

            echo json_encode($defaults);
        } catch (\Exception $e) {
            echo $e->getMessage();
        } finally {
            wp_die();
        }
    }

    /**
     * Ajax callback for retrieving a list of types for arguments of a command
     *
     * CHANGELOG
     *
     * Added 17.03.2020
     *
     * Changed 20.03.2020
     * Using the $command_facade property now to retrieve the list of parameter types
     */
    public function ajaxGetArgumentTypes() {
        try {
            $name = $_GET['name'];

            $types = $this->command_facade->getCommandParameterTypes($name);

            echo json_encode($types);
        } catch (\Exception $e) {
            echo $e->getMessage();
        } finally {
            wp_die();
        }
    }

    // GENERAL UTILITY FUNCTIONS
    // *************************

    /**
     * Returns an array of LogPost object, where each log was the output of a previously executed Command.
     * The Logs in the array will be sorted by date and in a descending order, which means, that those Commands
     * issued most recently will be the first items in the list.
     *
     * CHANGELOG
     *
     * Added 04.12.2018
     *
     * Changed 20.03.2020
     * The command log prefix is now retrieved through a CommandFacade object instance.
     *
     * @param int $count    The max(!) amount of LogPost objects to be in the returned array
     * @return array
     */
    public static function getCommandLogs(int $count=-1) {
        $command_facade = new CommandFacade();
        // Fetching all the posts objects that match the Log post type and which have the necessary prefix in the title
        $args = array(
            'post_type'         => LogPost::$POST_TYPE,
            'posts_per_page'    => $count,
            'orderby'           => 'date',
            'order'             => 'DESC',
            's'                 => $command_facade->getCommandLogPrefix()
        );
        $query = new \WP_Query($args);
        $posts = $query->get_posts();

        // Since the posts array only contains the raw WP_Post objects. They are being wrapped by the
        // LogPost class
        $log_posts = array();
        /** @var \WP_Post $post */
        foreach ($posts as $post) {
            // Loading all the log data into the wrapper object and then adding it to the list of
            // objects to be returned
            $log_post = new LogPost($post->ID, $post->post_title);
            $log_post->load();
            $log_posts[] = $log_post;
        }

        return $log_posts;
    }
}