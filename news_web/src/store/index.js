
import Vue from 'vue';
import Vuex from 'vuex';
import mutations from './mutations.js';
import actions from './actions.js';
import news from './news/news.js';
import msg from './msg/msg.js';
import special from './special/special.js';
import tag from './tag/tag.js';
import user from './user/user.js';

Vue.use(Vuex);

export default new Vuex.Store({

	modules: { news, msg, special, tag, user }, // 模块

	state: {                                 	// 定义状态
		topItem: 'news', // 顶栏当前选项
		pathList: [{ text: '新闻列表', route: '/news' }], // 主栏路径列表
	},

	getters: {                      	        // 定义 getter
		
	},

	mutations,                          	    // 定义同步提交方法
	actions                                 	// 定义异步操作方法
});


