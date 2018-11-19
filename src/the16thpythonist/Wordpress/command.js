// The container, which contains the input boxes for the parameters
var parameter_container = jQuery('div#parameter-container');

// The selection widget, where the commands can be chosen from
var select = jQuery('select#command-name');

/**
 * Executes the background command with the given name on the server
 *
 * CHANGELOG
 *
 * Added 13.11.2018
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
    // TODO: add the command log link to the list immediately
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

    // Adding the new parameters
    Object.keys(parameters).forEach(function (name, index) {
        let value = parameters[name];
        let label_element = jQuery(`<p class="command-parameter-label">${name}</p>`);
        label_element.appendTo(parameter_container);
        let parameter_element = jQuery(`<input class="command-parameter" title="${name}" value="${value}">`);
        parameter_element.appendTo(parameter_container);
    })

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
