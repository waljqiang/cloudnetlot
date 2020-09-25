import Vue from 'vue'
import Vuex from 'vuex'
import app from './modules/app'
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
    app,
    user
  },
  getters
})

export default store
