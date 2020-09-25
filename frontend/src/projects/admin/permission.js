import router from './router'
import store from './store'
import NProgress from 'nprogress' // progress bar
import 'nprogress/nprogress.css' // progress bar style
import { getToken } from '@/utils/auth' // getToken from cookie
import { Message } from 'element-ui'
NProgress.configure({ showSpinner: false })// NProgress configuration

const whiteList = ['/login','/sendEmail','/forgetpassword'] // 不重定向白名单
router.beforeEach((to, from, next) => {
	document.title = "管理后台";
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
			if(to.path === '/'){
				next(`/account/list`);
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
