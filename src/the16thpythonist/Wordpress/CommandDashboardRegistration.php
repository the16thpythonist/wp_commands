<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 15.08.18
 * Time: 11:19
 */

namespace the16thpythonist\Wordpress;

use Log\VoidLog;
use the16thpythonist\Command\Command;

use Log\LogPost;
use the16thpythonist\Command\CommandNamePocket;
use WP_Query;

/**
 * Class CommandDashboardRegistration
 *
 * This class is used to register the Command Dashboard Widget in Wordpress.
 * The Widget contains a list of the most recently executed commands.
 *
 * To use the widget, a instance of this class has to be created and the register method has to be called.
 * This is supposed to happen only once during the wordpress runtime.
 *
 * @package the16thpythonist\Wordpress
 */
class CommandDashboardRegistration
{
    /**
     * The unique id, with which the widget is being registered in wordpress.
     * This is a constant, because it is not supposed to be customizable whatsoever.
     */
    const WIDGET_ID     = '16-command-dashboard-widget';

    /**
     * The title of the widget box in the admin dashboard.
     * This is a constant, because it is not supposed to be customizable.
     */
    const WIDGET_NAME   = 'Commands Overview';

    /**
     * @var int This is the int amount of how many recent commands are supposed to be displayed in the widget.
     *          The amount can be customized by changing this static field, before an instance of this class has been
     *          created.
     */
    public static $RECENT_COMMANDS_LENGTH = 5;

    /**
     * @var string This is the datetime format used for displaying the date of a recently executed command.
     */
    public static $DATETIME_FORMAT = 'dS M, H:i';

    /**
     * Hooks in all the methods, that register stuff with wordpress
     *
     * Currently hooks in the 'register_dashboard_widget' method into the wordpress dashboard setup
     *
     * CHANGELOG
     *
     * Added 14.08.2018
     *
     * Changed 13.11.2018
     * Added the registration of the ajax method, that returns the default parameters for a command name
     *
     * @since 0.0.0.3
     */
    public function register() {
        add_action('wp_dashboard_setup', array($this, 'register_dashboard_widget'));

        // Registering the ajax callback method, which returns the list of default parameters for any command name
        add_action('wp_ajax_command_default_args', array($this, 'ajaxDefaultArguments'));

        // Register the test command
        // TestCommand::register('test-command');
    }

    /**
     * Calls the method, that creates a new dashboard widget
     *
     * Uses 'WIDGET_ID' class constant as the idd for the widget wordpress internally and 'WIDGET_NAME' as the name
     * of the Widget, which will be displayed in the wordpress backend as the title of the widget box
     *
     * CHANGELOG
     *
     * Added 15.08.2018
     *
     * @since 0.0.0.3
     */
    public function register_dashboard_widget() {
        wp_add_dashboard_widget(
            self::WIDGET_ID,
            self::WIDGET_NAME,
            array($this, 'display_widget')
        );
    }

    /**
     * Ajax callback for returning the default parameters for a given command name
     *
     * CHANGELOG
     *
     * 13.11.2018
     */
    public function ajaxDefaultArguments() {

        // The name of the command, for which the arguments are requested has to be given in the GET array
        if (array_key_exists('name', $_GET)) {

            $name = $_GET['name'];

            if (!array_key_exists($name, Command::$class_pocket)) {
                throw new \Exception(sprintf('The command name "%s" does not match any registered function'));
            }
            // Getting the class name according to the command name. This is saved within a static assoc array of the
            // "Command" abstract base class
            $class = Command::$class_pocket[$name];

            // Getting the array, that specifies the expected arguments from the specific class
            $instance = new $class(VoidLog::class);
            $args = $instance->params;

            // Returning a response, that contains the default parameters for the given command name
            echo json_encode($args);
        } else {
            throw new \Exception('No command name given!');
        }
        wp_die();
    }

