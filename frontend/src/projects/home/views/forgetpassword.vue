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
                   <span>{{$t('common.registered')}}</span> 
                </div>
                <el-form :model="registerForm" label-position="right" style="width:55%; margin:0 auto;" ref="registerForm" label-width="30%" >
                    <el-form-item :label="$t('common.account')" required>
                        <el-input v-model="registerForm.account"></el-input>
                    </el-form-item>
                    <el-form-item :label="$t('common.username')" >
                        <el-input v-model="registerForm.name"></el-input>
                    </el-form-item>
                    <el-form-item :label="$t('common.pwd')" required>
                        <el-input show-password v-model="registerForm.pwd"></el-input>
                    </el-form-item>
                    <el-form-item :label="$t('common.pwd_confirm')" required>
                        <el-input show-password v-model="registerForm.pwd_confirm"></el-input>
                    </el-form-item>
                    <el-form-item :label="$t('common.phone')" required >
                        <el-row :gutter="0">
                            <el-col :span="10">
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
                            <el-col :span="13" :offset="1" class="align_r">
                                    <el-input
                                        v-model="registerForm.phone_num"
                                        clearable>
                                    </el-input>                                
                            </el-col>
                        </el-row>
                    </el-form-item>
                    <el-form-item :label="$t('common.email')" required>
                        <el-input v-model="registerForm.email"></el-input>
                    </el-form-item>
                    <el-form-item :label="$t('common.address')" required>
                        <el-input maxlength="100" v-model="registerForm.address_detailed"></el-input>
                    </el-form-item>
                    <el-form-item label="" >
                        <el-checkbox v-model="registerForm.register_protocol"></el-checkbox>
                        <a @click="show_agree" >{{$t('common.agreement_tips')}}</a>
                    </el-form-item>
                    <el-form-item label="" class="align_r" >
                        <el-button @click="goLogin">{{$t('common.back_btn')}}</el-button>
                        <el-button @click="subRegister('registerForm')" type="primary">{{$t('common.registered')}}</el-button>
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

import {getCountrycode} from '@/projects/home/api/system'
import {register} from '@/projects/home/api/user'

export default {
    data(){
        return {
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
            rules: []
        }
    },
    components:{
        'header-counter':Header
    },
    methods: {
        subRegister(formName){
            this.rules = [
                {val: this.registerForm.account, rule: "check_account"},
                {val: this.registerForm.name, rule: "check_nickname"},
                {val: this.registerForm.pwd, rule: "check_user_pwd"},
                {val: this.registerForm.pwd_confirm, rule: "check_user_pwd"},
                {val: this.registerForm.phone_num, rule: "check_phone"},
                {val: this.registerForm.email, rule: "check_email"},
                {val: this.registerForm.address_detailed, rule: "check_adress"}
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
            if(this.registerForm.pwd_confirm!=this.registerForm.pwd){
                this.$message({
                    message: this.$t('msg.pwd_set_error_tips4'),
                    type: 'error',
                    offset:100
                });
                return false;
            }
            if(!this.registerForm.register_protocol){
                this.$message({
                    message: this.$t('msg.read_agreement_tips'),
                    type: 'warn',
                    offset:100
                });
                return false;
            }
            this.$store.commit('showloadding',{show:true});
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
                        message: this.$t('msg.register_success'),
                        type: 'success',
                        offset:100,
                        duration: 3 * 1000
                    });
                    setTimeout(() => {
                        this.$router.push({ path: '/login' })  
                    }, 3000);
                } 
            }).catch(error => {
                this.$store.commit('showloadding',{show:false});
                let err = {};
                let errCode = error.errorCode[0];
                switch (errCode) {
                    case 600400110:
                        err.message = this.$t('msg.account_repeat');
                        break;
                    case 600400111:
                        err.message = this.$t('check.nickname_tips');
                        break;
                    case 600400112:
                        err.message = this.$t('msg.account_empty');
                        break;
                    case 600400113:
                        err.message = this.$t('check.account_tips');
                        break;
                    case 600400114:
                        err.message = this.$t('check.account_tips');
                        break;
                    case 600400115:
                        err.message = this.$t('msg.pwd_set_error_tips2');
                        break;
                    case 600400116:
                        err.message = this.$t('check.pwd_set_error_tips');
                        break;
                    case 600400117:
                        err.message = this.$t('check.pwd_set_error_tips');
                        break;
                    case 600400118:
                        err.message = this.$t('msg.pwd_set_error_tips3');
                        break;
                    case 600400119:
                        err.message = this.$t('check.email_set_error_tips');
                        break;
                    case 600400120:
                        err.message = this.$t('msg.address_maxlength100');
                        break;
                    case 600400133:
                        err.message = this.$t('msg.phonecode_err');
                        break;
                    case 600400134:
                        err.message = this.$t('check.phone_set_error_tips');
                        break;
                    case 600400135:
                        err.message = this.$t('msg.phonecode_err');
                        break;
                    case 600400136:
                        err.message = this.$t('msg.pwd_set_error_tips4');
                        break;
                    default:
                        err.message = this.$t('msg.register_error');
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
        },
        show_agree(){
            let routeUrl = this.$router.resolve({
                path: "/agreement"
            });
            window.open(routeUrl.href, '_blank');     
        }
    },
    beforeCreate() {
		store.commit('showloadding',{text:this.$t('common.plase_wait')}); 
	},
    created(){
        getCountrycode({lang:this.lang}).then(response => {
            if(response.status==10000){
                this.country_code = response.data.list;
            } 
        }).catch(error => {
            this.$message({
                message: this.$t('msg.country_code_get_err'),
                type: 'error',
                offset:100
            });
        })
    },
    mounted(){
         this.$store.commit('showloadding',{show:false});
    },
    beforeDestroy() {
        this.$message.closeAll();
    }
}
</script>
<style lang="scss" scoped>
@import '@/projects/home/styles/index.scss';
a:hover {text-decoration:underline}
.login_conrainer { width: 850px; min-height: 400px; height: auto; margin: 5% auto 0 auto; padding: 20px; border-radius: 5px; background: $white_bg;}
</style>