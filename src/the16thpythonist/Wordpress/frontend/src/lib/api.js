import command from './command'
import axios from 'axios'
import he from "he"

function Ajax() {

    this.ajaxUrl = SERVER.ajaxUrl;

    // PUBLIC METHODS

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

function WpCommandsApi() {

    const ajax = new Ajax();

    // PROTECTED METHODS

    function getRegisteredCommandNames() {
        let get_promise = ajax.get('get_registered_command_names', {});
        return get_promise.then(function (result) {
            return result;
        })
    }

    function getCommandParameterExtendedInfo(command_name) {
        let get_promise = ajax.get('get_command_parameter_extended_info', {'name': command_name});
        return get_promise.then(function (result) {
            return result;
        })
    }

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

    this.getRecentCommandExecutions = function () {
        let get_promise = ajax.get('get_recent_commands', {'amount': 5});
        return get_promise.then(function (results) {
            let executions = [];
            for (let result of results) {
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