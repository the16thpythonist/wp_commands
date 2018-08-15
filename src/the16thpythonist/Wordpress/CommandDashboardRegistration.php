<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 15.08.18
 * Time: 11:19
 */

namespace the16thpythonist\Wordpress;


use Log\LogPost;

class CommandDashboardRegistration
{
    const WIDGET_ID     = '16-command-dashboard-widget';
    const WIDGET_NAME   = 'Commands Overview';

    public function register_dashboard_widget() {
        wp_add_dashboard_widget(
            self::WIDGET_ID,
            self::WIDGET_NAME,
            array($this, 'display_widget')
        );
    }

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
        $query = WP_Query($args);
        $posts = $query->get_posts();

        $commands = array();
        $index = 0;
        while (count($commands) <= 5) {
            $post = $posts[$index];
            $title = $post->post_title;

            if (strpos($title, 'Command') !== False) {
                $commands[$title] = array();
                // Adding the date and the link to the array
                $commands[$title]['date'] = $post->post_date;
                $commands[$title]['url'] = get_the_permalink($post->ID);
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
                <?php foreach ($commands as $title => $content): ?>
                    <p>
                        <?php echo $content['date']; ?>:
                        <a href="<?php echo $content['url']; ?>"><?php echo $title; ?></a>
                    </p>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

}