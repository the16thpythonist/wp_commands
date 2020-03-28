
function CommandParameter(name, defaultValue, type, optionalFlag) {
    this.name = name;
    this.default = defaultValue;
    this.type = type;
    this.optional = optionalFlag;
}

function Command(name, parameters) {
    this.name = name;
    this.parameters = parameters;
}

function CommandExecution(name, dateTime, logPath) {
    this.name = name;
    this.time = dateTime;
    this.log = logPath;
}

export default {
    Command: Command,
    CommandParameter: CommandParameter,
    CommandExecution: CommandExecution
}