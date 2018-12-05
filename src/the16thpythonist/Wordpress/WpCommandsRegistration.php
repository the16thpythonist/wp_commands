<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 19.11.18
 * Time: 14:27
 */

namespace the16thpythonist\Wordpress;

use Log\LogPost;
use the16thpythonist\Command\Command;

/**
 * Class WpCommandsRegistration
 *
 * The registration object setting up the whole package functionality in wordpress
 *
 * CHANGELOG
 *
 * Added 19.11.2018
 *
 * @package the16thpythonist\Wordpress
 */
class WpCommandsRegistration
{
    /**
     * WpCommandsRegistration constructor.
     *
     * CHANGELOG
     *
     * Added 19.11.2018
     */
    public function __construct()
    {

    }

    /**
     *
     * CHANGELOG
     *
     * Added 19.11.2018
     *
     * Changed 04.12.2018
     * Added the AJAX function registrations to be executed as well.
     * Added the JS Script registrations to be executed as well.
     */
    public function register() {
        // Registering the Command Menu
        CommandMenu::register();

        // 04.12.2018
        // Registering all the ajax utilities in wordpress
        $this->registerAjax();
        // Registering all the needed scripts to be linked to each element
        $this->registerScripts();
    }

    // 04.12.2018
    // REGISTER OF THE USED STYLES AND SCRIPTS
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
     */
    public function enqueueScripts() {
        wp_enqueue_script('commands-utility', plugin_dir_url(__FILE__) . 'command.js');
    }

    // 04.12.2018
    // GENERAL UTILITY FUNCTIONS PROVIDED VIA AJAX
    /**
     * This function registers all the AJAX utilities within wordpress
     *
     * CHANGELOG
     *
     * Added 04.12.2018
     */
    public function registerAjax() {
        // Adding a function which will provide a list of the most recent commands, that have been executed
        add_action('wp_ajax_get_recent_commands', array($this, 'ajaxGetRecentCommands'));
    }

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
     */
    public function ajaxGetRecentCommands() {
        // The parameters for this function, which will have to be passed with the GET request will have to be
        // the AMOUNT of recent commands to be returned.
        $amount = $_GET['amount'];

        // First we get the logs for all these past commands. Because the info what will be returned for each command
        // will be the name of the command, that was executed, the date when and the link to the log file
        $log_posts = WpCommands::getCommandLogs($amount);
        $results = array();
        /** @var LogPost $log_post */
        foreach ($log_posts as $log_post) {

            // Deriving the command name from the name of the log file, because the command log files are created by
            // using exactly the command name and a prefix. We will simply get rid of the prefix
            $log_name = $log_post->subject;
            $command_name = str_replace(Command::$LOG_PREFIX, '', $log_name);

            // The url to the log file can simply be generated as a wordpress permalink and we only need the post id
            // of the log to do that!
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

}