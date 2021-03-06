<template>
    <div class="command-widget">
        <p class="command-widget-info">
            <em>Select</em> a command, <em>enter parameters</em> and <em>execute</em> it by pressing the button!
        </p>
        <!--
        v-model of this component is bound to the currently selected Command object
        -->
        <CommandSelector :commands="commands" v-model="selectedCommand"></CommandSelector>

        <!--
        v-model is bound to the object, which describes the parameter values with the parameter names being the keys
        and the values being the values directly from the input.
        -->
        <ParameterInput :command="selectedCommand" v-model="commandParameters"></ParameterInput>

        <button class="execute-button" @click.prevent="onExecute">Run Command!</button>
        <RecentCommands :command-executions="recentExecutions"></RecentCommands>
    </div>
</template>

<script>
    import CommandSelector from "./CommandSelector";
    import ParameterInput from "./ParameterInput";
    import RecentCommands from "./RecentCommands";

    // This is a utility module, which contains the classes for interfacing with the server side wordpress api
    import api from "../lib/api";

    export default {
        name: "WpCommandsWidget",
        components: {
            CommandSelector,
            ParameterInput,
            RecentCommands
        },
        data: function () {
            return {
                // The Api object is used to interface with the server. In its methods it sends the appropriate AJAX
                // requests to the endpoints of the wordpress server to retrieve information such as all the
                // registered commands, command parameters etc.
                api: new api.WpCommandsApi(),
                // This array will hold the list of "Command" objects. Each of these objects will represent a command
                // which has been registered and which can be executed.
                commands: [],
                // This object will be the command, that has been selected by the user using the CommandSelector.
                selectedCommand: {parameters: []},
                // This object will store the information about the parameter value, which the user has put in for the
                // selected command. The keys of the object will be the parameter names and the values will be the
                // values from the input fields.
                commandParameters: {},
                // This array will contain "CommandExecution" objects. These objects describe the name and time of a
                // command that was executed in the past.
                recentExecutions: []
            }
        },
        methods: {
            /**
             * Will check the commandParameters, if they are valid to be passed for the command call
             *
             * CHANGELOG
             *
             * Added 28.03.2020
             *
             * Changed 19.04.2020
             * Using the commandParametersLength computed property now, because the statement required to get the
             * "length" of an object was too messy...
             *
             * @return {boolean}
             */
            checkParameters: function() {
                // We are not going to validate the parameters for their type here or something. All though it would be
                // nice idea to do that at some point...

                // Simply going to check if all the parameters have been put in.
                return this.commandParametersLength === this.selectedCommand.parameters.length
            },
            /**
             * Sets the "commandParameters" to an empty object again
             *
             * CHANGELOG
             *
             * Added 28.03.2020
             */
            clearParameters: function() {
                this.commandParameters = {};
            },
            /**
             * Updates the display of the recent commands
             *
             * Sends a request to the wordpress server api to retrieve a list if the most recent commands, that have
             * been executed.
             *
             * CHANGELOG
             *
             * Added 29.03.2020
             */
            updateRecentCommands: function() {
                let _this = this;

                this.api.getRecentCommandExecutions().then(function (recentExecutions) {
                    _this.recentExecutions = recentExecutions;
                })
                // this.recentExecutions = this.api.getRecentCommandExecutions();
            },
            /**
             * Callback function for the "Execute" button
             *
             * CHANGELOG
             *
             * Added 28.03.2020
             */
            onExecute: function () {
                if (this.checkParameters()) {
                    this.api.executeCommand(
                        this.selectedCommand.name,
                        this.commandParameters
                    ).then(function (value) {
                        console.log(value);
                    });

                    // After the command has been started we obviously want it to appear in the recent commands
                    setTimeout(this.updateRecentCommands, 1000);
                }
            },
            /**
             * Initializes the component state from the values requested from the server.
             *
             * This function makes requests to the server to obtain the information about all the available commands
             * and the recent history of executed commands. These values are then assigned to the data fields of the
             * component, thus displaying them on the page.
             *
             * CHANGELOG
             *
             * Added 22.04.2020
             *
             */
            initialize: function() {
                let _this = this;

                this.api.getRegisteredCommands().then(function (commands) {
                    _this.commands = commands;
                    _this.selectedCommand = commands[0];
                });

                this.api.getRecentCommandExecutions().then(function (recentExecutions) {
                    _this.recentExecutions = recentExecutions;
                });
            }
        },
        computed: {
            /**
             * Returns the amount of parameters that have been given for the command.
             *
             * CHANGELOG
             *
             * Added 19.04.2020
             */
            commandParametersLength: function () {
                return Object.keys(this.commandParameters).length;
            }
        },
        watch: {
            /**
             * Watcher for the "selectedCommand" data field
             *
             * This method will get invoked every time the selectedCommand changes. We use this to clear out any
             * current input from previous parameters.
             * Consider the following case: You choose a command and input some parameters, but then right before
             * hitting execute, you would decide to do a different command. You fill out the parameters there and hit
             * execute. If the parameters of the two commands are named differently. The internal object
             * "commandParameters" in this vue component would still also contain the parameters of the first command
             * and would thus pass additional parameter values to the server backend to be executed, potentially
             * causing problems there.
             *
             * @deprecated
             *
             * CHANGELOG
             *
             * Added 29.03.2020
             *
             * Deprecated 19.04.2020
             * This functionality is now being handled by the ParameterInput component itself.
             */
            selectedCommand: function (newCommand, oldCommand) {
                return;
                if (newCommand !== oldCommand) {
                    // This function will assign an empty object to commandParameters.
                    this.clearParameters();
                }
            }
        },
        /**
         * The lifecycle hook for after the component has been created
         *
         * This method will send two requests to the API:
         * 1) One request to obtain the list of all the registered commands, so that this list of commands can be used
         * to display the options for the CommandSelector component
         * 2) A list if all the recent CommandExecutions, so that this list can be supplied to the recent commands
         * widget.
         *
         * CHANGELOG
         *
         * Added 28.03.2020
         *
         * Deprecated 19.04.2020
         * So it turns out, that using the lifecycle hooks for initializing data is not the best idea. This should
         * rather be done in the data function itself.
         *
         * Changed 22.04.2020
         * un-deprecated this method because I changed the api object, which interfaces with the server to use promises.
         * And since the promises dont return values instantly, but rather whenever the server answers they could not
         * be used in the "data" function (because the strictly speaking the data fields are not known as references
         * at that point).
         */
        mounted: function() {
            this.initialize();
        }
    }
</script>

<style scoped>
    .command-widget {
        display: flex;
        flex-direction: column;
    }

    .command-widget>*{
        margin-top: 5px;
        margin-bottom: 5px;
    }

    .execute-button {
        align-self: center;
        margin-top: 8px;
        margin-bottom: 20px;
        padding-top: 10px;
        padding-bottom: 10px;
        padding-left: 25px;
        padding-right: 25px;
        font-weight: bold;
        font-size: 1.4em;
        letter-spacing: 0px;
        background-color: #3ECF8E;
        box-shadow: 0px 3px 10px 0px #c3c3c3;
        color: white;
        border-style: none;
        border-radius: 2px;
    }

    .execute-button:hover {
        background-color: #3fdd9b;
    }

    .command-widget-info {
        font-size: 1.2em;
        text-align: left;
    }
</style>