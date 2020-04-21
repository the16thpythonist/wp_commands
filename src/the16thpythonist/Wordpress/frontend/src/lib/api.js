import command from './command'
import axios from 'axios'

function Ajax(ajaxUrl) {

    this.ajaxUrl = ajaxUrl;

    // PUBLIC METHODS

    this.get = function (name, args, timeout) {
        let params = {...{action:"name"}, ...args};
        return axios.get(this.ajaxUrl, {params: params});
    };

    // PUBLIC METHODS
}

function WpCommandsApi() {

    this.ajax = new Ajax();

    // PROTECTED METHODS

    // PUBLIC METHODS

    this.getRegisteredCommands = function () {
        let promise = this.ajax.get();
        return [];
    };

    this.getCommandParameters = function (commandName) {
        return [];
    };

    this.getRecentCommandExecutions = function () {
        return [];
    };

    this.executeCommand = function (commandName, parameters) {
        return true;
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

    this.recentExecutions = [
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
     * @return {Command[]}
     */
    this.getRegisteredCommands = function () {
        return Object.values(registeredCommands);
    };

    /**
     *
     * CHANGELOG
     *
     * Added 28.03.2020
     *
     * @param commandName
     * @return {*}
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
        return this.recentExecutions;
    };

    /**
     *
     * CHANGELOG
     *
     * Added 28.03.2020
     *
     * @param commandName
     * @param parameters
     * @return {boolean}
     */
    this.executeCommand = function (commandName, parameters) {
        this.recentExecutions.push(new command.CommandExecution(commandName, new Date(), logPath));
        console.log(`Executing command "${commandName}" with parameters: ${JSON.stringify(parameters)}`);
        return true;
    }
}

export default {
    WpCommandsApi: WpCommandsApi,
    WpCommandsApiMock: WpCommandsApiMock
}