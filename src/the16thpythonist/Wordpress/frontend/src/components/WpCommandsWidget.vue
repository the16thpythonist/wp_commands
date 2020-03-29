<template>
    <div class="command-widget">
        <!--
        v-model of this component is bound to the currently selected Command object
        -->
        <CommandSelector :commands="commands" v-model="selectedCommand"></CommandSelector>

        <!--
        v-model is bound to the object, which describes the parameter values with the parameter names being the keys
        and the values being the values directly from the input.
        -->
        <ParameterInput :command="selectedCommand" v-model="commandParameters"></ParameterInput>

        <button @click.prevent="onExecute">Execute</button>
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
                api: new api.WpCommandsApiMock(),
                // This array will hold the list of "Command" objects. Each of these objects will represent a command
                // which has been registered and which can be executed.
                commands: [],
                // This object will be the command, that has been selected by the user using the CommandSelector.
                selectedCommand: {},
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
             * @return {boolean}
             */
            checkParameters: function() {
                // We are not going to validate the parameters for their type here or something. All though it would be
                // nice idea to do that at some point...

                // Simply going to check if all the parameters have been put in.
                return this.commandParameters.length === this.selectedCommand.parameters.length
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
                this.recentExecutions = this.api.getRecentCommandExecutions();
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
                    );

                    // After the command has been started we obviously want it to appear in the recent commands
                    this.updateRecentCommands();
                }
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
             * CHANGELOG
             *
             * Added 29.03.2020
             */
            selectedCommand: function (newCommand, oldCommand) {
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
         */
        created: function() {
            // Call the Api to get the commands
            this.commands = this.api.getRegisteredCommands();
            this.updateRecentCommands();
        }
    }
</script>

<style scoped>

</style>