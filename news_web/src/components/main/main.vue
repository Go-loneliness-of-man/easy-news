
<template>
  <div class="main">
    <div class="mainTop" v-show="mainTopShow">
      <div class="pathList">
        <div class="pathListItem" v-for="(item, index) in pathList" :key="index" @click="pathClick(item.route)">
          <div class="separator">{{index ? '>' : ''}}</div>
          <div :class="{ link: true, active: index === (pathList.length - 1) }">{{item.text}}</div>
        </div>
      </div>
      <div class="search">
        <el-input class="searchBar" v-model="search" placeholder="请输入关键字" prefix-icon="el-icon-search"></el-input>
        <el-button class="searchButton">搜索</el-button>
      </div>
    </div>
    <router-view/>
  </div>
</template>

<script>

export default {

  data() {
    return {
      search: '',
      
    }
  },

  computed: {
    pathList() { return this.$store.state.pathList; },

    // 顶栏是否显示
    mainTopShow() {
      let show = ['/news', '/theme', '/tag', '/user', '/msg'];
      return show.includes(this.$route.path);
    }
  },

  methods: {

    // 点击主栏路径
    pathClick(route) {
      if(this.$route.path !== route)
        this.$router.push(route);
    }
  },

  mounted() {

  }
};

</script>

<style lang="scss">
  @import "main.scss";
</style>
