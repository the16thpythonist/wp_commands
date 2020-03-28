<template>
    <div class="command-widget">
        <CommandSelector :commands="commands" v-model="selectedCommand"></CommandSelector>
        <ParameterInput :command="selectedCommand"></ParameterInput>
        <button @click.prevent="onExecute">Execute</button>
        <RecentCommands :command-executions="commandExecutions"></RecentCommands>
    </div>
</template>

<script>
    import CommandSelector from "./CommandSelector";
    import ParameterInput from "./ParameterInput";
    import RecentCommands from "./RecentCommands";

    export default {
        name: "WpCommandsWidget",
        components: {
            CommandSelector,
            ParameterInput,
            RecentCommands
        },
        data: function () {
            return {
                commands: [],
                selectedCommand: {},
                commandExecutions: []
            }
        },
        methods: {
            onExecute: function () {
                // check correctness and then...
                // Submit the whole thing to the server
            }
        }
    }
</script>

<style scoped>

</style>