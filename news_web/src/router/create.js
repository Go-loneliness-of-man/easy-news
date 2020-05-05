
import create from '../components/main/create/create.vue';
import msg from '../components/createForm/msg/msg.vue';
import news from '../components/createForm/news/news.vue';
import theme from '../components/createForm/theme/theme.vue';
import tag from '../components/createForm/tag/tag.vue';
import user from '../components/createForm/user/user.vue';

export default {
    path: 'create',
    component: create,
    children: [
        {
            path: 'msg',
            component: msg,
        },
        {
            path: 'news',
            component: news,
        },
        {
            path: 'theme',
            component: theme,
        },
        {
            path: 'tag',
            component: tag,
        },
        {
            path: 'user',
            component: user,
        },
    ]
};
