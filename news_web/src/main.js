// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue';
import App from './App.vue';
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
import router from './router/index.js';
import store from './store/index.js';
import VueTinymce from "@packy-tang/vue-tinymce"

import gAxios from './public/server.js'; // axios
import nComponents from './public/components/importComponents.js'; // 导入项目自定义组件
import './public/rwJs.js'; // 导入 rw.js

Vue.use(ElementUI);
Vue.use(nComponents);
Vue.use(VueTinymce);

Vue.config.productionTip = false;
Vue.prototype.$gAxios = gAxios;

/* eslint-disable no-new */
new Vue({
	el: '#app',
	router,
	store,
	components: { App },
	template: '<App/>',
});




