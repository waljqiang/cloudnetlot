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
	{ path: '/register', component: () => import('../views/register'), hidden: true },
    // { path: '/remote', component: () => import('../views/remote'), hidden: true },
    // { path: '/sendEmail', component: () => import('../views/sendEmail'), hidden: true },
    // { path: '/forgetpassword', component: () => import('../views/forgetpassword'), hidden: true },  

    // {
    //     path: '/help',
    //     component: () => import('@/views/help'),
    //     tips:true,
    //     help:true,
    //     hidden: true,
    //     meta: { title: 'help', icon: 'help_menu' }
	// },
	{
		path: '/',
		component: Layout,
		tips:false,
		children:[
			{
				path: '/device',
				meta: {title: 'dev_manage', icon: 'menu_dev_ico'},
				component: () => import('../views/user/index'), // Parent router-view
				hidden: false,
				children:[
					{
						meta:{ title:'dev_list'},
						translate:true,
						path:'/device/list',
						name: 'dev_list',
						components:{
							default:() => import('../views/device/list/list')
						}            
					},
					{
						meta:{ title:'dev_topo'},
						translate:true,
						path:'/device/topo',
						name: 'dev_topo',
						components:{
							default:() => import('../views/device/topo/topo')
						}
					}
				]
			},
			{
				path: '/project',
				meta: { title: 'project_management','icon': 'menu_project_ico'},
				hidden: false,
				component: () => import('../views/project/index'), // Parent router-view
				children:[
					{
						meta:{ title:'project_management'},
						translate:true,
						path:'/project/manage',
						name: 'project_manage',
						component: () => import('../views/project/manage/manage')
					}
				]
			},
			{
				path: '/account',
				meta: { title: 'account_management','icon': 'menu_account_ico'},
				component: () => import('../views/user/index'), // Parent router-view
				hidden: false,
				children:[
					{
						meta:{title:'account_list'},
						name:'user_edit',
						path:'/account/userConfig',
						hidden: false,
						component: () => import('../views/account/user/config'),
					},
					{
						meta:{title:'permission_settings'},
						name:'role_edit',
						path:'/account/roleConfig',
						hidden: false,
						component: () => import('../views/account/role/config'),
					},
					{
						meta:{title:'permission_settings'},
						name:'role_device',
						path:'/account/roleDevice',
						hidden: false,
						component: () => import('../views/account/role/device'),
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
