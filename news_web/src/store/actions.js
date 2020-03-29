
import axios from "../public/server.js";

export default {
    
    // 切换顶栏选项
    updataTopItem(context, item) {
        const hash = { news: '新闻列表', tag: '标签列表', special: '专题列表', user: '账号列表', msg: '消息列表' };
        const path = [{ text: hash[item], route: `/${item}` }];
        context.commit('updataTopItem', item); // 修改顶栏
        context.commit('updataPathList', path); // 修改主栏地址
        context.dispatch(`${item}/get`); // 获取数据
    }
};
