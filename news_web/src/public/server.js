import Qs from 'qs';
import axios from 'axios';
import baseURL from './env_config';

const env = process.env.NODE_ENV;

function reqSuccess(config) {
  if(env === 'development')
  	console.log(config, '发送成功');
	return config;
}

function reqFault(error) {
  if(env === 'development')
    console.log(error, '发送失败');
	return error;
}

function resSuccess(res) {
  if(env === 'development')
  	console.log(res, '成功获取数据');
	return res;
}

function resFault(error) {
  if(env === 'development')
  	console.log(error, '获取失败');
	return error;
}

function gAxios(base = baseURL) {
	const Axios = axios.create({ // 配置请求对象
		baseURL: base,
		timeout: 5000, // 超过 5 秒视为掉线
		responseType: 'json',
		withCredentials: false,
		transformRequest: [function (data) { // 转换数据适应 form 格式
			data = Qs.stringify(data);
			return data;
		}],
		headers: { 'Content-type': 'application/x-www-form-urlencoded' } // 修改请求头适应 php 默认配置
	});
	Axios.interceptors.request.use(reqSuccess, reqFault); // 配置请求拦截器
	Axios.interceptors.response.use(resSuccess, resFault); // 配置响应拦截器
	return Axios;
}

export default gAxios;

