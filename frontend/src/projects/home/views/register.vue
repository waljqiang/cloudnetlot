<template>
    <el-container >
        <el-header height="70px">
            <header-counter></header-counter>
        </el-header>
        <el-main class="login_main" :style="'background:url('+img_bg+') no-repeat;'" >
            <!-- <img src="@/assets/login_bg.jpg" class="bg_img" /> -->
            <div class="login_conrainer"      
                v-loading.fullscreen.lock="fullscreenLoading"
                :element-loading-text="loading_txt"
                :element-loading-background="loading_bgcolor"
            >
                <div style="line-height:50px">
                    <div class="tit_ico"></div>
                   <span>注册账号</span> 
                </div>
                <el-form :model="registerForm" label-position="left" style="width:55%; margin:0 auto;" :rules="rules" ref="registerForm" label-width="30%" >
                    <el-form-item label="账号" prop="account" :show-message="false">
                        <el-input v-model="registerForm.account"></el-input>
                    </el-form-item>
                    <el-form-item label="昵称" prop="name" :show-message="false">
                        <el-input v-model="registerForm.name"></el-input>
                    </el-form-item>
                    <el-form-item label="密码" prop="pwd" :show-message="false">
                        <el-input show-password v-model="registerForm.pwd"></el-input>
                    </el-form-item>
                    <el-form-item label="确认密码" prop="pwd_confirm" :show-message="false">
                        <el-input show-password v-model="registerForm.pwd_confirm"></el-input>
                    </el-form-item>
                    <el-form-item label="手机" prop="phone_num" :show-message="false">
                        <el-row :gutter="10">
                            <el-col :span="7">
                                <el-select v-model="registerForm.phone_code" class="flt">
                                    <el-option
                                    v-for="item in country_code"
                                    :key="item.phonecode"
                                    :label="item.phonecode"
                                    :value="item.phonecode">
                                        <div class="flt">{{ item.name }}</div>
                                        <div class="frt">{{ item.phonecode }}</div>
                                    </el-option>
                                </el-select>
                            </el-col>
                            <el-col :span="16" class="align_r">
                                <el-input
                                    placeholder="请输入手机号码"
                                    v-model="registerForm.phone_num"
                                    clearable>
                                </el-input>
                            </el-col>
                        </el-row>
                    </el-form-item>
                    <el-form-item label="邮箱" prop="email" :show-message="false">
                        <el-input v-model="registerForm.email"></el-input>
                    </el-form-item>
                    <!-- <el-form-item v-if="lang=='zh-cn'" label="地址信息" prop="address" :show-message="false">
                        <el-input  v-model="registerForm.address"></el-input>
                    </el-form-item> -->
                    <el-form-item label="详细地址" prop="address_detailed" :show-message="false">
                        <el-input  v-model="registerForm.address_detailed"></el-input>
                    </el-form-item>
                    <el-form-item label="" >
                        <el-checkbox v-model="registerForm.register_protocol"></el-checkbox>
                        <a>我已阅读并接受用户注册协议</a>
                    </el-form-item>
                    <el-form-item label="" class="align_r" >
                        <el-button @click="goLogin">返回登录</el-button>
                        <el-button @click="subRegister('registerForm')" type="primary">注册账号</el-button>
                    </el-form-item>
                </el-form>
            </div>
            <el-footer class="login_footer" :style="'background:url('+ligon_footer_bg+') repeat-x;'">
                公司自行研发众多软件设备，均支持云平台接口使用，智能路由器、智能远距离无线网桥、AC系列产品等等。 粤ICP备13056814号-3
            </el-footer>
            
        </el-main>
        
    </el-container>
</template>
<script>

// js-cookie
import Cookies from 'js-cookie'
//引入图片
import Header from '@/projects/home/assets/Out_header/header.vue';
import img_bg from '@/projects/home/assets/login/login_bg.jpg';

import ligon_footer_bg from '@/projects/home/assets/login/ligon_footer_bg.png';

//引入check_lib自定义校验库
import checkObj from '@/public/js/check_lib.js';

import {getCountrycode} from '@/projects/home/api/system'
import {register} from '@/projects/home/api/user'

