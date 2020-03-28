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
                    :placeholder="parameter.default">
            <input
                    v-else
                    type="text"
                    v-model="parameterValues[parameter.name]"
                    :id="parameter.name"
                    :name="parameter.name">
        </div>
    </div>
</template>

<script>
    export default {
        name: "ParameterInput",
        data: function () {
            return {
                parameterValues: {}
            }
        },
        props: {
            command: Object
        },
        computed: {
            parameters: function () {
                return this.command.parameters;
            }
        }
    }
</script>

<style scoped>

</style>