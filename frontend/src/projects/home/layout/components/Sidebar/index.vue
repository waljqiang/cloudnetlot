<template>
  	<div class="sidebar_box" 
		@mouseenter.stop="mEnter"
		@mouseleave.stop="mOut"
        >
		<el-scrollbar wrap-class="scrollbar-wrapper">
			<el-menu
				:default-active="activeM"
				:collapse="collapse"
				:collapse-transition="false"
				:text-color="variables.white_font"
				:active-text-color="variables.white_font"
				class="el-menu-vertical-custom"
				:router="true"
				:unique-opened="true"
				menu-trigger="click"
			>
				<sidebar-item v-for="route in MenuArr" v-if="!route.hidden" :key="route.path" :item="route" />	
			</el-menu>  
      	</el-scrollbar>
  	</div>
</template>

<script>
import SidebarItem from './SidebarItem'
import variables from '@/projects/home/styles/variables.scss'
export default {
	data(){
		return {
			collapse:true,
			MenuArr:"",
			activeM:this.$route.path
		}
	},
  	components: { SidebarItem },
	methods: {
		mEnter(){
			this.collapse=false;
		},
		mOut(){
			this.collapse=true;
		},
		filterMenu(){
			for(var i=0;i<this.routes.length;i++){
				if(this.routes[i].path=='/'){
					this.MenuArr = this.routes[i].children;
					return;
				}
			}
		}
	},
	computed: {
		routes() {
			return this.$router.options.routes;
		},
		variables() {
			return variables
		}
	},
  	created(){
		this.filterMenu();
  	},
	mounted(){
	}
}
</script>
<style lang="scss" scoped>
	.el-menu--collapse .el-menu--vertical,.el-menu--collapse .el-tooltip{
		display: none;
	}
</style>