<template>
    <div class="command-selector">
        <select v-model="selectedCommand">
            <!--
            By binding the value to the command object here, we can make sure that the selectedCommand, which has been
            v-model bound to the whole select element will also contain the complete objects.

            Changed 19.04.2020
            Added ':selected="index === 1"', this will set the selected property for the first option in the list.
            This is important to have the first item on default selected when the page loads!
            -->
            <option v-for="(command, index) in commands" :selected="index === 1" :value="command">{{ command.name }}</option>
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
                // Changed 19.04.2020
                // This is now set to a shallow copy of the "value" property instead of the property directly.
                selectedCommand: {...this.value}
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
             * @deprecated
             *
             * CHANGELOG
             *
             * Added 28.02.2020
             *
             * Deprecated 19.04.2020
             * So it turns out, that realizing the custom "v-model" behaviour using the @input is not gonna cut it as
             * that results in a delay of entered data. Instead the input event is now emitted from within a watcher
             * for the actual data field.
             *
             * @param e
             */
            handleInput: function (e) {
                console.log(this.arguments[0]);
                this.$emit('input', this.selectedCommand);
            }
        },
        watch: {
            // This is the watcher for the "selectedCommand" data field
            selectedCommand: {
                deep: true,
                /**
                 * This function is being called every time the "selectedCommand" variable changes
                 *
                 * This function will emit the "input" event with the new value of the "selectedCommand" variable to
                 * support the custom v-model behaviour of this component
                 *
                 * CHANGELOG
                 *
                 * Added 19.04.2020
                 *
                 * @param value The new value for the selectedCommand variable
                 */
                handler: function (value) {
                    this.$emit('input', value);
                }
            }
        }
    }
</script>

<style scoped>

</style>