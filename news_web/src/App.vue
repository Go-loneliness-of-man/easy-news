
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
    @include flex(flex-start, flex-start);
    
    background: $bglll;
    flex-direction: column;
  }
</style>
