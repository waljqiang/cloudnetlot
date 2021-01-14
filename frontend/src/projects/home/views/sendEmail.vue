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
                <div class="put_mb20" style="line-height:50px">
                    <div class="tit_ico"></div>
                   <span>{{$t("common.pwd_back")}}</span> 
                </div>
                <el-form v-show="sendok=='0'" :model="pwdForm" label-position="left" class="jz" style="width:55%;" ref="pwdForm" label-width="20%" >
                    <el-form-item :label="$t('common.account')" >
                        <el-input v-model="pwdForm.account"></el-input>
                    </el-form-item>
                                       
                    <el-form-item :label="$t('common.email')" >
                        <el-input v-model="pwdForm.email"></el-input>
                    </el-form-item>
                   
                    <el-form-item label="" class="align_r" >
                        <el-button @click="goLogin">{{$t('common.back_btn')}}</el-button>
                        <el-button @click="send" type="primary">{{$t('common.confirm_btn')}}</el-button>
                    </el-form-item>
                </el-form>
                <div v-show="sendok=='1'">
                    <el-row :gutter="20" class="mt20" >
                        <el-col :span="4" class="align_r">
                            <img src="@/public/img/common/send_ok.png" >
                        </el-col>
                        <el-col :span="20" class="align_l" style="line-height:28px;">
                            {{$t("msg.email_set_tips")}}
                        </el-col>
                    </el-row>
                    <el-row class="mt20">
                        <el-col :span="20"  class="align_c" style="line-height:28px;">
                           <el-button @click="goLogin">{{$t('common.back_btn')}}</el-button>
                        </el-col>
                    </el-row>
                </div>
                
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

import {sendEmail} from '@/projects/home/api/user'

export default {
    data(){
        return {
            lang:this.$i18n.locale,
            img_bg:img_bg,
            ligon_footer_bg:ligon_footer_bg,
            sendok:'0',
            pwdForm:{
                account:"",
                email:""
            },
            rules: []
        }
    },
    components:{
        'header-counter':Header
    },
    methods: {
        send(){
            this.rules = [
                {val: this.pwdForm.account, rule: "check_account"},
                {val: this.pwdForm.email, rule: "check_email"}
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

            this.$store.commit('showloadding',{show:true});
            let postData = {
                "username":this.pwdForm.account,
                "email":this.pwdForm.email,
                "lang":this.lang
            }
            sendEmail(postData).then(response => {
                this.$store.commit('showloadding',{show:false});
                if(response.status==10000){
                    this.$message({
                        message: this.$t('msg.link_suucess'),
                        type: 'success',
                        offset:100,
                        duration: 3 * 1000
                    });
                    this.sendok = '1';
                } 
            }).catch(error => {
                this.$store.commit('showloadding',{show:false});
                let err = {};
                let errCode = error.errorCode[0];
                switch (errCode) {
                    case 600400112:
                        err.message = this.$t('msg.account_empty');
                        break;
                    case 600400119:
                        err.message = this.$t('check.email_set_error_tips');
                        break;
                    case 600400127:
                        err.message = this.$t('check.email_set_error_tips');
                        break;
                    case 600400129:
                        err.message = this.$t('msg.account_error1');
                        break;
                    default:
                        err.message = this.$t('msg.link_error2');
                }
                this.$message({
                    message: err.message,
                    type: 'error',
                    offset:100
                });
            })
        },
        goLogin(){
            this.$router.push({ path: '/login' })  
        }
    },
    beforeCreate(){
        this.$store.commit('showloadding',{show:false,text:this.$t('common.plase_wait')});
    },
    mounted(){
    }
}
</script>
<style lang="scss" scoped>
@import '@/projects/home/styles/index.scss';
.login_conrainer { width: 850px; min-height: 400px; height: auto; margin: 5% auto 0 auto; padding: 20px; border-radius: 5px; background: $white_bg;}
</style>