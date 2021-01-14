import Vue from 'vue'
import Vuex from 'vuex'
import globalState from './modules/global'
import user from './modules/user'
import getters from './getters'

Vue.use(Vuex)

const store = new Vuex.Store({
	state: {
		thisPath:''
	},
	mutations:{
		updateState(state,p){
			state.thisPath = p;
		}
	},
	actions:{
		pathActive(context,p){
			context.commit('updateState',p)
		}
	},
	modules: {
		globalState,
		user
	},
	getters
})

export default store
