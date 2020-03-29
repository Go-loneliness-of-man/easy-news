
export default {

    // 修改顶栏当前选项
    updataTopItem(state, item) {
        state.topItem = item;
    },

    // 直接修改主栏路径
    updataPathList(state, path) {
        state.pathList = path;
    },

    // 向主栏路径加入一项
    pushPathList(state, item) {
        state.pathList.push(item);
    },
};