    /**
     * Echos the actual html code for the widget.
     *
     * The widget features a Display of the most recently executed commands
     *
     * CHANGELOG
     *
     * Added 15.08.2018
     *
     * Changed 29.08.2018
     * Fixed an issue with the dashboard plugin completely killing the CPU, because an index ran out of bounds, when
     * there were less actual log posts, then the number of recent posts to be displayed
     *
     * Changed 29.08.2018
     * Added the widget, which is used to select and actually execute the commands (which was only on the command
     * exclusive page up to this point) also to the dashboard widget. The commands can now be executed right from
     * the dashboard.
     *
     * @since 0.0.0.3
     */
    public function display_widget() {
        /*
         * What do I event want in there:
         * - The last 5 or so Logs, as links.
         * - The actual functionality via AJAX (not that important right now)
         */
        $args = array(
            'post_type'         => LogPost::$POST_TYPE,
            'posts_per_page'    => 50,
            'orderby'           => 'date',
            'order'             => 'DESC'

        );
        $query = new WP_Query($args);
        $posts = $query->get_posts();

        /*
         * $posts only contains the raw post objects for all the log posts. Not even all of these have to be Command
         * related. The next section checks if the post is command related and if that is the case uses the
         * data to populate a new array, which only contains the most important data (title as key and a array with url
         * and date as value), which can be directly iterated to create the HTML items.
         */
        $commands = array();
        $index = 0;
        while ((count($commands) < self::$RECENT_COMMANDS_LENGTH) && !(count($commands) >= count($posts))) {
            $post = $posts[$index];
            $title = $post->post_title;

            /*
             * The substring, for which to be checked to verify the log post being for a command is the log prefix, that
             * has been defined in the Command class. If it was hardcoded, this could break after a change of that
             * prefix.
             * But after the log prefix has been checked it is being removed from the string to get the pure command
             * name
             */
            if (strpos($title, Command::$LOG_PREFIX) !== False) {
                $command_name = str_replace(Command::$LOG_PREFIX . ': ', '', $title);
                $commands[] = array(
                    'title'     => $command_name,
                    'date'      => date(self::$DATETIME_FORMAT, strtotime($post->post_date)),
                    'url'       => get_the_permalink($post->ID)
                );
            }
            $index++;
        }

        /*
         * This url will lead to the page, where all the Log posts can be viewed. It makes sense to also include
         * this generic link into the widget.
         */
        $logs_uri = get_site_url(null, '/wp-admin/edit.php?post_type=' . LogPost::$POST_TYPE);

        /*
         * An array containing all the command names is needed for displaying the selection widget, that is used for
         * the widget, that actually executing the commands.
         */
        $command_names = CommandNamePocket::$names;

        // 13.11.2018
        // The value of each possible selection will no longer start with the prefix 'start_', this prefix will be
        // appended in the function, which actually sends the ajax request.
        ?>
        <div class="command-widget">
            <h2>Execute commands</h2>
            <p>
                Use the following selection to select a command, fill in the parameters and press the execute button!
            </p>
            <div id="command-container" style="display: flex; flex-direction: column;">
                <select id="command-name" title="action" style="height: 25px;width: 80%;">
                    <?php foreach ($command_names as $command_name): ?>
                        <option value="<?php echo $command_name; ?>"><?php echo $command_name; ?></option>
                    <?php endforeach; ?>
                </select>

                <div id="parameter-container" style="display: flex; flex-direction: column">
                </div>
                <input id="command-call" type="button" value="execute" style="margin-bottom: -5px;height: 25px;width: 20%">
            </div>
            <script type="text/javascript">
                loadCSS("<?php echo plugin_dir_url(__FILE__) . 'command.css' ?>");
                let script_url = "<?php echo plugin_dir_url(__FILE__); ?>command.js";
                console.log(`Attempting to load the script "${script_url}"`);
                jQuery.getScript(script_url, function () {

                    let call = jQuery('input#command-call');
                    let select = jQuery('select#command-name');

                    updateParameterContainer();

                    call.on('click', function () {
                        let command_name = select.attr('value');
                        let parameters = getCommandParameters();
                        executeCommand(command_name, parameters);
                        console.log(`Command "${command_name}" was executed!`);
                    });

                    select.on('change', function () {
                        updateParameterContainer();
                    })
                });
            </script>
            <h2>Command history</h2>
            <p>
                Displaying the last 5 commands, that were executed. (At this moment, the selection is not being
                updated, once a command was executed from within this widget, please visit
                <a href="<?php echo $logs_uri;?>">Log Posts</a> to view the log)
            </p>
            <div class="">
                <?php foreach ($commands as $command): ?>
                    <p>
                        <?php echo $command['date']; ?>:
                        <a href="<?php echo $command['url']; ?>"><?php echo $command['title']; ?></a>
                    </p>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }
}