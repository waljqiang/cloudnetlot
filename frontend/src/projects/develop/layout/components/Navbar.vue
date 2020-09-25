<template>
  	<div class="navbar"
	v-loading.fullscreen.lock="fullscreenLoading"
    :element-loading-text="loading_txt"
    :element-loading-background="loading_bgcolor"
   	>
    <!-- <hamburger :toggle-click="toggleSideBar" :is-active="sidebar.opened" class="hamburger-container"/> -->
    <!-- <breadcrumb /> -->
		<img :src="logo_img" >
		<div class="frt">
			<div class="navbar_menu flt" @click="handusercenter" title="个人中心" @mouseenter="navmEnter('userCenter_img')" @mouseleave="navmOut('userCenter_img')" >
				<img :src="userCenter_img" />
			</div>
			<!-- <div class="navbar_menu flt"  @mouseenter="navmEnter('msg_img')" @mouseleave="navmOut('msg_img')">
				<img :src="msg_img" />
			</div>
			<div class="navbar_menu flt"  @mouseenter="navmEnter('help_img')" @mouseleave="navmOut('help_img')">
				<img :src="help_img" />
			</div> -->
			<div class="navbar_menu flt" @click="handlogout"  @mouseenter="navmEnter('logout_img')" @mouseleave="navmOut('logout_img')">
				<img :src="logout_img" />
			</div>
		</div>
  	</div>
</template>

<script>
import { mapGetters } from 'vuex'
import store from '@/projects/develop/store'

import logout_img from '@/projects/develop/assets/common/logout_ico.png'
import userCenter_img from '@/projects/develop/assets/common/usercenter_ico.png'
import msg_img from '@/projects/develop/assets/common/msg_ico.png'
import help_img from '@/projects/develop/assets/common/help_ico.png'

import logout_img_hover from '@/projects/develop/assets/common/logout_ico_hover.png'
import userCenter_img_hover from '@/projects/develop/assets/common/usercenter_ico_hover.png'
import msg_img_hover from '@/projects/develop/assets/common/msg_ico_hover.png'
import help_img_hover from '@/projects/develop/assets/common/help_ico_hover.png'

import img_logo from "@/projects/develop/assets/login/logo.png"

let imgObj = {
      logout_img:logout_img,
      userCenter_img:userCenter_img,
      msg_img:msg_img,
      help_img:help_img,
      logout_img_hover:logout_img_hover,
      userCenter_img_hover:userCenter_img_hover,
      msg_img_hover:msg_img_hover,
      help_img_hover:help_img_hover
};

export default {
	data() {
		return {
			logout_img:logout_img,
			userCenter_img:userCenter_img,
			msg_img:msg_img,
			help_img:help_img,

			// userName:store.getters.roles.username!=''?store.getters.roles.username : store.getters.roles.account,
			logo_img:img_logo,

			fullscreenLoading:false,
            loading_txt:"请稍后……",
            loading_bgcolor:"rgba(0, 0, 0, 0.7)",
		}
	},

	computed: {
		routes() {
			return this.$router.options.routes
		}
	},
	mounted(){
		
	},
	methods: {
		navmEnter(key){
			this[key] = imgObj[key+"_hover"];
		},
		navmOut(key){
			this[key] = imgObj[key];
		},
		handusercenter(){
			this.$router.push({path:'/userCenter/infos'})
		},
		
		handlogout() {
			this.$confirm(this.$t('msg.logout_tips'), this.$t('msg.tips_title'), {
				confirmButtonText: this.$t('msg.confirm_btn'),
				cancelButtonText: this.$t('msg.cancel_btn'),
				type: 'warning'
			}).then(() => {
				this.fullscreenLoading = true; 
					this.$store.dispatch('LogOut').then(() => {
					//loading.close();
					location.reload() // 为了重新实例化vue-router对象 避免bug
				}).catch((error) => {
					this.fullscreenLoading = false;
					this.$message.error(this.$t('msg.set_error_tips2'));  
				})
			
			}).catch(() => {
				
			});   
		
		}
	}
}
</script>

<style rel="stylesheet/scss" lang="scss" scoped>


</style>

