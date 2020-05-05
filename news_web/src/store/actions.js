
import gAxios from "../public/server.js";

export default {
    
    // 切换顶栏选项
    async updateTopItem(context, item) {
        const hash = { create: '添加', news: '新闻列表', tag: '标签列表', theme: '专题列表', user: '账号列表', msg: '消息列表' };
        const path = [{ text: hash[item], route: `/${item}` }];
        context.commit('updateEasy', { key: 'topItem', value: item }); // 修改顶栏
        context.commit('updateEasy', { key: 'pathList', value: path }); // 修改主栏地址
        if(item !== 'create') // 获取数据
            await context.dispatch(`${item}/get`);
    }
};
