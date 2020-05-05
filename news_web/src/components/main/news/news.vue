
<template>
  <div class="news">
    <nTable class="newsList"><!-- 新闻列表 -->
      <template v-slot:nTableTitle><!-- 表格标题 -->
        <div class="newsTitleList nTableTitle">
          <div class="theme">所属专题</div>
          <div class="title">标题</div>
          <div class="click">点击量</div>
          <div class="good">点赞数</div>
          <div class="tags">标签列表</div>
          <div class="createTime">创建时间</div>
          <div class="updateTime">编辑时间</div>
          <div class="buttonList">操作</div>
        </div>
      </template>
      <template v-slot:nTableListItem><!-- 一行数据 -->
        <div class="newsListItem nTableListItem" v-for="(item, index) in newsList" :key="index">
          <div class="theme">{{item.theme}}</div>
          <div class="title">{{item.title}}</div>
          <div class="click">{{item.click}}</div>
          <div class="good">{{item.good}}</div>
          <div class="tags">
            <div v-for="(tag, index) in item.tags" :key="index" :class="{ tag: true, master: tag.master }">{{tag.name}}</div>
          </div>
          <div class="createTime">{{item.time | convertTime}}</div>
          <div class="updateTime">{{item.revise | convertTime}}</div>
          <div class="buttonList">
            <div class="button look" v-html="buttonList.look"></div>
            <div class="button update" v-html="buttonList.update"></div>
            <div class="button del" v-html="buttonList.del" @click="del(item)"></div>
          </div>
        </div>
      </template>
    </nTable>
  </div>
</template>

<script>

import del from '../../../assets/svg/del.js';
import update from '../../../assets/svg/update.js';
import look from '../../../assets/svg/look.js';

export default {

  data() {
    return {
      buttonList: { del, update, look, },
      page_size: 20,
      page_number: 1,
    }
  },

  computed: {
    newsList(){ return this.$store.state.news.list; }
  },

  methods: {

    look() {

    },

    update() {

    },

    del(item) {
      console.log(item);
    }
  },

  filters: {

    convertTime(number) {
      return rw.date.localTime('y-mon-d h:m:s', parseInt(number));
    }
  },

  mounted() {
    this.$store.dispatch('news/get', { number: 1, size: 19, search: '' });
  }
};

</script>

<style lang="scss">
  @import "news.scss";
</style>


