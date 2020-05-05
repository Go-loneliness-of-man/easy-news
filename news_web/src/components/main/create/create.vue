
<template>
    <div class="create">
        <div class="aside">
            <div :class="{ activeAside: item.name === currentItemName, asideItem: true }" @click="switchForm(item.name)" v-for="(item, i) in asideList" :key="i"><span>{{item.text}}</span></div>
        </div>

        <div class="formArea">
            <router-view></router-view>
        </div>
    </div>
</template>

<script>

export default {

  data() {
    return {
      asideList: [
        { text: '新闻', name: 'news' },
        { text: '专题', name: 'theme' },
        { text: '标签', name: 'tag' },
        { text: '账号', name: 'user' },
        { text: '消息', name: 'msg' },
      ],

      currentItemName: 'news',
    }
  },

  computed: {

  },

  methods: {

    // 选择表单
    switchForm(name) {
      let temp = name;
      name = `/create/${name}`;
      if(this.$route.path !== name) {
        this.currentItemName = temp;
        this.$router.push(name);
      }
    }
  },

  mounted() {
    let { $route: { path } } = this;
    this.currentItemName = path.split('/')[2];
  }
};

</script>

<style lang="scss">
    @import "create.scss";
</style>
