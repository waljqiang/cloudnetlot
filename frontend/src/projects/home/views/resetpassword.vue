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
                <div style="line-height:50px">
                    <div class="tit_ico"></div>
                   <span>{{$t('common.pwd_reset')}}</span> 
                </div>
                <el-form :model="pwdForm" label-position="left" style="width:55%; margin:0 auto;" ref="pwdForm" label-width="20%" >
                    <el-form-item :label="$t('common.pwd_new')" >
                        <el-input show-password v-model="pwdForm.pwd"></el-input>
                    </el-form-item>
                    <el-form-item :label="$t('common.pwd_confirm')" >
                        <el-input show-password v-model="pwdForm.pwd_confirm"></el-input>
                    </el-form-item>
                    
                    
                    <el-form-item label="" class="align_r" >
                        <el-button @click="reset" type="primary">{{$t('common.confirm_btn')}}</el-button>
                    </el-form-item>
                </el-form>
            </div>
            <el-footer class="login_footer" :style="'background:url('+ligon_footer_bg+') repeat-x;'">
                {{$t('common.footer_tips')}}
            </el-footer>
            
        </el-main>
        
    </el-container>
</template>
<script>

//引入图片
import Header from '@/projects/home/assets/Out_header/header.vue';
import img_bg from '@/projects/home/assets/login/login_bg.jpg';

import ligon_footer_bg from '@/projects/home/assets/login/ligon_footer_bg.png';

//引入check_lib自定义校验库
import checkObj from '@/public/js/check_lib.js';
import {resetPwd,checkmail} from '@/projects/home/api/user'

export default {
    data(){
        return {
            lang:this.$i18n.locale,
            img_bg:img_bg,
            ligon_footer_bg:ligon_footer_bg,
            pwdForm:{
                pwd:"",
                pwd_confirm:""
            },
            rules: []
        }
    },
    components:{
        'header-counter':Header
    },
    methods: {
        reset(formName){
            this.rules = [
                {val: this.pwdForm.pwd, rule: "check_user_pwd"},
                {val: this.pwdForm.pwd_confirm, rule: "check_user_pwd"},
            ]
            for(var i=0;i<this.rules.length;i++){
                let checkFun = this.rules[i].rule;
                let val = this.rules[i].val;
                let checkStatus = checkObj[checkFun](val);
                if (checkStatus!=true) {
                    this.$message({
                        message: this.$t("check."+checkStatus),
                        type: 'error',
                        offset:100
                    });
                    return false;
                }
            }
            if(this.pwdForm.pwd_confirm!=this.pwdForm.pwd){
                this.$message({
                    message: this.$t('msg.pwd_set_error_tips4'),
                    type: 'error',
                    offset:100
                });
                return false;
            }
            this.$store.commit('showloadding',{show:true});
            let postData = {
                "password":this.pwdForm.pwd,
                "password_confirmation":this.pwdForm.pwd_confirm,
                "content":this.GetRequest()
            }
            resetPwd(postData).then(response => {
                if(response.status==10000){
                    this.$message({
                        message: this.$t('msg.set_success_tips2'),
                        type: 'success',
                        offset:100
                    });
                    setTimeout(() => {
                        this.$router.push({ path: '/login' })  
                    }, 1500);
                } 
            }).catch(error => {
                this.$store.commit('showloadding',{show:false});
                let err = {};
                let errCode = error.errorCode[0];
                switch (errCode) {
                    case 600400115:
                        err.message = this.$t('msg.pwd_set_error_tips2');
                        break;
                    case 600400116:
                        err.message = this.$t('check.pwd_set_error_tips');
                        break;
                    case 600400117:
                        err.message = this.$t('check.pwd_set_error_tips');
                        break;
                    case 600400131:
                        err.message = this.$t('msg.link_err1');
                        break;
                    case 600400132:
                        err.message = this.$t('msg.link_err2');
                        break;
                    case 600400136:
                        err.message = this.$t('msg.pwd_set_error_tips4');
                        break;
                    default:
                        err.message = this.$t('msg.set_error_tips');
                }
                this.$message({
                    message: err.message,
                    type: 'error',
                    offset:100
                });
            })
        },
        checkLink(){
            let postData = {
                "content":this.GetRequest()
            }
            checkmail(postData).then(response => {
                if(response.status==10000){
                    
                } 
            }).catch(error => {
                let err = {};
                let errCode = error.errorCode[0];
                switch (errCode) {
                    case 600400128:
                        err.message = this.$t('msg.link_err1');
                        break;
                    case 600400129:
                        err.message = this.$t('msg.link_err2');
                        break;
                    default:
                        err.message = this.$t('msg.link_err1');
                }
                this.$message({
                    message: err.message,
                    type: 'error',
                    offset:100
                });
            })
        },
        GetRequest() {
            var url = window.location.href; 
            if (url.indexOf("?") != -1) {    
                var str = url.substr(1);
                var strs = str.split("=");  
                return strs[1].split("&")[0];    
            }
        },
    },
    beforeCreate(){
        this.$store.commit('showloadding',{show:false,text:this.$t('common.plase_wait')});
    },
    mounted(){
        this.checkLink();
    },
    beforeDestroy() {
    
    }
}
</script>
<style lang="scss" scoped>
@import '@/projects/home/styles/index.scss';
a:hover {text-decoration:underline}
.login_conrainer { width: 850px; min-height: 400px; height: auto; margin: 5% auto 0 auto; padding: 20px; border-radius: 5px; background: $white_bg;}
</style>