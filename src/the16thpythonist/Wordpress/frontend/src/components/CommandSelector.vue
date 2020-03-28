<template>
    <div class="command-selector">
        <option v-for="(command, index) in commands" :value="command.name">{{ command.name }}</option>
    </div>
</template>

<script>
    import Command from '@/lib/command';

    export default {
        name: "CommandSelector",
        data: function () {
            return {
                commandChoice: {}
            };
        },
        props: {
            commands: Array
        }
    }
</script>

<style scoped>

</style>