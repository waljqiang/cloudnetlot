<template>
  <div  class="app-wrapper">
      <navbar/>
      <div class="main-container">
        <div class="menu_box flt" >
          <Sidebar />
        </div>
        <div class="content_box frt" >
          <app-main />
        </div>
        
    </div>
  </div>
</template>

<script>
import { Navbar,Sidebar,AppMain } from './components'
// import ResizeMixin from './mixin/ResizeHandler'
//import {refreshToken} from '@/api/login'
export default {
  name: 'Layout',
  components: {
    Navbar,
    Sidebar,
    AppMain
  },
  // mixins: [ResizeMixin],
  computed: {
    // sidebar() {
    //   return this.$store.state.app.sidebar
    // },
    // device() {
    //   return this.$store.state.app.device
    // },
    // classObj() {
    //   return {
    //     hideSidebar: !this.sidebar.opened,
    //     openSidebar: this.sidebar.opened,
    //     withoutAnimation: this.sidebar.withoutAnimation,
    //     mobile: this.device === 'mobile'
    //   }
    // }
  },
  methods: {
    handleClickOutside() {
      this.$store.dispatch('CloseSideBar', { withoutAnimation: false })
    }
  },
  mounted(){
      // refreshToken(window.sessionStorage.getItem("refresh_token"),window.sessionStorage.getItem("system_token")).then(response2 => {  
      //     window.sessionStorage.setItem("system_token",response2.data.token);
      //     window.sessionStorage.setItem("refresh_token",response2.data.refresh_token);  
      // }).catch(error => {
      //   this.$router.push({ path: '/login' })
      //    // reject(error)
      // })
  }
}
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
 // @import "src/styles/mixin.scss";
   @import "@/projects/develop/styles/index.scss";
 
</style>
