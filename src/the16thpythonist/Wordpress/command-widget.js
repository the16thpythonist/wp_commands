// The container, which contains the input boxes for the parameters
var parameter_container = jQuery('div#parameter-container');

// The selection widget, where the commands can be chosen from
var select = jQuery('select#command-name');

// 05.12.2018
// The container, which contains the info about the recent commands
var recent_command_container = jQuery('div#recent-command-container');

/**
 * Executes the background command with the given name on the server
 *
 * CHANGELOG
 *
 * Added 13.11.2018
 *
 * Changed 05.12.2018
 * Added functionality, which will update the recent command display of the widget to view the one
 * just executed.
 *
 * @param command_name  The name of the command to be executed
 * @param parameters
 */
function executeCommand(command_name, parameters) {
    // The prefix 'start_' is appended, because the ajax callback was registered within wordpress using this callback
    let action = {
        'action':   `start_${command_name}`
    };

    // With this notation the two properties of the two objects action and parameters are combined into a single object.
    // This will be needed to pass all the necessary URL parameters to the get request.
    let data = {...action, ...parameters};
    console.log(`Executing Command ${command_name}`);
    jQuery.ajax({
        url:        ajaxURL(),
        type:       'Get',
        timeout:    5000,
        dataType:   'html',
        data:       data,
        success:    function (response) {
            console.log(response);
        },
        error:      function (response) {
            console.log(response);
        }
    });

    // 05.12.2018
    // After a certain time has passed and the log file of the newly executed command has probably been saved to
    // the database already, we update the recent commands display, so that the this new command will appear.
    setTimeout(function () {
        // First we delete all the currently displayed info about the recent commands and then
        // we request them again and display the new ones
        jQuery('p.recent-command').remove();
        getRecentCommands(3, displayRecentCommands);
    }, 500);
}

/**
 * Returns an object with property names being the parameter names and the value being the input parameter values
 *
 * CHANGELOG
 *
 * Added 13.11.2018
 *
 * @return {{}}
 */
function getCommandParameters() {
    // Object, whose properties act as associative array,
    let parameters = {};
    let parameter_inputs = jQuery('input.command-parameter');
    parameter_inputs.each(function () {
        let parameter_input = jQuery(this);
        let name = parameter_input.attr('title');
        parameters[name] = parameter_input.attr('value');
    });
    return parameters;
}

/**
 * Sends an AJAX request to the server, requesting the expected arguments for the given command name. The response will
 * be a JSON string, containing an array where the keys are the names of the expected arguments and the value the
 * default values to those args. This JS object to this JSON string is then passed to the given function "f" as the
 * only parameter.
 *
 * CHANGELOG
 *
 * Added 13.11.2018
 *
 * @param command_name: The name of the command, for which the argument names and default values to those args are
 *                      requested.
 * @param f:            The function, that gets executed, once the server responds with the arguments. The function has
 *                      to have one argument, which will be passed the object, whose property names are the names of
 *                      the command arguments and the values are the default values to those arguments
 */
function getCommandDefaultParameters(command_name, f) {
    jQuery.ajax({
        url:        ajaxURL(),
        type:       'Get',
        timeout:    5000,
        dataType:   'html',
        data:       {
            'action':   'command_default_args',
            'name':     command_name
        },
        success:    function (response) {
            // console.log(response);
            // The response will be a JSON string, representing an associative array, whose keys are the parameter
            // names and the values are the default values for that parameter
            let parameters = JSON.parse(response);

            // Calling the callback function with the parameters object as argument
            f(parameters);
        },
        error:      function (response) {
            console.log(response);
        }
    })
}

/**
 * Given the object, which defines the expected arguments and default values to a command, this function will add a
 * label with the argument name and a text input widget to the container, where the parameters can be typed in by the
 * user.
 *
 * CHANGELOG
 *
 * Added 19.11.2018
 *
 * @param parameters
 */
function displayParameterInputs(parameters) {

    // Clearing all the parameters that are currently being displayed
    jQuery('input.command-parameter').remove();
    jQuery('p.command-parameter-label').remove();
    // 04.12.2018
    // Also removing any possible info messages being displayed in the container
    jQuery('p.command-info').remove();

    // 04.12.2018
    // Here we first check if the parameters object is empty or actually contains parameters.
    // Because in case it does, those parameters obviously will be displayed, but if it doesnt, we will
    // instead display a little message, telling the user, that there are no parameters available for that command
    if (Object.keys(parameters).length) {

        // Adding the new parameters
        Object.keys(parameters).forEach(function (name, index) {
            let value = parameters[name];
            let label_element = jQuery(`<p class="command-parameter-label">${name}</p>`);
            label_element.appendTo(parameter_container);
            let parameter_element = jQuery(`<input class="command-parameter" title="${name}" value="${value}">`);
            parameter_element.appendTo(parameter_container);
        })
    } else {

        // 04.12.2018
        // Displaying the info message, that there are no parameters to this command
        let label_string = "Sorry, there are no parameters available for this command...";
        let label_element = jQuery(`<p class="command-info">${label_string}</p>`);
        // Now we add this label element to the parameter container, where the parameters WOULD HAVE been displayed
        // if there were any
        label_element.appendTo(parameter_container);
    }


}

/**
 * Requests the expected parameters based on the currently selected command name and then displays the according labels
 * and text inputs to the container, so that the user can type in the desired values.
 *
 * CHANGELOG
 *
 * Added 19.11.2018
 */
function updateParameterContainer() {
    let command_name = select.attr('value');
    getCommandDefaultParameters(command_name, function (parameters) {
        displayParameterInputs(parameters);
    });
}

// ALL THE FUNCTIONS FOR THE RECENT COMMANDS DISPLAY

function displayRecentCommands(recent_commands) {
    Object.keys(recent_commands).forEach(function (key, index) {
        let command = recent_commands[key];
        // For each command we create a new HTML element, that contains the information
        let html_string = `
        <p class="recent-command">
            ${command.date}: <strong>${command.name}</strong> was executed. 
            <a href="${command.log}">View the Log!</a>
        </p>
        `;
        let html_element = jQuery(html_string);
        // Adding the element to the container, where it belongs
        html_element.appendTo(recent_command_container);
    })
}