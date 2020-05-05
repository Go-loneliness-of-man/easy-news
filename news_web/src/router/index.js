
import Vue from 'vue';
import Router from 'vue-router';
import main from '../components/main/main.vue';
import top from '../components/top/top.vue';
import msg from '../components/main/msg/msg.vue';
import news from '../components/main/news/news.vue';
import theme from '../components/main/theme/theme.vue';
import tag from '../components/main/tag/tag.vue';
import user from '../components/main/user/user.vue';

import createR from './create.js';

Vue.use(Router);

const msgR = { path: 'msg', component: msg };
const newsR = { path: 'news', component: news };
const themeR = { path: 'theme', component: theme };
const tagR = { path: 'tag', component: tag };
const userR = { path: 'user', component: user };

const root = {
  path: '/',
  components: { top, main },
  children: [ msgR, newsR, themeR, tagR, userR, createR ],
};

const router = new Router({ routes: [ root ] });

// 导航守卫
router.beforeEach(function (to, from, next) {
  switch(to.fullPath) {
    case '/': // 默认 /news
      next('/news');
      break;
  }
  next();
});

export default router;
