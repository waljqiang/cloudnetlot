<template>
    <el-container v-cloak >
        <el-header height="70px">
            <header-counter></header-counter>
        </el-header>
        <el-main class="login_main" :style="'background:url('+img_bg+') no-repeat;'" >
            <!-- <img src="@/assets/login_bg.jpg" class="bg_img" /> -->
            <div class="login_conrainer"      
                v-loading.fullscreen.lock="$store.state.globalState.loadding.show"
                :element-loading-text="$store.state.globalState.loadding.text"
                :element-loading-background="$store.state.globalState.loadding.bg"
            >
                <el-row :gutter="30">
                    <el-col :xs="0" :sm="14" :md='14' :lg="14">
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
                                    <span slot="label" > <img :src="activeTables=='account'?account_ico_active:account_ico"  style="vertical-align:middle" /> {{$t('common.pwd_login')}}</span>
                                    <div class="account_login">
                                        <el-form :model="loginForm" ref="loginForm" >
                                            <el-form-item label=""  class="put_mb10" :show-message="false" >
                                                <el-input v-model="loginForm.account" autocomplete="off" :placeholder="$t('msg.account_empty')" clearable></el-input>
                                            </el-form-item>
                                            <el-form-item label="" class="put_mb10" :show-message="false" >
                                                <el-input type="password" v-model="loginForm.password" :placeholder="$t('msg.pwd_empty')" autocomplete="off" show-password clearable></el-input>
                                            </el-form-item>
                                        </el-form>
                                        <div>
                                            <div class="flt">
                                                <el-checkbox v-model="loginForm.remember_account">{{$t('common.member_account')}}</el-checkbox>
                                            </div>
                                            <div class="frt" @click="sendMail" >
                                                <a >{{$t('common.forget_pwd')+"?"}}</a>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <el-button class="sub_btn" type="primary" @click="submitAccount()" >{{$t('common.login_btn')}}</el-button>
                                        <div class="register_txt">
                                            <a @click="goRegister">
                                                {{$t('common.no_account_registered_tips')}}
                                                <span class="blue_font">{{$t('common.now_register')}}</span>
                                            </a>
                                        </div>
                                        
                                    </div>
                                </el-tab-pane>
                                <!-- <el-tab-pane name="phone">
                                    <span slot="label"  > <img :src="activeTables=='phone'?phone_ico_active:phone_ico" style="vertical-align:middle" /> {{$t('common.phone_login')}}</span>
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
                                </el-tab-pane> -->
                            </el-tabs>
                        </div>
                    </el-col>
                </el-row>
            </div>
            <el-footer class="login_footer" :style="'background:url('+ligon_footer_bg+') repeat-x;'">
                {{$t('common.footer_tips')}}
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

import {getCountrycode} from '@/projects/home/api/system'
export default {
    data(){       
        return {
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

            country_code:[],
            
            phoneForm:{
                select_code:"",
                phone:"",
                verify:"",
                remember_phone:false
            },
            verify_txt:this.$t('common.get_verify'),
            countdownNum:0,
            
            rules:[]
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
            _this.verify_txt = _this.countdownNum+this.$t('common.post_verify_tips');
            let verifyCountdown = setInterval(function(){
                if(_this.countdownNum<=1){ 
                    _this.verify_txt = this.$t('common.get_verify');
                    _this.countdownNum = 0;
                    clearInterval(verifyCountdown);
                    return;
                }
                _this.countdownNum -= 1;
                _this.verify_txt = _this.countdownNum+this.$t('common.post_verify_tips');
            },1000)
        },
        submitAccount() {   //账密登录
            this.rules = [
                {val: this.loginForm.account, rule: "check_account"},
                {val: this.loginForm.password, rule: "check_user_pwd"}
            ]
            for(var i=0;i<this.rules.length;i++){
                let checkFun = this.rules[i].rule;
                let val = this.rules[i].val;
                let checkStatus = checkObj[checkFun](val,'login');
                if (checkStatus!=true) {
                    this.$message({
                        message: this.$t("check."+checkStatus),
                        type: 'error',
                        offset:100
                    });
                    return false;
                }
            }
            this.$store.commit('showloadding',{show:true});
            this.$store.dispatch('Login', this.loginForm).then((authRes) => {
                if(authRes.status==10000){
                    this.saveUserInfo();
                    setTimeout(() => {
                        this.$router.push({ path: '/home/main' })  
                    }, 500);  
                }                
            }).catch((error) => {
                this.$store.commit('showloadding',{show:false});
                let err = {};
                let errCode = error.errorCode[0];
                switch (errCode) {
                    case 600400107:
                        err.message = this.$t('msg.account_pwd_error');
                        err.tips = true;
                        break;
                    case 600400112:
                        err.message = this.$t('msg.account_pwd_error');
                        err.tips = true;
                        break;
                    case 600400113:
                        err.message = this.$t('msg.account_pwd_error');
                        err.tips = true;
                        break;
                    case 600400114:
                        err.message = this.$t('msg.account_pwd_error');
                        err.tips = true;
                        break;
                    case 600400115:
                        err.message = this.$t('msg.account_pwd_error');
                        err.tips = true;
                        break;
                    case 600400116:
                        err.message = this.$t('msg.account_pwd_error');
                        err.tips = true;
                        break;
                    case 600400117:
                        err.message = this.$t('msg.account_pwd_error');
                        err.tips = true;
                        break;
                    case 600400138:
                        err.message = this.$t('msg.account_pwd_error');
                        err.tips = true;
                        break; 
                    default:
                        err.message = this.$t('msg.login_error');
                }
                this.$message({
                    message: err.message,
                    type: 'error',
                    offset:100
                });
            })
        },
        goRegister(){
            this.$router.push({ path: '/register' })  
        },
        sendMail(){
            this.$router.push({path:'/sendEmail'})
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
        
        if(phone!=""&&phone!=undefined){
            this.phoneForm.phone = phone;
            this.phoneForm.remember_phone = true;
        }

        getCountrycode({lang:this.lang}).then(response => {
            if(response.status==10000){
                this.country_code = response.data.list;
                this.phoneForm.select_code = this.country_code[0].phonecode;
            } 
        }).catch(error => {
            this.$message({
                message: this.$t('msg.country_code_get_err'),
                type: 'error',
                offset:100
            });
        })
    },
    beforeCreate(){
        this.$store.commit('showloadding',{show:false,text:this.$t('common.plase_wait')});
    },
    mounted(){
        let _this = this;
        document.onkeydown = function(event) {
            let e = event ? event : (window.event ? window.event : null);
            if (e.keyCode == 13) {
                _this.submitAccount('loginForm');
            }
        }
    },
    beforeDestroy() {
        this.$message.closeAll();
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
@media screen and (max-width: 1280px) {
    body {
        background-color:lightblue;
    }
}

</style>