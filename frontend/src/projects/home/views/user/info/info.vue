<template >
    <div style="width:600px;" class="jz"
  >
        <el-tabs v-model="activeName" :stretch="false" @tab-click="handleClick">
            <el-tab-pane label="" name="first">

            </el-tab-pane>
            <el-tab-pane :label="$t('common.bacsic_info_title')" name="second">
                <el-form :model="info" label-position="left" style="width:70%; margin:0 auto;" ref="infoForm" label-width="20%" >
                    <el-form-item :label="$t('common.account')" >
                        <span v-text="info.account"></span>
                    </el-form-item>
                    <el-form-item :label="$t('common.username')" >
                        <el-input v-model="info.name"></el-input>
                    </el-form-item>
                    <el-form-item :label="$t('common.phone')"  >
                        <el-row :gutter="0">
                            <el-col :span="10">
                                <el-select v-model="info.phone_code" class="flt">
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
                                        v-model="info.phone_num"
                                        clearable>
                                    </el-input>                                
                            </el-col>
                        </el-row>
                    </el-form-item>
                    <el-form-item :label="$t('common.email')" >
                        <el-input v-model="info.email"></el-input>
                    </el-form-item>
                    <el-form-item label="" class="align_r" >
                        <el-button @click="editInfo()" type="primary">{{$t('common.confirm_btn')}}</el-button>
                    </el-form-item>
                </el-form>
            </el-tab-pane>
            <el-tab-pane :label="$t('common.change_pwd')" name="third">
                <el-form :model="info" label-position="left" style="width:70%; margin:0 auto;" ref="pwdForm" label-width="20%" >
                    <el-form-item :label="$t('common.old_pwd')" >
                        <el-input show-password v-model="pwdForm.old_pwd"></el-input>
                    </el-form-item>
                    <el-form-item :label="$t('common.new_pwd')" >
                        <el-input show-password v-model="pwdForm.new_pwd"></el-input>
                    </el-form-item>
                    <el-form-item :label="$t('common.confirm_pwd')"  >
                        <el-input show-password v-model="pwdForm.pwd_confirm"></el-input>
                    </el-form-item>
                    <el-form-item label="" class="align_r" >
                        <el-button @click="editPwd()" type="primary">{{$t('common.confirm_btn')}}</el-button>
                    </el-form-item>
                </el-form>
            </el-tab-pane>
        </el-tabs>
    </div>    
</template>
<script>
import {getCountrycode} from '@/projects/home/api/system';
import {editUserInfo,editUserPwd} from '@/projects/home/api/user'
import store from '@/projects/home/store';
//引入check_lib自定义校验库
import checkObj from '@/public/js/check_lib.js';
export default {
    data() {
        return {
            activeName: 'second',
            info:{
                account:"", 
                name:"",
                phone_code:"",
                phone_num:"",
                email:"",
                address_detailed:"",
            },
            pwdForm:{
                old_pwd:"",
                new_pwd:"",
                pwd_confirm:"",
            },
            country_code:[],
        };
    },
    methods: {
        handleClick(tab, event) {
           
        },
        editInfo(){
            this.rules = [
                {val: this.info.name, rule: "check_nickname"},
                {val: this.info.phone_num, rule: "check_phone"},
                {val: this.info.email, rule: "check_email"}
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
            store.commit('showloadding',{show:true});
            let postData = {
                "nickname":this.info.name,
                "phonecode":this.info.phone_code,
                "phone":this.info.phone_num,
                "email":this.info.email,
                "address":this.info.address_detailed
            }
            editUserInfo(postData).then(response => {
                if(response.status==10000){
                    this.$message({
                        message: this.$t('msg.set_success_tips'),
                        type: 'success',
                        offset:100
                    });
                }
                store.commit('showloadding',{show:false});
            }).catch(error => {
                this.$store.commit('showloadding',{show:false});
                let err = {};
                let errCode = error.errorCode[0];
                switch (errCode) {
                    case 600400111:
                        err.message = this.$t('check.nickname_tips');
                        break;
                    case 600400119:
                        err.message = this.$t('check.email_set_error_tips');
                        break;
                    case 600400122:
                        err.message = this.$t('msg.phonecode_err');
                        break;
                    case 600400135:
                        err.message = this.$t('msg.phonecode_err');
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
        editPwd(){
            this.rules = [
                {val: this.pwdForm.old_pwd, rule: "check_user_pwd"},
                {val: this.pwdForm.new_pwd, rule: "check_user_pwd"},
                {val: this.pwdForm.pwd_confirm, rule: "check_user_pwd"}
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
            if(this.pwdForm.pwd_confirm!=this.pwdForm.new_pwd){
                this.$message({
                    message: this.$t('msg.pwd_set_error_tips4'),
                    type: 'error',
                    offset:100
                });
                return false;
            }
            store.commit('showloadding',{show:true});
            let postData = {
                "old_password":this.pwdForm.old_pwd,
                "new_password":this.pwdForm.new_pwd,
                "new_password_confirmation":this.pwdForm.pwd_confirm
            }
            editUserPwd(postData).then(response => {
                if(response.status==10000){
                    setTimeout(() => {
                        this.$router.push({ path: '/login' })  
                    }, 1000);
                    this.$message({
                        message: this.$t('msg.edit_pwd_success'),
                        type: 'success',
                        offset:100
                    });
                }
                store.commit('showloadding',{show:false});
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
                    case 600400118:
                        err.message = this.$t('msg.pwd_set_error_tips3');
                        break;
                    case 600400137:
                        err.message = this.$t('msg.pwd_set_error_tips5');
                        break;
                    case 600400136:
                        err.message = this.$t('msg.pwd_set_error_tips4');
                        break;
                    case 600400138:
                        err.message = this.$t('msg.old_pwd_error');
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
        }
    },
    beforeCreate(){
        this.$store.commit('showloadding',{show:true,text:this.$t('common.plase_wait')});
    },
    created(){
        getCountrycode({lang:this.lang}).then(response => {
            if(response.status==10000){
                this.country_code = response.data.list;
                store.commit('showloadding',{show:false});  
            } 
        }).catch(error => {
            this.$message({
                message: this.$t('msg.country_code_get_err'),
                type: 'error',
                offset:100
            });
        });
    },
    mounted(){
        let userInfos = store.state.user.infos.data;
        this.info.account = userInfos.username;
        this.info.name = userInfos.nickname;
        this.info.phone_code = userInfos.phonecode;
        this.info.phone_num = userInfos.phone;
        this.info.email = userInfos.email;  
           
    }
}
</script>
<style scoped>

</style>