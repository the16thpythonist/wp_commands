<template>
  <div id="app">
    <WpCommandsWidget></WpCommandsWidget>
  </div>
</template>

<script>
import WpCommandsWidget from "./components/WpCommandsWidget";

export default {
  name: "App",
  components: {
    WpCommandsWidget
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
  margin-top: 10px;
}
</style>
