
<template>
    <div class="top">
        <div class="logo"><img src="../../../static/logo.JPG"></div>
        <div class="topList">
            <div v-for="(item, index) in topList" :key="index" :class="{ topListItem: true, active: topItem === item.name }" @click="switchMain(item.name)">
                <div class="icon" v-html="item.svg"></div>
                <div class="text"><span>{{item.text}}</span></div>
            </div>
        </div>
    </div>
</template>

<script>

import msg from '../../assets/svg/msg.js';
import news from '../../assets/svg/news.js';
import special from '../../assets/svg/special.js';
import tag from '../../assets/svg/tag.js';
import user from '../../assets/svg/user.js';

export default {

    data() {
        return {
            topList: [ // 顶部按钮
                { svg: news, text: '新闻', name: 'news' },
                { svg: special, text: '专题', name: 'special' },
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
        switchMain(item) {
            if(this.$route.path !== `/${item}`)
                this.$router.push(`/${item}`);
            this.$store.dispatch('updataTopItem', item);
        }
    },

    mounted() {

    }
};

</script>

<style lang="scss">
    @import "top.scss";
</style>
