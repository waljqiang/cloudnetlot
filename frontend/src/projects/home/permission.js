import router from './router'
import store from './store'
import NProgress from 'nprogress' // progress bar
import 'nprogress/nprogress.css' // progress bar style
import { getToken } from '@/utils/auth' // getToken from cookie
NProgress.configure({ showSpinner: false })// NProgress configuration

const whiteList = ['/login','/sendEmail','/forgetpassword','/register','/agreement','/resetpassword'] // 不重定向白名单
router.beforeEach((to, from, next) => {
	document.title = "云平台";
	store.dispatch('pathActive',to.path)
	window.sessionStorage.setItem("path",to.path);
	NProgress.start()
  	if (getToken()) {
      	if (to.path === '/login') {
          window.sessionStorage.removeItem("routeItem");
          window.sessionStorage.removeItem("system_token");
          next()
          NProgress.done() 
      	} else {
			if (!store.state.user.infos.data) {
				store.dispatch('GetInfo').then(infoRes => { // 拉取用户信息
					if(!infoRes.data.is_primary){//非主账号，删除账号管理路由
						let routeArr = router.options.routes;	
						for(var i=0;i<routeArr.length;i++){
							if(routeArr[i].path=='/'){
								for(var k=0;k<routeArr[i].children.length;k++){	
									if(routeArr[i].children[k].is_primary){
										//routeArr[i].children.pop();
										routeArr[i].children.splice(k,1);
									}
								}
								break;
							} 
						}
					}
					next()
				}).catch((err) => {
					//next({ path: '/login' })
				})
			} else {
				next()
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
