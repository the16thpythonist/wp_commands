<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 17.07.18
 * Time: 07:57
 */

namespace the16thpythonist\Wordpress;

use the16thpythonist\Command\CommandReference;


/**
 * Class CommandMenuRegistration
 *
 *
 *
 */
class CommandMenuRegistration
{
    public function __construct()
    {

    }

    /**
     * Hooks in all the methods, that register stuff with wordpress
     *
     * CHANGELOG
     *
     * Added 17.07.2018
     *
     * @since 0.0.0.0
     */
    public function register() {
        add_action('init', array($this, 'register_menu_page'));
    }

    /**
     * Registers the new menu page with wordpress
     *
     * CHANGELOG
     *
     * Added 17.07.2018
     *
     * @since 0.0.0.0
     */
    public function register_menu_page() {
        add_menu_page(
            'Wordpress Background Commands',
            'Background Commands',
            'activate_plugins',
            'background-commands',
            array($this, 'display_page'),
            'dashicons-editor-code',
            15
        );
    }

    /**
     * Echos the HTML for the menu page
     *
     * CHANGELOG
     *
     * Added 17.07.2018
     */
    public function display_page() {
        $commands = CommandReference::getCommands();
        $ajax_url = admin_url('admin-ajax.php');
        ?>
        <div class="background-commands-container">
            <h2>Wordpress Background Commands</h2>
            <form action="<?php echo $ajax_url; ?>" method="get">
                <select title="action">
                    <?php foreach ($commands as $command): ?>
                        <option value="<?php echo $command; ?>"><?php echo $command; ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="submit">
            </form>
        </div>
        <?php
    }

}