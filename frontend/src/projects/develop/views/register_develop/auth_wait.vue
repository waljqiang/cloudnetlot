<template >
    <div class="form_box"
        v-loading.fullscreen.lock="fullscreenLoading"
        :element-loading-text="loading_txt"
        :element-loading-background="loading_bgcolor"
    >
        <h1 style="color:#909399; text-align:center;line-height:10">
            <i class="el-icon-warning-outline"></i>
            您当前的账号正在审核过程中，请您耐心等待，如有疑问请联系管理员！
        </h1>   
    </div>
</template>
<script>
import store from '@/projects/develop/store'
export default {
    data() {
        return {
            fullscreenLoading:false,
            loading_txt:"正在登陆，请稍后……",
            loading_bgcolor:"rgba(0, 0, 0, 0.7)",         
        };
    },
    methods: {
        getUserInfo(){
            store.dispatch('GetInfo').then(infoRes => { // 拉取用户信息
                if(infoRes.status==10000){
                    if(infoRes.data.status=='0'||infoRes.data.status=='2'){//0:未申请开发者1：申请审核中2：审核未通过，3：审核通过
                        this.$router.push({ path: '/register_develop' });
                    }else if(infoRes.data.status=='3'){
                        this.$router.push({ path: '/product/list' });
                    }else{
                        
                    }
                }else{	
                    Message.$message({
                        message:"获取用户信息失败",
                        type: 'error',
                        offset:100
                    });
                }
            }).catch((err) => {
            })
        }   
    },
    mounted(){
        this.getUserInfo();
    }
}
</script>
<style scoped>

</style>