export default {
    data(){
        //el-input 增加 :validate-event="false" 属性，去掉即时校验，等待submit的时候校验
        let checkPwdConfirm = (rule, value, callback) => {
            if(value!=this.registerForm.pwd){
                setTimeout(() => {
                    this.$message({
                        message: "两次输入的密码不相同",
                        type: 'error',
                        offset:100
                    });
                }, 300);
               return callback(new Error("*"));
            }
            checkVal(rule, value, callback,'check_user_pwd');
        }
        let checkAdress = (rule, value, callback) => {
            if(value==""){
                setTimeout(() => {
                    this.$message({
                        message: "地址不能为空",
                        type: 'error',
                        offset:100
                    });
                }, 300);
               return callback(new Error("*"));
            }else{
                callback()
            }
        }
        let checkVal = (rule, value, callback,checkFun) => {
            let checkStatus = checkObj[checkFun](value);
            if (checkStatus!=true) {
                setTimeout(() => {
                    this.$message({
                        message: this.$t("check."+checkStatus),
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
            loading_txt:"正在注册，请稍后……",
            loading_bgcolor:"rgba(0, 0, 0, 0.7)",

            lang:this.$i18n.locale,
            img_bg:img_bg,
            ligon_footer_bg:ligon_footer_bg,

            registerForm:{
                account:"",
                name:"",
                pwd:"",
                pwd_confirm:"",
                phone_code:"",
                phone_num:"",
                email:"",
                address:"",
                address_detailed:"",
                register_protocol:"",
            },
            country_code:[],
            rules: {
                account: [
                    { validator: (rule, value, callback) => {
                        checkVal(rule, value, callback, 'check_account')
                    }, required: true, trigger: 'blur' }
                ],
                pwd:[
                    { validator: (rule, value, callback) => {
                        checkVal(rule, value, callback, 'check_user_pwd')
                    }, required: true, trigger: 'blur' }
                ],
                pwd_confirm:[
                    { validator: checkPwdConfirm, required: true, trigger: 'blur' }
                ],
                phone_num: [
                    { validator: (rule, value, callback) => {
                        checkVal(rule, value, callback, 'check_phone')
                    }, required: true, trigger: 'blur' }
                ],
                email: [
                    { validator: (rule, value, callback) => {
                        checkVal(rule, value, callback, 'check_email')
                    }, required: true, trigger: 'blur' }
                ],
                address_detailed: [
                    { validator: checkAdress, required: true, trigger: 'blur' }
                ]
                
            }
        }
    },
    components:{
        'header-counter':Header
    },
    methods: {
        subRegister(formName){
            this.$refs[formName].validate((valid) => {
                if (valid) {
                    if(!this.registerForm.register_protocol){
                        this.$message({
                            message: '请先阅读并接受用户注册协议',
                            type: 'warn',
                            offset:100
                        });
                    }
                    this.fullscreenLoading = true;
                    let postData = {
                        "username":this.registerForm.account,
                        "nickname":this.registerForm.name,
                        "password":this.registerForm.pwd,
                        "password_confirmation":this.registerForm.pwd_confirm,
                        "phonecode":this.registerForm.phone_code,
                        "phone":this.registerForm.phone_num,
                        "email":this.registerForm.email,
                        "address":this.registerForm.address_detailed
                    }
                    register(postData).then(response => {
                        if(response.status==10000){
                            this.$message({
                                message: '注册成功',
                                type: 'success',
                                offset:100,
                                duration: 3 * 1000
                            });
                            setTimeout(() => {
                                this.$router.push({ path: '/login' })  
                            }, 3000);
                        }else{
                            this.$message({
                                message: '注册失败',
                                type: 'error',
                                offset:100
                            });
                        } 
                    }).catch(error => {
                        console.log(error)
                    })
                } else {
                    return false;
                }
            });  
        },
        goLogin(){
            this.$router.push({ path: '/login' })  
        }
    },
    created(){
        getCountrycode({lang:this.lang}).then(response => {
            if(response.status==10000){
                this.country_code = response.data.list;
            }else{
                this.$message({
                    message: '国家码获取失败',
                    type: 'error',
                    offset:100
                });
            } 
        }).catch(error => {
            console.log(error)
        })
    },
    mounted(){
        
    }
}
</script>
<style lang="scss" scoped>
@import '@/styles/index.scss';
.login_conrainer { width: 850px; min-height: 400px; height: auto; margin: 1% auto 0 auto; padding: 20px; border-radius: 5px; background: $white_bg;}
</style>