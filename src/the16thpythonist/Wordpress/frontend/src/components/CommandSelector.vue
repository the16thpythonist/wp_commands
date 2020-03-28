<template>
    <div class="command-selector">
        <select v-model="selectedCommand" @input="handleInput">
            <option v-for="(command, index) in commands" :value="command">{{ command.name }}</option>
        </select>
    </div>
</template>

<script>
    export default {
        name: "CommandSelector",
        props: {
            value: Object,
            commands: Array
        },
        data: function () {
            return {
                selectedCommand: this.value
            };
        },
        methods: {
            handleInput: function (e) {
                this.$emit('input', this.selectedCommand);
            }
        }
    }
</script>

<style scoped>

</style>