<template>
    
        <el-submenu v-if="item.children.length>1" :index="item.path" :show-timeout="0" :hide-timeout="0" >
			<template slot="title">
				<img :src="showImg(item.meta.icon)" />
				<span >{{$t('route.'+item.meta.title)}}</span>
			</template>
			<el-menu-item v-for="child in item.children" :key="child.path" :index="child.path" >{{$t('route.'+child.meta.title)}}</el-menu-item>
        </el-submenu>
        <el-menu-item v-else :index="item.children[0].path">
			<img :src="showImg(item.meta.icon)" />
			<span v-text="$t('route.'+item.meta.title)" slot="title"></span>
        </el-menu-item>

</template>

<script>
export default {
	name: 'SidebarItem',
	props: {
		// route object
		item: {
			type: Object,
			required: true
		}
	},
	data(){
		return {
		
		}
	},
	methods: {
		showImg(name){
			return require('@/public/img/home/'+name+'.png');
		},
	},
	computed: {
		
	},
	created(){
			
	},
	mounted(){
		
	}
}
</script>
<style lang="scss" scoped>

</style>