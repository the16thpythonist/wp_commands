<template>
    <div class="command-selector">
        <select v-model="selectedCommand">
            <option v-for="(command, index) in commands" :value="command">{{ command.name }}</option>
        </select>
    </div>
</template>

<script>
    export default {
        name: "CommandSelector",
        data: function () {
            return {
                selectedCommand: {}
            };
        },
        props: {
            commands: Array
        }
    }
</script>

<style scoped>

</style>