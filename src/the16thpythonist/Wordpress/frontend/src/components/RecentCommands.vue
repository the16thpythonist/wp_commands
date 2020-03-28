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

</style>