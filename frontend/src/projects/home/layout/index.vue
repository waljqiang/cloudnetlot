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
export default {
  name: 'Layout',
  components: {
    Navbar,
    Sidebar,
    AppMain
  },
  // mixins: [ResizeMixin],
  computed: {
  },
  methods: {
    handleClickOutside() {
      this.$store.dispatch('CloseSideBar', { withoutAnimation: false })
    }
  },
  mounted(){

  }
}
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
 // @import "src/styles/mixin.scss";
   @import "@/projects/home/styles/index.scss";
 
</style>
