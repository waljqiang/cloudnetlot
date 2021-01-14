const globalState = {
  state: {
    loadding: {
      show:false,
      text:"",
      bg:"rgba(0, 0, 0, 0.7)"

    }
  },
  mutations: {
    showloadding(state, load) {
      
      if(load.text){
        state.loadding.text = load.text;
      }
      if(load.bg){
        state.loadding.bg = load.bg;
      }
      if(load.show!=undefined){
        state.loadding.show = load.show;
      }
		}
  },
  actions: {
    setloadding(context,load){
      context.commit("showloadding",load);
    }
  },
  getters: {
    isloading:(state)=>{
      return state.loadding
    }
  }
}

export default globalState
