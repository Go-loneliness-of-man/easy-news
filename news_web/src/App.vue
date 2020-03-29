
<template>
  <div id="app">
    <router-view name="top"/>
    <router-view name="main"/>
  </div>
</template>

<script>

export default {
  name: 'App',
}
</script>

<style lang="scss">
  @import "./assets/scss/importScss.scss";

  #app {
    @include wh(calc(100%), calc(100%));
    background: $bglll;
  }
</style>
