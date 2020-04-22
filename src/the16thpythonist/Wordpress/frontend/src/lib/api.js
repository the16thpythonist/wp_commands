import command from './command'
import axios from 'axios'

function Ajax(ajaxUrl) {

    this.ajaxUrl = ajaxUrl;

    // PUBLIC METHODS

    this.get = function (name, args, timeout=1000) {
        let params = {...{action:"name"}, ...args};
        return axios.get(this.ajaxUrl, {params: params});
    };

    // PUBLIC METHODS
}

function WpCommandsApi() {

    const ajax = new Ajax();

    // PROTECTED METHODS

    function getRegisteredCommandNames() {
        let get_promise = ajax.get('get_registered_command_names', {});
        return get_promise.then(function (result) {
            return JSON.parse(result);
        })
    }

    function getCommandParameterExtendedInfo(command_name) {
        let get_promise = ajax.get('get_command_parameter_extended_info', {'name': command_name});
        return get_promise.then(function (result) {
            return JSON.parse(result);
        })
    }

    function getCommandParameters(command_name) {
        let command_parameter_info_promise = getCommandParameterExtendedInfo(command_name);
        return command_parameter_info_promise.then(function (parameter_info) {
            return command.CommandParameter(
                parameter_info['name'],
                parameter_info['default'],
                parameter_info['type'],
                parameter_info['optional']
            )
        })
    }

    // PUBLIC METHODS

    this.getRegisteredCommands = function () {
        let command_names_promise = getRegisteredCommandNames();
        let commands_promise = command_names_promise.then(function (command_names) {
            let parameter_promises = [];
            command_names.forEach(function (command_name) {
                let parameter_promise = getCommandParameterExtendedInfo(command_name);
                parameter_promises.push(parameter_promise);
            });

            return Promise.all(parameter_promises).then(function (parameters) {
                let commands = [];
                command_names.forEach(function (command_name, index) {
                    let _command_parameters = parameters[index];
                    let _command = command.Command(command_name, _command_parameters);
                    commands.push(_command);
                });
                return commands;
            })
        })
    };

    this.getCommandParameters = function (commandName) {
        return [];
    };

    this.getRecentCommandExecutions = function () {
        let get_promise = ajax.get('get_recent_commands', {'amount': 5});
        return get_promise.then(function (result) {
            let _object = JSON.parse(result);

            return command.CommandExecution(
                _object['name'],
                new Date(_object['date']),
                _object['log']
            );
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
     *
     * CHANGELOG
     *
     * Added 28.03.2020
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
     *
     * CHANGELOG
     *
     * Added 28.03.2020
     *
     * Changed 19.04.2020
     * Contained a bug, where the "this" was missing before the "recentExecutions" and thus it was not accessing the
     * object field, but causing a reference error
     *
     * @return [CommandExecution]
     */
    this.getRecentCommandExecutions = function () {
        return new Promise(function (resolve, reject) {
            resolve(recentExecutions);
        });
    };

    /**
     *
     * CHANGELOG
     *
     * Added 28.03.2020
     *
     * @param commandName
     * @param parameters
     * @return
     */
    this.executeCommand = function (commandName, parameters) {
        return new Promise(function (resolve, reject) {
            this.recentExecutions.push(new command.CommandExecution(commandName, new Date(), logPath));
            console.log(`Executing command "${commandName}" with parameters: ${JSON.stringify(parameters)}`);
            resolve(true);
        });

    }
}

export default {
    WpCommandsApi: WpCommandsApi,
    WpCommandsApiMock: WpCommandsApiMock
}