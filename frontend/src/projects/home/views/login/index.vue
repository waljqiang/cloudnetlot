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
                            <el-tabs v-model="activeTables" >
                                <el-tab-pane name="account">
                                    <span slot="label" > <img :src="activeTables=='account'?account_ico_active:account_ico"  style="vertical-align:middle" /> 密码登录</span>
                                    <div class="account_login">
                                        <el-form :model="loginForm" :rules="rules" ref="loginForm" >
                                            <el-form-item label="" prop="account" class="put_mb10" :show-message="false" >
                                                <el-input v-model="loginForm.account" autocomplete="off" placeholder="请输入账号" clearable></el-input>
                                            </el-form-item>
                                            <el-form-item label="" prop="password" class="put_mb10" :show-message="false" >
                                                <el-input type="password" v-model="loginForm.password" placeholder="请输入密码" autocomplete="off" show-password clearable></el-input>
                                            </el-form-item>
                                        </el-form>
                                        <div>
                                            <div class="flt">
                                                <el-checkbox v-model="loginForm.remember_account">记住账号</el-checkbox>
                                            </div>
                                            <div class="frt">
                                                <a >忘记密码？</a>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <el-button class="sub_btn" type="primary" @click="submitAccount('loginForm')" >登录</el-button>
                                        <div class="register_txt">
                                            <a @click="goRegister">
                                                还没有账号？现在去注册。
                                                <span class="blue_font">立即注册</span>
                                            </a>
                                        </div>
                                        
                                    </div>
                                </el-tab-pane>
                                <el-tab-pane name="phone">
                                    <span slot="label"  > <img :src="activeTables=='phone'?phone_ico_active:phone_ico" style="vertical-align:middle" /> 短信登录</span>
                                    <div class="account_login">
                                        <el-row :gutter="10">
                                            <el-col :lg="7">
                                                <el-select v-model="phoneForm.select_code" class="flt">
                                                    <el-option
                                                    v-for="item in country_code"
                                                    :key="item.phonecode"
                                                    :label="'+'+item.phonecode"
                                                    :value="item.phonecode">
                                                    </el-option>
                                                </el-select>
                                            </el-col>
                                            <el-col :lg="17">
                                                <el-input
                                                    class="put_mb10"
                                                    placeholder="请输入手机号码"
                                                    v-model="phoneForm.phone"
                                                    clearable>
                                                </el-input>
                                            </el-col>
                                        </el-row>
                                        <el-row :gutter="10">
                                            <el-col :lg="16">
                                                <el-input
                                                    class="put_mb10"
                                                    placeholder="请输入验证码"
                                                    v-model="phoneForm.verify"
                                                    clearable>
                                                </el-input>
                                            </el-col>
                                            <el-col :lg="8">
                                                <el-button type="primary" style="width:100%" :disabled="countdownNum>0" @click="getVerify" v-text="verify_txt"></el-button>
                                            </el-col>
                                        </el-row>
                                        <div>
                                            <el-checkbox v-model="phoneForm.remember_phone">记住手机号</el-checkbox>
                                        </div>
                                        <el-button class="sub_btn" type="primary">登录</el-button>
                                        <div class="register_txt">
                                            <a  @click="goRegister">
                                                还没有账号？现在去注册。
                                                <span class="blue_font">立即注册</span>
                                            </a>
                                        </div>
                                    </div>
                                </el-tab-pane>
                            </el-tabs>
                        </div>
                    </el-col>
                </el-row>
            </div>
            <el-footer class="login_footer" :style="'background:url('+ligon_footer_bg+') repeat-x;'">
                公司自行研发众多软件设备，均支持云平台接口使用，智能路由器、智能远距离无线网桥、AC系列产品等等。 粤ICP备13056814号-3
            </el-footer>
        </el-main>
        
    </el-container>
</template>
<script>
// 引入base64
const Base64 = require('js-base64').Base64
// js-cookie
import Cookies from 'js-cookie'
//引入图片
import Header from '@/projects/home/assets/Out_header/header.vue';
import img_bg from '@/projects/home/assets/login/login_bg.jpg';
import account_ico_active from '@/projects/home/assets/login/login_account_ico_active.png';
import account_ico from '@/projects/home/assets/login/login_account_ico.png';
import phone_ico_active from '@/projects/home/assets/login/login_phone_ico_active.png';
import phone_ico from '@/projects/home/assets/login/login_phone_ico.png';
import advertising_img from '@/projects/home/assets/login/advertising1.jpg';
import ligon_footer_bg from '@/projects/home/assets/login/ligon_footer_bg.png';

//引入check_lib自定义校验库
import checkObj from '@/public/js/check_lib.js';

export default {
    data(){
        //el-input 增加 :validate-event="false" 属性，去掉即时校验，等待submit的时候校验
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

            country_code:[ {
                "name_en": "Andorra",
                "name_zh": "安道尔",
                "short2": "AD",
                "short3": "AND",
                "num": "020",
                "phonecode": "376"
            }],
            
            phoneForm:{
                select_code:"",
                phone:"",
                verify:"",
                remember_phone:false
            },
            verify_txt:"获取验证码",
            countdownNum:0,

            rules: {
                account: [
                    { validator: (rule, value, callback) => {
                        checkVal(rule, value, callback, 'check_account')
                    }, required: true, trigger: 'blur' }
                ],
                password:[
                     { validator: (rule, value, callback) => {
                        checkVal(rule, value, callback, 'check_user_pwd')
                    }, required: true, trigger: 'blur' }
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
        savePhoneInfo(){
             // 判断用户是否勾选记住phone，如果勾选，向cookie中储存登录信息，
            // 如果没有勾选，储存的信息为空
            if(this.phoneForm.remember_phone){
               Cookies.set("phone",this.phoneForm.phone)
            }else{
                Cookies.set("phone","")
            } 
        },
        getVerify(){
            var _this = this;
            _this.countdownNum = 60;
            _this.verify_txt = _this.countdownNum+"秒后重发";
           
            let verifyCountdown = setInterval(function(){
                if(_this.countdownNum<=1){ 
                    _this.verify_txt = "获取验证码";
                    _this.countdownNum = 0;
                    clearInterval(verifyCountdown);
                    return;
                }
                _this.countdownNum -= 1;
                 _this.verify_txt = _this.countdownNum+"秒后重发";
            },1000)
        },
        submitAccount(formName) {   //账密登录
            let _this = this;
            this.$refs[formName].validate((valid) => {
                if (valid) {
                    this.fullscreenLoading = true;
                    this.$store.dispatch('Login', this.loginForm).then((authRes) => {
                        if(authRes.status==10000){
                            _this.saveUserInfo();
                            this.$router.push({ path: '/device/list' })   
                        }                
                    }).catch((error) => {
                        this.fullscreenLoading = false;
                    })
                } else {
                    return false;
                }
            });
        },
        goRegister(){
            this.$router.push({ path: '/register' })  
            
        }
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
        this.phoneForm.select_code = this.country_code[0].phonecode;

        if(phone!=""&&phone!=undefined){
            this.phoneForm.phone = phone;
            this.phoneForm.remember_phone = true;
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
@import '@/projects/home/styles/index.scss';
.login_conrainer { width: 1180px; height: 400px; margin: 10% auto 0 auto;}
.form_box { width: 100%; height: 400px; padding: 40px 0; border-radius: 5px; background: $white_bg;}

.account_login { width: 300px; margin:20px auto 0 auto;}
.sub_btn { width: 100%; height: 40px; margin-top: 25px;}
.register_txt { line-height: 100px; text-align: center;}

</style>