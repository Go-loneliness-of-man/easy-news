
<template>
  <div class="top">
    <div class="logo"><img src="/static/img/logo.JPG"></div>
    <div class="topList">
      <div v-for="(item, index) in topList" :key="index" :class="{ topListItem: true, active: topItem === item.name }" @click="switchMain(item.name)">
        <div class="icon" v-html="item.svg"></div>
        <div class="text"><span>{{item.text}}</span></div>
      </div>
    </div>
  </div>
</template>

<script>

import create from '../../assets/svg/create.js';
import msg from '../../assets/svg/msg.js';
import news from '../../assets/svg/news.js';
import theme from '../../assets/svg/theme.js';
import tag from '../../assets/svg/tag.js';
import user from '../../assets/svg/user.js';

export default {

  data() {
    return {
      topList: [ // 顶部按钮
        { svg: create, text: '添加', name: 'create' },
        { svg: news, text: '新闻', name: 'news' },
        { svg: theme, text: '专题', name: 'theme' },
        { svg: tag, text: '标签', name: 'tag' },
        { svg: user, text: '账号', name: 'user' },
        { svg: msg, text: '消息', name: 'msg' },
      ],
    }
  },

  computed: {
    topItem(){ return this.$store.state.topItem; }
  },

  methods: {

    // 选择内容区
    switchMain(name) {
      if(name === 'create')
        this.$router.push(`/${name}/news`);
      else if(this.$route.path !== `/${name}`)
        this.$router.push(`/${name}`);
      this.$store.dispatch('updateTopItem', name);
    }
  },

  mounted() {
    let { topItem, $store, $route: { path } } = this;
    path = path.split('/')[1];
    if(path !== topItem) // 初始化顶栏选项
      $store.commit('updateEasy', { key: 'topItem', value: path }); // 修改顶栏
  }
};

</script>

<style lang="scss">
  @import "top.scss";
</style>
