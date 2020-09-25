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
                <el-row :gutter="30">
                    <el-col :xs="14" :sm="14" :lg="14">
                        <el-carousel :interval="5000"  trigger="click" height="400px" arrow="always">
                            <el-carousel-item v-for="item in roll_arr" :key="item">
                                <img :src="item" />
                            </el-carousel-item>
                        </el-carousel>
                    </el-col>
                    <el-col :xs="10" :sm="10" :lg="10">
                        <div class="form_box">
                            <div class="account_login">
                                <el-form :model="loginForm" :rules="rules" ref="loginForm" >
                                    <el-form-item label-position="top" label="账号" prop="account" class="put_mb25" :show-message="false" >
                                        <el-input v-model="loginForm.account" autocomplete="off" placeholder="请输入账号" clearable></el-input>
                                    </el-form-item>
                                    <el-form-item label-position="top" label="密码" prop="password" class="put_mb25" :show-message="false" >
                                        <el-input type="password" v-model="loginForm.password" placeholder="请输入密码" autocomplete="off" show-password clearable></el-input>
                                    </el-form-item>
                                </el-form>
                                <div>
                                    <div class="flt">
                                        <el-checkbox v-model="loginForm.remember_account">记住账号</el-checkbox>
                                    </div>
                                   
                                    <div class="clear"></div>
                                </div>
                                <el-button class="sub_btn" type="primary" @click="submitAccount('loginForm')" >登录</el-button>
                                                                
                            </div>
                        </div>
                    </el-col>
                </el-row>
            </div>
            <!-- <el-footer class="login_footer" :style="'background:url('+ligon_footer_bg+') repeat-x;'">
                公司自行研发众多软件设备，均支持云平台接口使用，智能路由器、智能远距离无线网桥、AC系列产品等等。 粤ICP备13056814号-3
            </el-footer> -->
        </el-main>
        
    </el-container>
</template>
<script>
// 引入base64
const Base64 = require('js-base64').Base64
// js-cookie
import Cookies from 'js-cookie'
//引入图片
import Header from '@/projects/develop/assets/Out_header/header.vue';
import img_bg from '@/projects/develop/assets/login/login_bg.jpg';
import account_ico_active from '@/projects/develop/assets/login/login_account_ico_active.png';
import account_ico from '@/projects/develop/assets/login/login_account_ico.png';
import phone_ico_active from '@/projects/develop/assets/login/login_phone_ico_active.png';
import phone_ico from '@/projects/develop/assets/login/login_phone_ico.png';
import advertising_img from '@/projects/develop/assets/login/advertising1.jpg';
import ligon_footer_bg from '@/projects/develop/assets/login/ligon_footer_bg.png';

//引入check_lib自定义校验库
import checkObj from '@/public/js/check_lib.js';

export default {
    data(){
        //el-input 增加 :validate-event="false" 属性，去掉即时校验，等待submit的时候校验
        let checkAccount = (rule, value, callback) => {
            let checkStatus = checkObj.check_account(value)
            returnInfos(checkStatus,this.$t("check."+checkStatus),callback);
        }
        let checkPwd = (rule, value, callback) => {
            let checkStatus = checkObj.check_user_pwd(value);
            returnInfos(checkStatus,this.$t("check."+checkStatus),callback);
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
            loading_txt:"正在登陆，请稍后……",
            loading_bgcolor:"rgba(0, 0, 0, 0.7)",

            lang:this.$i18n.locale,
            img_bg:img_bg,
            account_ico_active:account_ico_active,
            account_ico:account_ico,
            phone_ico_active:phone_ico_active,
            phone_ico:phone_ico,
            ligon_footer_bg:ligon_footer_bg,
            roll_arr:[advertising_img],
            activeTables:"account",
            loginForm:{
                account:'',
                password:'',
                remember_account:false
            },

            rules: {
                account: [
                    { validator: checkAccount, trigger: 'blur' }
                ],
                password:[
                     { validator: checkPwd, trigger: 'blur' }
                ]
            }
        }
    },
    components:{
        'header-counter':Header
    },
    methods: {
         // 储存表单信息
        saveUserInfo() {
            // 判断用户是否勾选记住密码，如果勾选，向cookie中储存登录信息，
            // 如果没有勾选，储存的信息为空
            if(this.loginForm.remember_account){
               Cookies.set("account",this.loginForm.account)
                // base64加密密码
                let passWord = Base64.encode(this.loginForm.password)
                Cookies.set("password",passWord)    
            }else{
                Cookies.set("account","")
                Cookies.set("password","") 
            } 
        },
        
        submitAccount(formName) {   //账密登录
            let _this = this;
            this.$refs[formName].validate((valid) => {
                if (valid) {
                    _this.fullscreenLoading = true;
                    _this.$store.dispatch('Login', this.loginForm).then((authRes) => {
                        if(authRes.status==10000){
                            _this.saveUserInfo();
                            _this.$router.push({ path: '/product/list' })  
                        }                
                    }).catch((error) => {
                        this.fullscreenLoading = false;
                    })
                } else {
                    return false;
                }
            });
        },
        
    },
    
    created () {
        // 在页面加载时从cookie获取登录信息
        let account = Cookies.get("account");
        let phone = Cookies.get("phone");
        // 如果存在赋值给表单，并且将记住密码勾选
        if(account!=""&&account!=undefined){
            let password = Base64.decode(Cookies.get("password"))
            this.loginForm.account = account;
            this.loginForm.password = password;
            this.loginForm.remember_account = true;
        }
    },
    mounted(){
        let _this = this;
        document.onkeydown = function(event) {
            let e = event ? event : (window.event ? window.event : null);
            if (e.keyCode == 13) {
                _this.submitAccount('loginForm');
            }
        }
    }
}
</script>
<style lang="scss" scoped>
@import '@/projects/develop/styles/index.scss';

.login_main { width:100%; height: calc(100vh - 70px); background-size: cover; padding: 0;} 
.login_conrainer { width: 1180px; height: 400px; margin: 10% auto 0 auto;}
.form_box { width: 100%; height: 400px; padding: 40px 0; border-radius: 5px; background: $white_bg;}

.account_login { width: 300px; margin:20px auto 0 auto;}
.sub_btn { width: 100%; height: 40px; margin-top: 25px;}
.register_txt { line-height: 100px; text-align: center;}

.login_footer {width: 100%; line-height: 50px; text-align: center; color: $white_font; position: absolute; bottom: 50px; left: 0;}
</style>