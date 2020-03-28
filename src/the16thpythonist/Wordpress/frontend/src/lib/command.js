
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

export default {
    myvar: "Bye",
    Command: Command,
    CommandParameter: CommandParameter
}