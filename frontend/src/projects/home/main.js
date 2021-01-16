import Vue from 'vue'
//import commonJs from '../../public/js/common'
import Cookies from 'js-cookie'

import 'normalize.css/normalize.css' // A modern alternative to CSS resets

import './theme/index.css'
import ElementUI from 'element-ui'
//import 'element-ui/lib/theme-chalk/index.css'

import './styles/index.scss' // global css

import App from './App'
import store from './store'
import router from './router'
import i18n from './lang' // internationalization
//import '@/icons' // icon
import './permission' // permission control

//import VueSocketIO from 'vue-socket.io'
/**
 * This project originally used easy-mock to simulate data,
 * but its official service is very unstable,
 * and you can build your own service if you need it.
 * So here I use Mock.js for local emulation,
 * it will intercept your request, so you won't see the request in the network.
 * If you remove `../mock` it will automatically request easy-mock data.
 */
 //import './mock' // simulation data

window.Vue = Vue // 要在vue-i18n实例化之前执行

Vue.use(ElementUI, {
  size: Cookies.get('size') || 'small', // set element-ui default size
  i18n: (key, value) => i18n.t(key, value)
})

// Vue.use(new VueSocketIO({
//   debug: true,
//   // 服务器端地址
//   connection: "http://192.168.33.10:7777",
//   vuex: {}
// }))


Vue.config.productionTip = false

new Vue({
  el: '#app',
  router,
  store,
  i18n,
  render: h => h(App)
})
