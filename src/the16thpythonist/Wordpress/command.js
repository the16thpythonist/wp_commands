/**
 * Requests the server for list with infos about the most recently executed commands. The result will be an
 * object, containing other objects with the following keys:
 * - name:  The string name of the command
 * - date:  string datetime, at which the command was started
 * - log:   permalink url to the log post, which was created as output of the command
 *
 * CHANGELOG
 *
 * Added 04.12.2018
 *
 * Changed 05.12.2018
 * Added the missing URL parameter for passing the amount value
 *
 * @param amount    The int amount of items to be returned
 * @param cb        The callback function to be executed, once the response arrives. Has to accept one
 *                  parameter, which is the resulted object containing all the sub objects with the infos
 */
function getRecentCommands(amount, cb) {

    let data = {
        'action': 'get_recent_commands',
        'amount': amount
    };
    jQuery.ajax({
        url:        ajaxURL(),
        type:       'Get',
        timeout:    1000,
        dataType:   'html',
        data:       data,
        success:    function (response) {
            console.log(response);
            // The received response will be a JSON string. Turning that into an object
            let recent_commands = JSON.parse(response);
            // Executing the given callback with the results
            cb(recent_commands);
        },
        error:      function (response) {
            console.log(response);
            console.log('Couldnt get RECENT COMMANDS!');
        }
    })

}