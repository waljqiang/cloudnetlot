import {login, logout } from '@/projects/home/api/auth'
import {getUserInfo } from '@/projects/home/api/user'


import { saveToken, removeToken,removeSession } from '@/utils/auth'
const user = {
	state: {
		name: '',
		is_primary: '',
		roles: []
	},

	mutations: {
		SET_NAME: (state, name) => {
			state.name = name
		},
		SET_PRIMARY: (state, primary) => {
			state.is_primary = primary
		},
		SET_ROLES: (state, roles) => {
			state.roles = roles
		}
	},

	actions: {
		// 登录
		Login({ commit }, userInfo) {
			const username = userInfo.account.trim()
			return new Promise((resolve, reject) => {
				login(username, userInfo.password).then(response => {
					if(response.status==10000){
						saveToken(response);              
						resolve(response);
					}else{
						reject(response);
					} 
				}).catch(error => {
					reject(error);
				})
					
			})
		},

		// 获取用户信息
		GetInfo({ commit }) {
			return new Promise((resolve, reject) => {
				getUserInfo().then(response => {
					if(response.status==10000){
						commit('SET_PRIMARY', response.is_primary)            
						resolve(response)
					}else{
						reject(response)
					}
				}).catch(error => {
					reject(error)
				})
			})
		},

		// 登出
		LogOut({ commit, state }) {
			return new Promise((resolve, reject) => {
				logout(state.token).then((response) => {
					window.sessionStorage.removeItem("routeItem");
					removeSession("expires");
					removeSession("refresh_token");
					removeToken()
					resolve()
				}).catch(error => {
					reject(error)
				})
			})
		},

		// 前端 登出
		FedLogOut({ commit }) {
			return new Promise(resolve => {
				window.sessionStorage.removeItem("routeItem");
				removeToken()
				resolve()
			})
		}
	}
}

export default user
