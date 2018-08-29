<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 15.08.18
 * Time: 11:19
 */

namespace the16thpythonist\Wordpress;

use the16thpythonist\Command\Command;

use Log\LogPost;
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
     * @since 0.0.0.3
     */
    public function register() {
        add_action('wp_dashboard_setup', array($this, 'register_dashboard_widget'));
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
             */
            if (strpos($title, Command::$LOG_PREFIX) !== False) {
                $commands[] = array(
                    'title'     => $title,
                    'date'      => date(self::$DATETIME_FORMAT, strtotime($post->post_date)),
                    'url'       => get_the_permalink($post->ID)
                );
            }
            $index++;
        }
        ?>
        <div class="command-widget">
            <h2>Command history</h2>
            <p>
                The last 5 Commands, that have been executed
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