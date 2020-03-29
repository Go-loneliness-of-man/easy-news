import axios from 'axios';
import baseURL from './env_config';

const Axios = axios.create({ // 配置请求对象
	baseURL: baseURL,
	timeout: 30000,
	responseType: 'json',
	withCredentials: false,
	headers: { 'Content-Type': 'application/json;charset=utf-8' }
});

// 配置请求拦截器
Axios.interceptors.request.use(
	config => {

		return config;
	},
	error => {
		console.log(error);
		return Promise.reject(error);
	}
);

// 配置响应拦截器
Axios.interceptors.response.use(res => {
	if (res.data.code !== 200)
		console.log(res.data.errMsg ? res.data.errMsg : res.data.message);
	return res.data;
});

export default Axios;
