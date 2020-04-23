import command from './command'
import axios from 'axios'
import he from "he"

/**
 * This is a wrapper class to simplify calling ajax actions on the wordpress backend.
 *
 * BACKGROUND
 *
 * With wordpress executing custom ajax actions works a little bit differently: All the requests have to be sent to one
 * common url. Which callback is being executed simply depends on the additional "action" parameter passed with the GET
 * request.
 *
 * EXAMPLE
 *
 * Consider the following example, where you want to execute the ajax callback function "set_count" on the server with
 * the argument "count" set to 10. The server will return a boolean value of whether or not the process was successful.
 * As soon as the response has arrived you want to display it in an alert.
 *
 * ```javascript
 * let ajax = new Ajax();
 * let promise = ajax.get('set_count', {'count': 10});
 * promise.then(function(result) {
 *     alert(result);
 * });
 * ```
 *
 * CHANGELOG
 *
 * Added 19.04.2020
 *
 * @constructor
 */
function Ajax() {

    // The "SERVER" variable is dynamically created by the server and injected into the frontend code. It is an object
    // which contains data fields with information, which the server makes available to the JS code.
    // This includes for example the URL string for the address which has to be querried to execute ajax actions on
    // the wordpress server.
    this.ajaxUrl = SERVER.ajaxUrl;

    // PUBLIC METHODS

    /**
     * Sends a GET request to the wordpress server, which will execute the callback with the given name
     *
     * The parameters given by "args" will be passed to the callback call as GET parameters
     *
     * CHANGELOG
     *
     * Added 19.04.2020
     *
     * Changed 22.04.2020
     * Previously I though the result of the axios promise would simply be the string returned from the server, but as
     * it turned out it is an object, which already contains the decoded JSON as the "data" field. So now a promise is
     * returned, which will yield this decoded JSON object as the result.
     *
     * @param name
     * @param args
     * @param timeout
     * @return {Promise<T>}
     */
    this.get = function (name, args, timeout=1000) {
        let params = {...{action:name}, ...args};
        return axios.get(this.ajaxUrl, {params: params}).then(function (result) {
            console.log(result);
            return result.data;
        }).catch(function (error) {
            console.log(error);
        });
    };

    // PUBLIC METHODS
}

/**
 * This is a wrapper class for requesting relevant information from the WpCommands backend.
 *
 * BACKGROUND
 *
 * CHANGELOG
 *
 * Added 22.04.2020
 *
 * @constructor
 */
function WpCommandsApi() {

    const ajax = new Ajax();

    // PROTECTED METHODS

    /**
     * Returns a promise, which results in an array of strings, which are the names of all the available commands.
     *
     * CHANGELOG
     *
     * Added 22.04.2020
     *
     * @return {Promise<T>}
     */
    function getRegisteredCommandNames() {
        let get_promise = ajax.get('get_registered_command_names', {});
        return get_promise.then(function (result) {
            return result;
        })
    }

    /**
     * Returns a promise, which results in an object, whose values are objects which contain info about the parameters
     * of the given command_name
     *
     * CHANGELOG
     *
     * Added 22.04.2020
     *
     * @param command_name
     * @return {Promise<T>}
     */
    function getCommandParameterExtendedInfo(command_name) {
        let get_promise = ajax.get('get_command_parameter_extended_info', {'name': command_name});
        return get_promise.then(function (result) {
            return result;
        })
    }

    /**
     * Returns a promise, which results in an array of CommandParameter objects, that describe the parameters to the
     * given command_name
     *
     * CHANGELOG
     *
     * Added 22.04.2020
     *
     * @param command_name
     * @return {Promise<[]>}
     */
    function getCommandParameters(command_name) {
        let command_parameter_info_promise = getCommandParameterExtendedInfo(command_name);
        return command_parameter_info_promise.then(function (parameter_info) {
            let parameters = [];
            for (let [name, info] of Object.entries(parameter_info)) {
                let _parameter = new command.CommandParameter(
                    info.name,
                    info.default,
                    info.type,
                    info.optional
                );
                console.log(_parameter);
                parameters.push(_parameter);
            }
            return parameters;
        })
    }

    // PUBLIC METHODS

    /**
     * Returns a promise, which results in an array of Command objects, describing all the available commands to be ex.
     *
     * CHANGELOG
     *
     * Added 22.04.2020
     *
     * @return {Promise<[]>}
     */
    this.getRegisteredCommands = function () {
        let command_names_promise = getRegisteredCommandNames();
        return command_names_promise.then(function (_command_names) {
            let command_names = _command_names;
            let parameter_promises = [];
            command_names.forEach(function (command_name) {
                let parameter_promise = getCommandParameters(command_name);
                parameter_promises.push(parameter_promise);
            });

            return Promise.all(parameter_promises).then(function (parameters) {
                let commands = [];
                command_names.forEach(function (name, index) {
                    let _command = new command.Command(name, parameters[index]);
                    commands.push(_command);
                });
                return commands;
            });
        });

    };

    /**
     * Returns a promise, which results in an array of CommandExecution objects, each describing one of the recently
     * executed commands by name, date of execution and the log url
     *
     * CHANGELOG
     *
     * Added 22.04.2020
     *
     * @return {Promise<[]>}
     */
    this.getRecentCommandExecutions = function () {
        let get_promise = ajax.get('get_recent_commands', {'amount': 5});
        return get_promise.then(function (results) {
            let executions = [];
            for (let result of results) {
                // STRING DECODING:
                // So one of the problems, that occurred with this function is that using the string from "result.log"
                // directly did not work, because the special characters were still URL encoded. I think this is because
                // the json parsing is done implicitly within axios somewhere it just handles strings like this.
                // The solution is the line "he.decode()" this function will decode the URL encoding and make it work
                // as an actual string. This is the purpose of the "he" library.
                let _execution = new command.CommandExecution(
                    result.name,
                    new Date(result.date),
                    he.decode(result.log)
                );
                executions.push(_execution);
            }
            return executions;
        })
    };

    /**
     * Returns a promise, which results in a boolean value indicating if the execution of the command on the server
     * was successful or not.
     *
     * Given a command name and the parameters for the commands execution, this function will send a GET request to
     * the server issuing it to execute the command.
     *
     * CHANGELOG
     *
     * Added 22.04.2020
     *
     * @param commandName
     * @param parameters
     * @return {Promise<T>}
     */
    this.executeCommand = function (commandName, parameters) {
        return ajax.get(commandName, parameters);
    }
}

