
import gAxios from "../../public/server.js";

export default {
  async get(context, params) {
    const { data: { code, result } } = await gAxios().get('news/read', { params });
    if(code === 200) {
      context.commit('updateList', result);
    }
  }
};
