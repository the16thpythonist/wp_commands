<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 19.11.18
 * Time: 14:26
 */

namespace the16thpythonist\Wordpress;

use Log\LogPost;
use the16thpythonist\Command\Command;

/**
 * Class WpCommands
 *
 * The facade for the whole package
 *
 * @package the16thpythonist\Wordpress
 */
class WpCommands
{
    public static $REGISTRATION;

    /**
     * CHANGELOG
     *
     * Added 19.11.2018
     */
    public static function register() {
        // Using the registration object to register all functionality in wordpress
        $registration = new WpCommandsRegistration();
        $registration->register();

        // Saving the registration object for possible later use
        self::$REGISTRATION = $registration;
    }

    /**
     * Returns an array of LogPost object, where each log was the output of a previously executed Command.
     * The Logs in the array will be sorted by date and in a descending order, which means, that those Commands
     * issued most recently will be the first items in the list.
     *
     * CHANGELOG
     *
     * Added 04.12.2018
     *
     * @param int $count    The max(!) amount of LogPost objects to be in the returned array
     * @return array
     */
    public static function getCommandLogs(int $count=-1) {

        // Fetching all the posts objects that match the Log post type and which have the necessary prefix in the title
        $args = array(
            'post_type'         => LogPost::$POST_TYPE,
            'posts_per_page'    => $count,
            'orderby'           => 'date',
            'order'             => 'DESC',
            's'                 => Command::$LOG_PREFIX
        );
        $query = new \WP_Query($args);
        $posts = $query->get_posts();

        // Since the posts array only contains the raw WP_Post objects. They are being wrapped by the
        // LogPost class
        $log_posts = array();
        /** @var \WP_Post $post */
        foreach ($posts as $post) {
            // These post properties will be needed to create the LogPost wrapper object
            $post_title = $post->post_title;
            $post_id = $post->ID;

            // Loading all the log data into the wrapper object and then adding it to the list of
            // objects to be returned
            $log_post = new LogPost($post_id, $post_title);
            $log_post->load();
            $log_posts[] = $log_post;
        }

        return $log_posts;
    }
}