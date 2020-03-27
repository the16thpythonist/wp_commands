<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 15.08.18
 * Time: 11:19
 */

namespace the16thpythonist\Wordpress;

use the16thpythonist\Command\CommandFacade;

use Log\LogPost;
use Log\VoidLog;

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
 * CHANGELOG
 *
 * Added 14.08.2018
 *
 * Changed 23.03.2020
 * Added additional instance property $command_facade, which will store a CommandFacade instance. This object will be
 * used for all access to the command business logic.
 *
 * @package the16thpythonist\Wordpress
 */
class CommandDashboardRegistration
{
    /**
     * The unique id, with which the widget is being registered in wordpress.
     * This is a constant, because it is not supposed to be customizable whatsoever.
     */
    const WIDGET_ID     = 'command-dashboard-widget';

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

    public $command_facade;

    public function __construct()
    {
        $this->command_facade = new CommandFacade();
    }

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
     * Changed 04.12.2018
     * Added the stylesheet to be included with wordpress
     *
     * Changed 17.03.2020
     * Removed the registration of the TestCommand. This is something the user should decide to do on his own.
     * Removed the registration of "wp_ajax_command_default_args". It has been moved to the WpCommandsRegistration
     * class as it makes more sense there conceptionally.
     *
     * @since 0.0.0.3
     */
    public function register() {
        add_action('wp_dashboard_setup', array($this, 'register_dashboard_widget'));

        // 04.12.2018
        // Adding the stylesheet to be included by wordpress, but only within the admin backend
        add_action('admin_enqueue_scripts', array($this, 'registerStylesheet'));
    }

    /**
     * Adds the stylesheet "command.css", which contains the style/layout rules for the dashboard widget
     * to be included by wordpress
     *
     * CHANGELOG
     *
     * Added 04.12.2018
     */
    public function registerStylesheet() {
        // Adding the CSS stylesheet for the widget to wordpress
        wp_enqueue_style('commands', plugin_dir_url(__FILE__) . 'command.css');
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
     * Changed 23.03.2020
     * Replaced all the usages of Command and CommandNamePocket with uses of CommandFacade.
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
        if (count($posts) > 1) {
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
                $log_prefix = $this->command_facade->getCommandLogPrefix();
                if (strpos($title, $log_prefix) !== False) {
                    $command_name = $this->command_facade->getCommandFromLogName($title);
                    $commands[] = array(
                        'title'     => $command_name,
                        'date'      => date(self::$DATETIME_FORMAT, strtotime($post->post_date)),
                        'url'       => get_the_permalink($post->ID)
                    );
                }
                $index++;
            }
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
        $command_names = $this->command_facade->registeredCommands();

        // 13.11.2018
        // The value of each possible selection will no longer start with the prefix 'start_', this prefix will be
        // appended in the function, which actually sends the ajax request.
        ?>
        <div id="app">
            <app></app>
        </div>

        <script>
            new Vue({
                components: {
                    app: wpcommandsapp
                }
            }).$mount('#app')
        </script>
        <div class="command-widget">
            <p>Use the following section to <strong>select a command</strong> and press the button <strong>to execute it</strong>!</p>
            <div id="command-container" style="display: flex; flex-direction: column;">
                <select id="command-name" title="action" style="height: 25px;width: 80%;">
                    <?php foreach ($command_names as $command_name): ?>
                        <option value="<?php echo $command_name; ?>"><?php echo $command_name; ?></option>
                    <?php endforeach; ?>
                </select>

                <div class="command-container" id="parameter-container" style="display: flex; flex-direction: column">
                </div>
                <input id="command-call" type="button" value="execute" style="margin-bottom: -5px;height: 25px;width: 20%">
            </div>

            <hr>

            <p>
                View the <strong>3 most recently executed Commands</strong>:
            </p>
            <div class="command-container" id="recent-command-container">

            </div>

            <script type="text/javascript">
                //loadCSS("<?php echo plugin_dir_url(__FILE__) . 'command.css' ?>");
                let script_url = "<?php echo plugin_dir_url(__FILE__); ?>command-widget.js";
                console.log(`Attempting to load the script "${script_url}"`);
                jQuery.getScript(script_url, function () {

                    let call = jQuery('input#command-call');
                    let select = jQuery('select#command-name');

                    updateParameterContainer();
                    getRecentCommands(3, displayRecentCommands);

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
        </div>
        <?php
    }
}