<template >
  <section >
    <transition name="fade-transform" mode="out-in">   
        <router-view/>
    </transition>
  </section>
  
</template>
<script>
export default {
  data(){
    return {
    
    }
  },
  methods:{
    
  },
  components: {
   // Breadcrumb
  }
}

</script>


