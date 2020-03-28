/**
 *
 * CHANGELOG
 *
 * Added 28.03.2020
 *
 * @param name
 * @param defaultValue
 * @param type
 * @param optionalFlag
 * @constructor
 */
function CommandParameter(name, defaultValue, type, optionalFlag) {
    this.name = name;
    this.default = defaultValue;
    this.type = type;
    this.optional = optionalFlag;
}

/**
 *
 * CHANGELOG
 *
 * Added 28.03.2020
 *
 * @param name
 * @param parameters
 * @constructor
 */
function Command(name, parameters) {
    this.name = name;
    this.parameters = parameters;
}

/**
 *
 * CHANGELOG
 *
 * Added 28.03.2020
 *
 * @param name
 * @param dateTime
 * @param logPath
 * @constructor
 */
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