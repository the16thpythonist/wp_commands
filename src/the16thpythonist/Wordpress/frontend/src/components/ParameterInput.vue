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
        methods: {
            handleInput: function (e) {
                this.$emit('input', this.parameterValues);
            }
        }
    }
</script>

<style scoped>

</style>