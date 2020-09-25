<template>
    <div
        v-loading.fullscreen.lock="fullscreenLoading"
        :element-loading-text="loading_txt"
        :element-loading-background="loading_bgcolor">
        <el-page-header @back="goBack" content="用户详情" style="line-height:50px" ></el-page-header>
         <el-divider></el-divider>
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
                <span v-text="infos.appid"></span>
            </el-form-item>
            <el-form-item label="开发者秘钥" >
                <span v-text="infos.secret"></span>
            </el-form-item>
            <el-form-item label="开发者姓名" >
                <span v-text="infos.name"></span>
            </el-form-item>
            <el-form-item label="开发者所属企业名称">
                <span v-text="infos.enterprise"></span>
            </el-form-item>
        </el-form>
    </div>
</template>
<script>
import {developInfo} from '@/projects/admin/api/develop'
export default {
    data(){
        return {
            fullscreenLoading:false,
            loading_txt:"请稍后……",
            loading_bgcolor:"rgba(0, 0, 0, 0.7)",
           
            infos:{
                "username": "",
                "nickname": "",
                "email": "",
                "phone": "",
                "name": "",
                "idcard": "",
                "enterprise": "",
                "enterprise_des": "",
                "enterprisecode": "",
                "appid": "",
                "secret": "",
                "status": 1,
                "created_at": ""
            }
  
        }
    },
    methods:{
        goBack(){
             this.$router.push({path:"/account/list"})
        },
        getInfo(){
            developInfo({uid:this.$route.query.id}).then(response => {
                this.fullscreenLoading = false;
                if(response.status==10000){
                    this.infos = response.data;
                }else{
                     this.$message({
                        message:'请求失败',
                        type: 'error',
                        offset:100
                    });
                }
                
            })
            .catch((error) => {
                this.fullscreenLoading = false;
            })
        }
    },
    beforeMount(){
        
    },
    mounted(){   
        this.$nextTick(function () {
            this.fullscreenLoading = true;
            this.getInfo();
            
        })  
    },
    beforeDestroy(){
        
    }
}

</script>
<style lang="scss" scoped>

</style>


