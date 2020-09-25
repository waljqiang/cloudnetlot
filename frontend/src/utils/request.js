import axios from 'axios'
import { Message, MessageBox } from 'element-ui'
import store from '@/projects/develop/store'
import {saveToken,getToken,isTokenExpired,getRefreshToken } from '@/utils/auth'

// 
var domain = window.location.origin+"/"+requestRootName ;
window.sessionStorage.setItem('domain',domain);
// 创建axios实例
const service = axios.create({
	baseURL: domain,//process.env.BASE_API, // api 的 base_url
	timeout: 60000 // 请求超时时间
})

// request拦截器
service.interceptors.request.use(
  	config => {
		if (getToken()) {
			config.headers['Authorization'] = 'Bearer '+getToken() // 让每个请求携带自定义token 请根据实际情况自行修改
			/*判断刷新token请求的refresh_token是否过期*/
			if(isTokenExpired()) {
				
				/*判断是否正在刷新*/
				if (!window.isRefreshing) {
					/*将刷新token的标志置为true*/
					window.isRefreshing = true
					/*发起刷新token的请求*/
					getRefreshToken().then(res => {
						window.isRefreshing = false;
						saveToken(res.data);// 重新获取的token有效时间
						config.headers['Authorization'] = 'Bearer '+getToken();
						return config;
					}).catch(err => {
						store.dispatch('FedLogOut').then(() => {
						  location.reload() // 为了重新实例化vue-router对象 避免bug
						})
					});
				}
				
			}
		}
		return config
  	},
  error => {
    // Do something with request error
    console.log(error) // for debug
    Promise.reject(error)
  }
)

// response 拦截器
service.interceptors.response.use(
	response => {
		/**
		 * code为非10000是抛错
		 */
		const res = response.data;
		if (res.status != 10000) {
			if(res.status == 600500100){	
				Message({
					message: '认证错误',
					type: 'error',
					duration: 5 * 1000
				})
				 window.sessionStorage.removeItem("routeItem");
				store.dispatch('FedLogOut').then(() => {
				  location.reload() // 为了重新实例化vue-router对象 避免bug
				})
			}else if(res.status == 600500101){
				Message({
					message: '缺少token',
					type: 'error',
					duration: 5 * 1000
				})
				 window.sessionStorage.removeItem("routeItem");
				store.dispatch('FedLogOut').then(() => {
				  location.reload() // 为了重新实例化vue-router对象 避免bug
				})
			}else if(res.status == 600500102){
				Message({
					message: 'token无效',
					type: 'error',
					duration: 5 * 1000
				})
				 window.sessionStorage.removeItem("routeItem");
				store.dispatch('FedLogOut').then(() => {
				  location.reload() // 为了重新实例化vue-router对象 避免bug
				})
			}else if(res.status == 600500103){
				Message({
					message: '	token过期',
					type: 'error',
					duration: 5 * 1000
				})
				 window.sessionStorage.removeItem("routeItem");
				store.dispatch('FedLogOut').then(() => {
				  location.reload() // 为了重新实例化vue-router对象 避免bug
				})
			}else{
				Message({
					message: res.message || 'response Error',
					type: 'error',
					duration: 5 * 1000
				})
				return Promise.reject(new Error(res.message || 'Error'))
			}

		} else {
			return response.data;
		}
	},
	error => {
		console.log('err' + error) // for debug
		// Message({
		//   message: error.message,
		//   type: 'error',
		//   duration: 5 * 1000
		// })
		return Promise.reject(error)
	}
)

export default service
