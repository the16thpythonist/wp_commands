import command from './command'

function Ajax() {

}

function WpCommandsApi() {

    this.ajax = Ajax();

    // PROTECTED METHODS

    // PUBLIC METHODS

    this.getRegisteredCommands = function () {
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

    this.getRegisteredCommands = function () {
        return Object.values(registeredCommands);
    };

    this.getCommandParameters = function (commandName) {
        let keys = Object.keys(registeredCommands);
        if (keys.includes(commandName)) {
            let cmd = registeredCommands[commandName];
            return cmd.parameters;
        } else {
            console.log('Command')
        }
    };

    this.getRecentCommandExecutions = function () {
        return recentExecutions;
    };

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