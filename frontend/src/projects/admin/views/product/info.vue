<template>
    <div
        v-loading.fullscreen.lock="fullscreenLoading"
        :element-loading-text="loading_txt"
        :element-loading-background="loading_bgcolor">
        <el-page-header @back="goBack" content="产品详情" style="line-height:50px" ></el-page-header>
         <el-divider></el-divider>
        <el-form  label-width="200px" >
            <el-form-item label="产品型号" >
                <span v-text="infos.size"></span>
            </el-form-item>
            <el-form-item label="产品类型" >
                <span  v-text="infos.type"></span>
            </el-form-item>
            <el-form-item label="产品名称" >
                <span v-text="infos.prtname"></span>
            </el-form-item>
            <el-form-item label="状态">
                <span v-if="infos.status==1" >注册未发布</span>
                <span v-else-if="infos.status==2">发布审核中</span>
                <span v-else-if="infos.status==3" >审核未通过</span>
                <span v-else >审核通过</span>
            </el-form-item>
            <el-form-item label="创建时间" >
                <span v-text="infos.created_at"></span>
            </el-form-item>
            <el-form-item label="所属用户" >
                <span v-text="infos.username"></span>
            </el-form-item>
        </el-form>
    </div>
</template>
<script>
import {productInfo} from '@/projects/admin/api/product'
export default {
    data(){
        return {
            fullscreenLoading:false,
            loading_txt:"请稍后……",
            loading_bgcolor:"rgba(0, 0, 0, 0.7)",
           
            infos:{
                
            }
  
        }
    },
    methods:{
        goBack(){
             this.$router.push({path:"/product/list"})
        },
        getInfo(){
            productInfo({prtid:this.$route.query.id}).then(response => {
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


