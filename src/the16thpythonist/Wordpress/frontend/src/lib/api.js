
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
}

export default {
    WpCommandsApi: WpCommandsApi,
}