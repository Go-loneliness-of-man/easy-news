
import nTable from "./nTable/nTable.vue";

const components = {
  nTable,
}

export default function (Vue) {
  Object.keys(components).forEach(key => Vue.component(key, components[key]));
}
