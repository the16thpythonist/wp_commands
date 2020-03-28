<template>
    <div class="command-widget">
        <CommandSelector :commands="commands" v-model="selectedCommand"></CommandSelector>
        <ParameterInput :command="selectedCommand" v-model="commandParameters"></ParameterInput>
        <button @click.prevent="onExecute">Execute</button>
        <RecentCommands :command-executions="recentExecutions"></RecentCommands>
    </div>
</template>

<script>
    import CommandSelector from "./CommandSelector";
    import ParameterInput from "./ParameterInput";
    import RecentCommands from "./RecentCommands";

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
                api: new api.WpCommandsApiMock(),
                commands: [],
                selectedCommand: {},
                commandParameters: {},
                recentExecutions: []
            }
        },
        methods: {
            checkParameters: function() {
                return true
            },
            onExecute: function () {
                if (this.checkParameters()) {
                    this.api.executeCommand(
                        this.selectedCommand.name,
                        this.commandParameters
                    );
                }
            }
        },
        created: function() {
            // Call the Api to get the commands
            this.commands = this.api.getRegisteredCommands();
            this.recentExecutions = this.api.getRecentCommandExecutions();
        }
    }
</script>

<style scoped>

</style>