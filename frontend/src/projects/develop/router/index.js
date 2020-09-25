import Vue from 'vue'
import Router from 'vue-router'

// in development-env not use lazy-loading, because lazy-loading too many pages will cause webpack hot update too slow. so only in production use lazy-loading;
// detail: https://panjiachen.github.io/vue-element-admin-site/#/lazy-loading

Vue.use(Router)

/* Layout */
import Layout from '../layout'

/**
* hidden: true                   if `hidden:true` will not show in the sidebar(default is false)
* alwaysShow: true               if set true, will always show the root menu, whatever its child routes length
*                                if not set alwaysShow, only more than one route under the children
*                                it will becomes nested mode, otherwise not show the root menu
* redirect: noredirect           if `redirect:noredirect` will no redirect in the breadcrumb
* name:'router-name'             the name is used by <keep-alive> (must set!!!)
* meta : {
    title: 'title'               the name show in subMenu and breadcrumb (recommend set)
    icon: 'svg-name'             the icon show in the sidebar
    breadcrumb: false            if false, the item will hidden in breadcrumb(default is true)
  }
**/
import i18n from '../lang' // internationalization
let lang = i18n.locale;

export let constantRouterMap = [    //添加新一级路由需要修改 permission.JS 相关判断 constantRouterMap
    { path: '/login', component: () => import('../views/login/index'), hidden: true },
	{ path: '/404', component: () => import('../views/404'), hidden: true },
	{
		path: '/',
		component: Layout,
		tips:false,
		children:[
			{ 	path: '/userCenter/infos', 
				component: () => import('../views/user_center/index'), 
				hidden: true 
			},
			{ path: '/register_develop',
				component: () => import('../views/register_develop/index'),
				hidden: true 
			},
			{ path: '/develop_auth',
				component: () => import('../views/register_develop/auth_wait'),
				hidden: true 
			},
			{
				path: '/product',
				meta: { title: 'product_list','icon': 'menu_account_ico'},
				component: () => import('../views/product/list'), // Parent router-view
				hidden: false,
				children:[
					{
						translate:true,
						path:'/product/list',
						hidden: false,
						component: () => import('../views/product/list'),
					}
				]
			}
		]
    },
  
  { path: '*', redirect: '/404', hidden: true }
]

export default new Router({
  // mode: 'history', //后端支持可开
  scrollBehavior: () => ({ y: 0 }),
  routes: constantRouterMap
})
