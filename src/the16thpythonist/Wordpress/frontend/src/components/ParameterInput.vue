<template>
    <div class="command-parameters">
        <div v-show="parameters.length > 0" class="command-parameter-input" v-for="(parameter, index) in parameters">
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
                    :placeholder="parameter.default">
            <input
                    v-else
                    type="text"
                    v-model="parameterValues[parameter.name]"
                    :id="parameter.name"
                    :name="parameter.name">
        </div>
        <!-- 20.04.2020: This is a text message indicating that no parameters are required if the list of
         parameters is empty -->
        <div class="command-parameter-input" v-show="parameters.length === 0">
            <div class="command-parameter-type">
                This command does not accept parameters...
            </div>
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
            // Changed 19.04.2020
            // The initialization of the command was moved here.
            // The values start out as a shallow copy of the value property and then get populated with the default
            // values from the parameter specification.
            let values = {...this.value};

            // actually returning the object with the data variables
            return {
                parameterValues: values
            }
        },
        computed: {
            parameters: function () {
                return this.command.parameters;
            }
        },
        watch: {
            /**
             * This function gets called every time the "command" variable changes.
             *
             * If the command variable changes, that means that another command with other parameters has been selected
             * to not cause interference with the parameters of the previous command, this function first resets the
             * state variable "parameterValues" and then populates it with the default variables of the new command.
             *
             * CHANGELOG
             *
             * Added 29.03.2020
             */
            command: function (newCommand, oldCommand) {
                if (newCommand !== oldCommand) {
                    this.parameterValues = {};
                    this.fillDefaultValues();
                }
            },
            parameterValues: {
                deep: true,
                handler: function (value) {
                    this.$emit('input', value);
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
             *
             * Changed 19.04.2020
             * This function contained a logic error. previously the whole parameterValues variable would be overwritten
             * with the default value instead of just the value of the key. Added the indexing with the parameter name
             * now.
             */
            fillDefaultValues: function() {
                this.parameters.forEach((parameter) => {
                    if (parameter.optional) {
                        this.parameterValues[parameter.name] = parameter.default;
                    }
                });
            }
        },
        /**
         * This is the lifecycle hook for when the component is being created.
         *
         * @deprecated
         *
         * CHANGELOG
         *
         * Added 29.03.2020
         *
         * Deprecated 19.04.2020
         * So using the lifecycle hook turned out to be a bad solution. The better solution for doing initialization
         * would be to do it within the very data function.
         */
        created: function () {
            this.fillDefaultValues();
        }
    }
</script>

<style scoped>
    .command-parameters {
        padding: 5px;
        background-color: #f5f5f5;
        border-style: solid;
        border-width: 1px;
        border-color: #d7d7d7;
        display: flex;
        flex-direction: column;
    }

    .command-parameter-input {
        display: flex;
        flex-direction: column;
        margin-bottom: 5px;
        margin-top: 5px;
    }

    .command-parameter-input input {
        width: 100%;
    }

    .command-parameter-info {
        margin-bottom: 2px;
        display: flex;
        flex-direction: row;
        justify-content: space-between;
    }

    .command-parameter-info label {
        margin-left: 3px;
        font-size: 1.15em;
        font-style: normal;
    }

    .command-parameter-type {
        font-size: 0.85em;
        color: #919191;
    }
</style>