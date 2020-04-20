<template>
    <div class="recent-commands">
        <div class="command-execution" v-for="(execution, index) in commandExecutions">
            <span class="execution-time">{{ execution.time }}</span>:
            <span class="execution-name">{{ execution.name }}</span> was executed.
            <a :href="execution.log">View the Log!</a>
        </div>
    </div>
</template>

<script>
    export default {
        name: "RecentCommands",
        props: {
            commandExecutions: Array
        }
    }
</script>

<style scoped>
    .recent-commands {
        padding: 5px;
        background-color: #f5f5f5;
        border-style: solid;
        border-width: 1px;
        border-color: #d7d7d7;
        display: flex;
        flex-direction: column;
    }

    .command-execution {
        text-align: left;
        margin-top: 2px;
        margin-bottom: 2px;
        font-size: 0.9em;
    }

    .execution-name, .execution-log {
        font-size: 1.05em;
        font-weight: bold;
    }

    .execution-log {
        color: #3ECF8E;
    }
</style>