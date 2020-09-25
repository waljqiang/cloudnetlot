<template >
    <div class="form_box"
        v-loading.fullscreen.lock="fullscreenLoading"
        :element-loading-text="loading_txt"
        :element-loading-background="loading_bgcolor"
    >
        <el-tabs v-model="active" >
            <el-tab-pane  label="用户信息" name="first">
                <el-form  label-width="200px" >
                    <el-form-item label="用户名" >
                        <span v-text="infos.username"></span>
                    </el-form-item>
                    <el-form-item label="昵称" >
                        <span  v-text="infos.nickname"></span>
                    </el-form-item>
                    <el-form-item label="邮箱" >
                        <span v-text="infos.email"></span>
                    </el-form-item>
                    <el-form-item label="地址">
                        <span v-text="infos.address" ></span>
                    </el-form-item>
                    <el-form-item label="status">
                        <span v-if="infos.status==0" >未申请开发者</span>
                        <span v-else-if="infos.status==1">申请审核中</span>
                        <span v-else-if="infos.status==2" >审核未通过</span>
                        <span v-else >审核通过</span>
                    </el-form-item>
                    <el-form-item label="开发者appid" >
                        <span v-text="infos.develop.appid"></span>
                    </el-form-item>
                    <el-form-item label="开发者秘钥" >
                        <span v-text="infos.develop.secret"></span>
                    </el-form-item>
                    <el-form-item label="开发者姓名" >
                        <span v-text="infos.develop.name"></span>
                    </el-form-item>
                    <el-form-item label="开发者所属企业名称">
                        <span v-text="infos.develop.enterprise"></span>
                    </el-form-item>
                </el-form>
            </el-tab-pane>
        </el-tabs>
        
    </div>
</template>
<script>

import store from '@/projects/develop/store'
export default {
    data() {
        
        return {
            fullscreenLoading:false,
            loading_txt:"请稍后……",
            loading_bgcolor:"rgba(0, 0, 0, 0.7)",
            
            active:"first",
            infos:store.state.user.infos
        };
    },
    methods: {
        
    },
    mounted(){
        console.log(store.state.user.infos)
    }
}
</script>
<style scoped>

</style>