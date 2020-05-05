
export default {

    // 所有简单赋值操作都采用该方法修改
    updateEasy(state, { key, value }) {
        state[key] = value;
    },

    // 向主栏路径加入一项
    pushPathList(state, item) {
        state.pathList.push(item);
    },
};



