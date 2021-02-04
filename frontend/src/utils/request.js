import axios from 'axios'
import { Message, MessageBox } from 'element-ui'
import store from '@/projects/home/store'
import {saveToken,getToken,isTokenExpired,getRefreshToken } from '@/utils/auth'

// 
var domain = window.location.origin+"/"+requestRootName;
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
			let errCode = res.errorCode[0];
			let err = {};			
			switch (errCode) {
				case 600000100:
					err.message = this.$t('msg.request_err_tips1');
					err.out = false;
					err.type = 'global';
					break;
				case 600000101:
					err.message = this.$t('msg.request_err_tips1');
					err.out = false;
					err.type = 'global';
					break;
				case 600000102:
					err.message = this.$t('msg.request_err_tips2');
					err.out = false;
					err.type = 'global';
					break;
				case 600000103:
					err.message = this.$t('msg.request_err_tips3');
					err.out = false;
					err.type = 'global';
					break;
				case 600100100:
					err.message = this.$t('msg.request_err_tips4');
					err.out = true;
					err.type = 'global';
					break;
				case 600100101:
					err.message = this.$t('msg.request_err_tips4');
					err.out = true;
					err.type = 'global';
					break;
				case 600100102:
					err.message = this.$t('msg.request_err_tips4');
					err.out = true;
					err.type = 'global';
					break;
				case 600100103:
					err.message = this.$t('msg.request_err_tips5');
					err.out = true;
					err.type = 'global';
					break;
				case 600100104:
					err.message = this.$t('msg.request_err_tips5');
					err.out = true;
					err.type = 'global';
					break;
				case 600100105:
					err.message = this.$t('msg.request_err_tips5');
					err.out = true;
					err.type = 'global';
					break;
				case 600100106:
					err.message = this.$t('msg.request_err_tips5');
					err.out = true;
					err.type = 'global';
					break;
				case 600100107:
					err.message = this.$t('msg.request_err_tips5');
					err.out = true;
					err.type = 'global';
					break;
				case 600100108:
					err.message = this.$t('msg.request_err_tips5');
					err.out = true;
					err.type = 'global';
					break;
				case 600100109:
					err.message = this.$t('msg.request_err_tips5');
					err.out = true;
					err.type = 'global';
					break;
				case 600101100:
					err.message = this.$t('msg.request_err_tips6');
					err.out = true;
					err.type = 'global';
					break;
				case 600101101:
					err.message = this.$t('msg.request_err_tips7');
					err.out = true;
					err.type = 'global';
					break;
				case 600101102:
					err.message = this.$t('msg.request_err_tips8');
					err.out = true;
					err.type = 'global';
					break;
				case 600102100:
					err.message = this.$t('msg.request_err_tips9');
					err.out = true;
					err.type = 'global';
					break;
				case 600103100:
					err.message = this.$t('msg.request_err_tips9');
					err.out = true;
					err.type = 'global';
					break;
				case 600104100:
					err.message = this.$t('msg.request_err_tips9');
					err.out = true;
					err.type = 'global';
					break;
			}
			if(err.type == 'global'){
				if(err.out){
					Message({
						message: err.message,
						type: 'error',
						offset:100,
						duration: 3 * 1000
					})
					window.sessionStorage.removeItem("routeItem");
					store.dispatch('FedLogOut').then(() => {
					  location.reload() // 为了重新实例化vue-router对象 避免bug
					})
				}else{alert(4);
					Message({
						message: err.message,
						type: 'error',
						offset:100,
						duration: 3 * 1000
					})
					store.commit('showloadding',{show:false});
				}
			}else{
				return Promise.reject(response.data);
			}
		} 
		return response.data;
	},
	error => {
		switch (err.response.status) {
			case 400:
				err.message = this.$t('msg.request_err_tips1');
				break;
			case 401:
				err.message = this.$t('msg.request_err_tips10');
				break;
			case 403:
				err.message = this.$t('msg.request_err_tips11');
				break;
			case 404:
				err.message = this.$t('msg.request_err_tips12');
				break;
			case 405:
				err.message = this.$t('msg.request_err_tips13');
				break;
			case 408:
				err.message = this.$t('msg.request_err_tips14');
				break;
			case 500:
				err.message = this.$t('msg.request_err_tips9');
				break;
			case 501:
				err.message = this.$t('msg.request_err_tips9');
				break;
			case 502:
				err.message = this.$t('msg.request_err_tips15');
				break;
			case 503:
				err.message = this.$t('msg.request_err_tips16');
				break;
			case 504:
				err.message = this.$t('msg.request_err_tips14');
				break;
			case 505:
				err.message = this.$t('msg.request_err_tips17');
				break;
			default:
				err.message = this.$t('msg.request_err_tips18');
		}
		Message({
			message: err.message,
			type: 'error',
			offset:100
		})
		store.commit('showloadding',{show:false});
		return error.response;
	}
)

export default service
