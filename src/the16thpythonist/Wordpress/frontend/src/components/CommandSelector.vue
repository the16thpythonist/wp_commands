<template>
    <div class="command-selector">
        <select v-model="selectedCommand" @input="handleInput">
            <!--
            By binding the value to the command object here, we can make sure that the selectedCommand, which has been
            v-model bound to the whole select element will also contain the complete objects.
            -->
            <option v-for="(command, index) in commands" :value="command">{{ command.name }}</option>
        </select>
    </div>
</template>

<script>
    export default {
        name: "CommandSelector",
        props: {
            // This value property is supposed to be an initial value for the selectedCommand. It may also be an
            // empty object, which will then be an empty selection.
            // This value property has to be used to support the usage of v-model regarding the selected command
            // of this component
            value: Object,
            // This is the array, which will have to contain all "Command" objects, which are supposed to make up all
            // the options from which this component offers the selection.
            commands: Array
        },
        data: function () {
            return {
                // This will contain the "Command" object, which is currently selected by the user using the select
                // element.
                selectedCommand: this.value
            };
        },
        methods: {
            /**
             * Callback for the input event on the selection element
             *
             * This method is getting invoked every time the input on the selection element changes. And what this
             * method does in turn is to also emit an "input" event, passing the selectedCommand object along as a
             * parameter.
             * This is done to support the usage of v-model regarding the selected command object of this component
             *
             * CHANGELOG
             *
             * Added 28.02.2020
             *
             * @param e
             */
            handleInput: function (e) {
                this.$emit('input', this.selectedCommand);
            }
        }
    }
</script>

<style scoped>

</style>