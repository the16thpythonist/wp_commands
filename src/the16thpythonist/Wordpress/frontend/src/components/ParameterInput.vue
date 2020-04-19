<template>
    <div class="command-parameters">
        <div class="command-parameter-input" v-for="(parameter, index) in parameters">
            <div class="command-parameter-info">
                <label
                        class="command-parameter-name"
                        :for="parameter.name">
                    {{ parameter.name }}
                </label>
                <div class="command-parameter-type">Type: {{ parameter.type }}</div>
            </div>
            <input
                    v-if="parameter.optional"
                    type="text"
                    v-model="parameterValues[parameter.name]"
                    :id="parameter.name"
                    :name="parameter.name"
                    :placeholder="parameter.default"
                    @input="handleInput">
            <input
                    v-else
                    type="text"
                    v-model="parameterValues[parameter.name]"
                    :id="parameter.name"
                    :name="parameter.name"
                    @input="handleInput">
        </div>
    </div>
</template>

<script>
    export default {
        name: "ParameterInput",
        props: {
            value: Object,
            command: Object
        },
        data: function () {
            return {
                parameterValues: this.value
            }
        },
        computed: {
            parameters: function () {
                return this.command.parameters;
            }
        },
        watch: {
            /**
             * CHANGELOG
             *
             * Added 29.03.2020
             */
            command: function (newCommand, oldCommand) {
                if (newCommand !== oldCommand) {
                    this.fillDefaultValues();
                }
            }
        },
        methods: {
            /**
             * Callback for the "input" event on all the input fields of the component.
             *
             * This method is being called every time ANY(!) one of the input fields for the parameter has its value
             * changed by the user. This method in turn then also emits an "input" event to be seen by the parent
             * component. Passing the parameterValues as a parameter to the event.
             * This functionality is required to support the v-model functionality with the parameter values of this
             * component.
             *
             * CHANGELOG
             *
             * Added 28.03.2020
             */
            handleInput: function (e) {
                this.$emit('input', this.parameterValues);
            },
            /**
             * Sets the parameter values for all optional parameters to their supplied default values.
             *
             * This function will iterate through all the of the parameters of the subject command.
             *
             * CHANGELOG
             *
             * Added 29.03.2020
             */
            fillDefaultValues: function() {
                this.parameters.forEach((parameter) => {
                    if (parameter.optional) {
                        this.parameterValues = parameter.default;
                    }
                });
            }
        },
        /**
         *
         *
         * CHANGELOG
         *
         * Added 29.03.2020
         */
        created: function () {
            this.fillDefaultValues();
        }
    }
</script>

<style scoped>

</style>