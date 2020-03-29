
import Vue from 'vue';
import Router from 'vue-router';
import top from '../components/top/top.vue';
import main from '../components/main/main.vue';
import msg from '../components/msg/msg.vue';
import news from '../components/news/news.vue';
import special from '../components/special/special.vue';
import tag from '../components/tag/tag.vue';
import user from '../components/user/user.vue';

Vue.use(Router);

const msgR = { path: 'msg', components: { msg } };
const newsR = { path: 'news', components: { news } };
const specialR = { path: 'special', components: { special } };
const tagR = { path: 'tag', components: { tag } };
const userR = { path: 'user', components: { user } };

const index = {
  path: '/',
  components: {
    top,
    main,
  },
  children: [ msgR, newsR, specialR, tagR, userR ],
};

const router = new Router({
  routes: [
    index
  ],
});

// // 检测登录，未登录跳转到登录页面
// router.beforeEach(function (to, from, next) {
//   if (to.fullPath !== '/login') {                 // 不是登录页，检测
//     let flag = true;
//     if (flag) {                                   // 已登录
//       next();
//     }
//     else {                                        // 未登录，跳转回登录页
//       next('/login');
//     }
//   }
//   else {                                          // 是登录页
//     next();
//   }
// });

export default router;
