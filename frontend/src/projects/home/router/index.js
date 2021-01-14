import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

/* Layout */
import Layout from '../layout'

import i18n from '../lang' // internationalization
let lang = i18n.locale;

export let constantRouterMap = [    //添加新一级路由需要修改 permission.JS 相关判断 constantRouterMap
    { path: '/login', component: () => import('../views/login/index'), hidden: true },
	{ path: '/404', component: () => import('../views/404'), hidden: true },
	{ path: '/register', component: () => import('../views/register'), hidden: true },
	{ path: '/agreement', component: () => import('../views/user_agreement'), hidden: true },
	
    // { path: '/remote', component: () => import('../views/remote'), hidden: true },
     { path: '/sendEmail', component: () => import('../views/sendEmail'), hidden: true },
     { path: '/resetpassword', component: () => import('../views/resetpassword'), hidden: true },  

    
	{
		path: '/',
		component: Layout,
		tips:false,
		children:[
			{
				path: '/home',
				meta: { title: 'home','icon': 'menu_home_ico'},
				hidden: false,
				component: () => import('../views/home/main/main'), // Parent router-view
				children:[
					{
						meta:{ title:'project_management'},
						translate:true,
						path:'/home/main',
						name: 'home',
						component: () => import('../views/home/main/main')
					}
				]
			},
			{
				path: '/device',
				meta: {title: 'dev_manage', icon: 'menu_dev_ico'},
				component: () => import('../views/device/list/list'),
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
				component: () => import('../views/project/manage/manage'),
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
				path: '/inspection',
				meta: { title: 'regular_inspection','icon': 'menu_inspect_ico'},
				hidden: false,
				component: () => import('../views/inspection/index'),
				children:[
					{
						meta:{ title:'regular_inspection'},
						translate:true,
						path:'/inspection/manage',
						name: 'inspection',
						component: () => import('../views/inspection/list/index')
					}
				]
			},
			{
				path: '/maintain',
				meta: { title: 'sys_maintain','icon': 'menu_maintenance_ico'},
				component: () => import('../views/maintain/index'),
				hidden: false,
				children:[
					{
						meta:{title:'backup_reset'},
						name:'backup_reset',
						path:'/maintain/config',
						hidden: false,
						component: () => import('../views/maintain/config/index'),
					},
					{
						meta:{title:'upgrade_management'},
						name:'dev_upgrade',
						path:'/maintain/upgrade',
						hidden: false,
						component: () => import('../views/maintain/upgrade/index'),
					},
					{
						meta:{title:'log_management'},
						name:'sys_log',
						path:'/maintain/log',
						hidden: false,
						component: () => import('../views/maintain/log/index'),
					}
				]
			},
			{
				path: '/account',
				meta: { title: 'account_management','icon': 'menu_account_ico'},
				component: () => import('../views/account/user/list'),
				hidden: false,
				children:[
					{
						meta:{title:'account_list'},
						name:'account_manage',
						path:'/account/list',
						hidden: false,
						component: () => import('../views/account/user/list'),
					}
				]
			},
			{
				path: '/user_info',
				component: () => import('../views/user/info/info'),
				nav:true,
				hidden: true,
				meta: { title: 'personal_center', icon: 'usercenter' }
			},
			{
				path: '/msg',
				component: () => import('../views/msg/info/info'),
				nav:true,
				hidden: true,
				meta: { title: 'system_message', icon: 'help_menu' }
			},
			{
				path: '/help',
				component: () => import('../views/help'),
				nav:true,
				hidden: true,
				meta: { title: 'help', icon: 'help_menu' }
			},
		]
    },
  	{ path: '*', redirect: '/404', hidden: true }
]

export default new Router({
  // mode: 'history', //后端支持可开
  scrollBehavior: () => ({ y: 0 }),
  routes: constantRouterMap
})
