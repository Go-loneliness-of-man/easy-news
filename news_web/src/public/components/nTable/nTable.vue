
<template>
  <div class="nTable">
    <slot name="nTableTitle" class="nTableTitle"></slot><!-- 列表标题 -->
    <slot name="nTableListItem" class="nTableListItem"></slot><!-- 一行数据 -->
  </div>
</template>

<script>

export default {

    data() {
        return {
            
        }
    },

    computed: {

    },

    methods: {

    },

    mounted() {

    }
};

</script>

<style lang="scss">
    @import "nTable.scss";
</style>
