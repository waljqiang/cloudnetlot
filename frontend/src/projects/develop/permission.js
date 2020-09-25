import router from './router'
import store from './store'
import NProgress from 'nprogress' // progress bar
import 'nprogress/nprogress.css' // progress bar style
import { getToken } from '@/utils/auth' // getToken from cookie
import { Message } from 'element-ui'
NProgress.configure({ showSpinner: false })// NProgress configuration

const whiteList = ['/login','/sendEmail','/forgetpassword'] // 不重定向白名单
router.beforeEach((to, from, next) => {
	document.title = "开发者平台";
	store.dispatch('pathActive',to.path)
	window.sessionStorage.setItem("path",to.path);
	NProgress.start()
	if (getToken()) {
		if (to.path === '/login') {
			window.sessionStorage.removeItem("routeItem");
			window.sessionStorage.removeItem("system_token");
			next();
			NProgress.done();
		} else {
			if(to.path != '/register_develop'&& to.path!= '/develop_auth'){
				store.dispatch('GetInfo').then(infoRes => { // 拉取用户信息
					if(infoRes.status==10000){
						if(infoRes.data.status=='0'||infoRes.data.status=='2'){//0:未申请开发者1：申请审核中2：审核未通过，3：审核通过
							next(`/register_develop`)
						}else if(infoRes.data.status=='1'){
							next(`/develop_auth`);
						}else{
							next();
						}
					}else{	
						Message.$message({
							message:"获取用户信息失败",
							type: 'error',
							offset:100
						});
						next(`/login?redirect=${to.path}`) // 否则全部重定向到登录页
					}
				}).catch((err) => {
				})
			}else{
				next();
			}
		
		}
	}else {  
		window.sessionStorage.removeItem("routeItem");
		window.sessionStorage.removeItem("system_token");
		if(whiteList.indexOf(to.path) !== -1) {
			next()
		} else {
			next(`/login?redirect=${to.path}`) // 否则全部重定向到登录页
			NProgress.done()
		}
	}
})

router.afterEach(() => {
	NProgress.done() // 结束Progress
})
