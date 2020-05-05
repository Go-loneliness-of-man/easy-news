const env = process.env.NODE_ENV;

let base_url = 'http://newsapi.golone.xyz/api/'; // 生产环境后台地址

switch(env) {
  case 'development':
    base_url = 'http://newsapi.com/api/';
    break;
}

export default base_url;
