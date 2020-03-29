
<template>
    <div class="main">
        <div class="mainTop">
            <div class="pathList">
                <div class="pathListItem" v-for="(item, index) in pathList" :key="index" @click="pathClick(item.route)">
                    <div class="separator">{{index ? '>' : ''}}</div>
                    <div :class="{ link: true, active: index === (pathList.length - 1) }">{{item.text}}</div>
                </div>
            </div>
            <div class="search">

            </div>
            <div class="create">

            </div>
        </div>
        <router-view name="msg"/>
        <router-view name="news"/>
        <router-view name="special"/>
        <router-view name="tag"/>
        <router-view name="user"/>
    </div>
</template>

<script>

export default {

    data() {
        return {
            
        }
    },

    computed: {
        pathList(){ return this.$store.state.pathList; }
    },

    methods: {

        // 选择内容区
        switchMain(item) {
            this.$store.dispatch('updataTopItem', item);
        },

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
