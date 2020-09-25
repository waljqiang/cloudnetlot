<template >
    <div class="form_box"
        v-loading.fullscreen.lock="fullscreenLoading"
        :element-loading-text="loading_txt"
        :element-loading-background="loading_bgcolor"
    >
        <el-form :model="registerForm" :rules="rules" ref="registerForm" label-width="200px" >
            <el-form-item label="申请人姓名" prop="name" :show-message="false">
                <el-input v-model="registerForm.name"></el-input>
            </el-form-item>
            <el-form-item label="身份证号码" prop="idCard" :show-message="false">
                <el-input v-model="registerForm.idCard"></el-input>
            </el-form-item>
            <el-form-item label="企业名称" prop="enterprise" :show-message="false">
                <el-input v-model="registerForm.enterprise"></el-input>
            </el-form-item>
            <el-form-item label="企业社会统一信用码" prop="enterpriseCode" :show-message="false">
                <el-input v-model="registerForm.enterpriseCode"></el-input>
            </el-form-item>
            <el-form-item label="企业描述" prop="enterpriseDesc" :show-message="false">
                <el-input type="textarea" v-model="registerForm.enterpriseDesc"></el-input>
            </el-form-item>
        </el-form>

        <el-button type="primary" @click="onSubmit('registerForm')" >提交</el-button>
    </div>
</template>
<script>
//引入check_lib自定义校验库
import checkObj from '@/public/js/check_lib.js';
import {develop} from '@/projects/develop/api/user'
export default {
    data() {
        let checkName = (rule, value, callback) => {
            let checkStatus = checkObj.check_blank(value);
            returnInfos(checkStatus,'请输入姓名',callback);
        }
        let checkIdCard = (rule, value, callback) => {
            let checkStatus = checkObj.check_blank(value);
            returnInfos(checkStatus,'请输入身份证号',callback);
        }
        let checkEnterprise = (rule, value, callback) => {
            let checkStatus = checkObj.check_blank(value);
            returnInfos(checkStatus,'请输入企业名称',callback);
        }
        let checkEnterpriseCode = (rule, value, callback) => {
            let checkStatus = checkObj.check_blank(value);
            returnInfos(checkStatus,'请输入企业社会统一信用码',callback);
        }
        let checkEnterpriseDesc = (rule, value, callback) => {
            let checkStatus = checkObj.check_blank(value);
            returnInfos(checkStatus,'请输入企业描述',callback);
        }
        let returnInfos = (checkStatus,infos,callback) => {
            if (checkStatus!=true) {
                setTimeout(() => {
                    this.$message({
                        message: infos,
                        type: 'error',
                        offset:100
                    });
                }, 300);
                
                return callback(new Error("*"));
            }else{
                callback()
            }
        }
        return {
            fullscreenLoading:false,
            loading_txt:"请稍后……",
            loading_bgcolor:"rgba(0, 0, 0, 0.7)",
            
            registerForm:{
                name:"",
                idCard:"",
                enterprise:"",
                enterpriseCode:"",
                enterpriseDesc:"",
                // lang:this.$i18n.locale
            },
            rules: {
                name: [
                    {required: true, validator: checkName, trigger: 'blur' }
                ],
                idCard:[
                    {required: true, validator: checkIdCard, trigger: 'blur' }
                ],
                enterprise:[
                    {required: true, validator: checkEnterprise, trigger: 'blur' }
                ],
                enterpriseCode:[
                    {required: true, validator: checkEnterpriseCode, trigger: 'blur' }
                ],
                enterpriseDesc:[
                    {required: true, validator: checkEnterpriseDesc, trigger: 'blur' }
                ]
            }
        };
    },
    methods: {
        onSubmit(formName){
            let _this = this;
            this.$refs[formName].validate((valid) => {
                if (valid) {
                    _this.fullscreenLoading = true;
                    develop(_this.registerForm).then(response => {
                        _this.fullscreenLoading = false;
                        if(response.status==10000){
                            this.$message({
                                message: "操作成功",
                                type: 'success',
                                offset:100
                            });
                            setTimeout(() => {
                                _this.$router.push({ path: '/product/list' })
                            }, 1000);
                        }else{
                           this.$message({
                                message:"操作失败",
                                type: 'error',
                                offset:100
                            });
                        }
                    }).catch(error => {
                        _this.fullscreenLoading = false;
                        this.$message({
                            message:"请求失败",
                            type: 'error',
                            offset:100
                        });
                    })
                }else {
                    return false;
                }
            })
            
        }
    },
    mounted(){
        
    }
}
</script>
<style scoped>

</style>