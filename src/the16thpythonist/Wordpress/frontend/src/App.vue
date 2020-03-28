<template>
  <div id="app">
    <CommandSelector :commands="commands"></CommandSelector>
    <ParameterInput :command="cmd"></ParameterInput>
  </div>
</template>

<script>
import CommandSelector from "./components/CommandSelector.vue";
import ParameterInput from "./components/ParameterInput";

import command from "./lib/command";

export default {
  name: "App",
  data: function () {
    return {
      commands: [
              new command.Command("test1", []),
              new command.Command("test2", [])
      ],
      cmd: new command.Command("name1", [
              new command.CommandParameter("param1", "default", "string", true),
              new command.CommandParameter("param2", "10", "int", false)
      ])
    }
  },
  components: {
    CommandSelector,
    ParameterInput
  }
};
</script>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
}
</style>
