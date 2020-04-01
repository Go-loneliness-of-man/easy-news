
import Vue from 'vue';
import Router from 'vue-router';
import main from '../components/main/main.vue';
import top from '../components/top/top.vue';
import msg from '../components/main/msg/msg.vue';
import news from '../components/main/news/news.vue';
import special from '../components/main/special/special.vue';
import tag from '../components/main/tag/tag.vue';
import user from '../components/main/user/user.vue';

Vue.use(Router);

const msgR = { path: 'msg', component: msg };
const newsR = { path: 'news', component: news };
const specialR = { path: 'special', component: special };
const tagR = { path: 'tag', component: tag };
const userR = { path: 'user', component: user };

const index = {
  path: '/',
  components: { top, main },
  children: [ msgR, newsR, specialR, tagR, userR ],
};

const router = new Router({ routes: [ index ] });

// 检测登录，未登录跳转到登录页面
router.beforeEach(function (to, from, next) {
  if (to.fullPath === '/') { // 默认在新闻选项
    next('/news');
  }
  next();
});

export default router;
