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
 * The whole purpose of this class is to register the new "background command" menu with wordpress.
 *
 * CHANGELOG
 *
 * Added 17.07.2018
 *
 * @since 0.0.0.0
 *
 * @package the16thpythonist\Wordpress
 */
class CommandMenuRegistration
{
    /**
     * CommandMenuRegistration constructor.
     *
     * CHANGELOG
     *
     * Added 17.07.2018
     *
     * @since 0.0.0.0
     */
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
     * Changed 17.10.2018
     * Changed the hook, at which the menu us being registered from "init" to "admin_page" as the former caused a
     * fatal error in the unit tests.
     *
     * @since 0.0.0.0
     */
    public function register() {
        add_action('admin_menu', array($this, 'register_menu_page'));
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
            1
        );
    }

    /**
     * Echos the HTML for the menu page
     *
     * CHANGELOG
     *
     * Added 17.07.2018
     *
     * @since 0.0.0.0
     */
    public function display_page() {
        $commands = CommandReference::getCommands();
        $ajax_url = admin_url('admin-ajax.php');
        ?>

        <div class="background-commands-container">
            <h2>Wordpress Background Commands</h2>
            <select id="command-name" title="action">
                <?php foreach ($commands as $command): ?>
                    <option value="<?php echo $command; ?>"><?php echo $command; ?></option>
                <?php endforeach; ?>
            </select>
            <input id="command-call" type="button" value="execute">
        </div>
        <script type="text/javascript">
            function sendAJAX() {
                var action = 'action=' + select.attr('value');
                console.log(action);
                jQuery.ajax({
                    url:        ajaxurl,
                    type:       'Get',
                    timeout:    5000,
                    dataType:   'html',
                    data:       'action=' + select.attr('value'),
                    success:    function(response) {
                        alert(response);
                    },
                    error:      function(response) {
                        console.log(response);
                    }
                });
            }
            var call = jQuery('input#command-call'),
                select = jQuery('select#command-name');
            console.log("Script gets executed");
            console.log(call.attr('value'));
            call.on('click', sendAJAX);
        </script>
        <?php
    }
}