/**
 *
 * CHANGELOG
 *
 * Added 28.03.2020
 *
 * @constructor
 */
function WpCommandsApiMock() {

    const registeredCommands = {
        'background-task1': new command.Command('background-task1', [
            new command.CommandParameter('param1', '', 'string', false),
            new command.CommandParameter('param2', '', 'int', false),
            new command.CommandParameter('param3', 'hello,world,!', 'csv', true)
        ]),
        'background-task2': new command.Command('background-task2', [
            new command.CommandParameter('count', '10', 'int', true)
        ])
    };

    const logPath = 'https://google.de';

    const recentExecutions = [
        new command.CommandExecution('background-task1', new Date(), logPath),
        new command.CommandExecution('background-task2', new Date(), logPath),
        new command.CommandExecution('background-task1', new Date(), logPath)
    ];

    // PROTECTED METHODS

    // PUBLIC METHODS

    /**
     * Returns a list of "Command" objects, which represent all the available commands to be executed on the server.
     *
     * CHANGELOG
     *
     * Added 28.03.2020
     *
     * Changed 22.04.2020
     * Made this function return a promise instead of directly returning the value
     *
     * @return
     */
    this.getRegisteredCommands = function () {
        return new Promise(function (resolve, reject) {
            resolve(Object.values(registeredCommands));
        });
    };

    /**
     *
     * CHANGELOG
     *
     * Added 28.03.2020
     *
     * Changed 22.04.2020
     * Made this function return a promise instead of directly returning the value
     *
     * @param commandName
     * @return
     */
    this.getCommandParameters = function (commandName) {
        let keys = Object.keys(registeredCommands);
        if (keys.includes(commandName)) {
            let cmd = registeredCommands[commandName];
            return cmd.parameters;
        } else {
            console.log('Command')
        }
    };

    /**
     * Returns an array of "CommandExecution" objects, each defining a command that has been recently executed.
     *
     * CHANGELOG
     *
     * Added 28.03.2020
     *
     * Changed 19.04.2020
     * Contained a bug, where the "this" was missing before the "recentExecutions" and thus it was not accessing the
     * object field, but causing a reference error
     *
     * Changed 22.04.2020
     * Made this function return a promise instead of directly returning the value
     *
     * @return [CommandExecution]
     */
    this.getRecentCommandExecutions = function () {
        return new Promise(function (resolve, reject) {
            resolve(recentExecutions);
        });
    };

    /**
     * Sends a request to the server to execute the command with the given name, using the given parameters.
     *
     * CHANGELOG
     *
     * Added 28.03.2020
     *
     * Changed 22.04.2020
     * Made this function return a promise instead of directly returning the value
     *
     * @param commandName
     * @param parameters
     * @return
     */
    this.executeCommand = function (commandName, parameters) {
        return new Promise(function (resolve, reject) {
            recentExecutions.push(new command.CommandExecution(commandName, new Date(), logPath));
            console.log(`Executing command "${commandName}" with parameters: ${JSON.stringify(parameters)}`);
            resolve(true);
        });

    }
}

export default {
    WpCommandsApi: WpCommandsApi,
    WpCommandsApiMock: WpCommandsApiMock
}