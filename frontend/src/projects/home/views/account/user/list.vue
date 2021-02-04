<template>
    <div>
        <div class="" style="height:38px">
            <el-dropdown :hide-on-click="false" :show-timeout="0" class="frt" @command="haClick" >
                <span class="">
                     <img src="@/public/img/home/batch_ico.png" class="va" width="15" height="15" >
                     <i class="el-icon-arrow-down el-icon--right"></i>
                </span>
                <el-dropdown-menu slot="dropdown">
                    <el-dropdown-item  class="blue_bg white_font white_border dropdown_li" command="reset_pwd">{{$t('common.reset_pwd')}}</el-dropdown-item>
                    <el-dropdown-item  class="red_bg white_font white_border dropdown_li_danger" command="del_account">{{$t('common.del_account')}}</el-dropdown-item>
                </el-dropdown-menu>
            </el-dropdown>
            <div class="el-dropdown-link frt mr20" :title="$t('common.create_account_title')" @click="showAdd" style="height:28px;">
                    <img src="@/public/img/home/add_user.png" class="va" width="15" height="15" >
            </div>
            <div class=" frt mr20">
                <el-input placeholder="请输入内容" size="mini" class="blue_font" v-model="search_data" >
                    <el-button slot="append" @click="changeRole" >
                        <img src="@/public/img/home/search.png" class="va" width="15" height="15" >
                    </el-button>
                </el-input>
            </div>
            <div class="frt mr20">
                <el-select v-model="permiss" size="mini" @change="changeRole" >
                    <el-option label="全部角色" value=""></el-option>
                    <el-option label="Admin" value="2"></el-option>
                    <el-option label="Guest" value="1"></el-option>
                </el-select>
            </div>
            
            <div class="frt mr20 change_type">
                <div :class="account_status=='2'?'type_item light_gray_bg flt':'type_item flt'" @click="changeType('2')">
                    <span class="mr20">全部账号</span>
                    <span class="blue_font mr20">{{count_all}}</span>
                </div>
                <div :class="account_status=='1'?'type_item light_gray_bg flt':'type_item flt'"   @click="changeType('1')">
                    <span class="mr20">启用账号</span>
                    <span class="blue_font mr20">{{count_enable}}</span>
                </div>
                <div :class="account_status=='0'?'type_item light_gray_bg flt':'type_item flt'"   @click="changeType('0')">
                    <span class="mr20">停用账号</span>
                    <span class="red_font mr20">{{count_disable}}</span>
                </div>
            </div>
        </div>  
        <table-counter  
            v-bind:column="column"
            v-bind:thisdata="thisgedata"
            v-bind:pagesizes="false"
            v-bind:pagelayout="pagelayout"
            v-bind:pageoffset="pageoffset"
            v-bind:pageindex="pageindex" 
            v-bind:total="total"
            v-bind:ispage='ispage'
            v-bind:tableheight="tableH"
            v-bind:unchecked="false"
            v-on:listenPageIndex="setPageIndex"
            v-on:listenPageOffset="setPageOffset"
            v-on:listenCheckData="setCheckData"
            v-on:listenSort="setSort"
            >
        </table-counter> 

        <el-dialog
            title=""
            :close-on-click-modal="false"
            :visible.sync="dialogVisible"
            width="600px"
            :show-close="false">
            <div slot="title" class="frt" >
                <div class="custom_close align_c" @click="handleClose" >
                    <i class="el-icon-close" />
                </div>
            </div>
            <div>
                <el-form :model="accountFrom" style="width:80%; margin:0 auto" class="group_from" label-position="left" label-width="25%" size="mini" >
                    <el-form-item :label="$t('common.account')" >
                        <el-input :disabled="action!='add'" v-model="accountFrom.account"></el-input>
                    </el-form-item>
                    <el-form-item :label="$t('common.username')" >
                        <el-input v-model="accountFrom.name"></el-input>
                    </el-form-item>
                    <el-form-item v-show="action=='add'" :label="$t('common.pwd')" >
                        <el-input show-password v-model="accountFrom.pwd"></el-input>
                    </el-form-item>
                    <el-form-item :label="$t('common.phone')"  >
                        <el-row :gutter="0">
                            <el-col :span="10">
                                <el-select v-model="accountFrom.phone_code" class="flt">
                                    <el-option
                                    v-for="item in country_code"
                                    :key="item.phonecode"
                                    :label="item.phonecode"
                                    :value="item.phonecode">
                                        <div class="flt">{{ item.name }}</div>
                                        <div class="frt">{{ "   "+item.phonecode }}</div>
                                    </el-option>
                                </el-select>
                            </el-col>
                            <el-col :span="13" :offset="1" class="align_r">
                                    <el-input
                                        v-model="accountFrom.phone_num"
                                        clearable>
                                    </el-input>                                
                            </el-col>
                        </el-row>
                    </el-form-item>
                    <el-form-item :label="$t('common.email')" >
                        <el-input v-model="accountFrom.email"></el-input>
                    </el-form-item>
                    <el-form-item :label="$t('common.role_txt')">
                        <el-radio-group v-model="accountFrom.role">
                            <el-radio :label="'2'">Admin</el-radio>
                            <el-radio :label="'1'">Guest</el-radio>
                        </el-radio-group>
                        
                    </el-form-item>
                    <el-form-item :label="$t('common.state')">
                        <el-radio-group v-model="accountFrom.status">
                            <el-radio :label="'1'">{{$t('common.enable_txt')}}</el-radio>
                            <el-radio :label="'0'">{{$t('common.disable_txt')}}</el-radio>
                        </el-radio-group>
                        
                    </el-form-item>
                    <el-form-item :label="$t('common.bind_group')">
                        <el-scrollbar style="height:210px" class="light_gray_border" id="main_scrol">
                            <el-tree
                                v-if="dialogVisible"
                                ref="tree"
                                :props="props"
                                :data='treeData'
                                :show-checkbox = "true"
                                empty-text=""
                                node-key="gid"
                                :default-expanded-keys="accountFrom.group"
                                :default-checked-keys="accountFrom.group"
                                accordion>
                                <div class="custom-tree-node" slot-scope="{ node, data }" >
                                    <span  v-text="data.name" :title="data.name" class="project_tree_name flt" ></span>
                                
                                </div>
                            </el-tree>
                        </el-scrollbar>
                        
                    </el-form-item>
                    <el-form-item label="">
                       <el-button size="mini" class="frt" @click="subFun" type="primary">{{$t('common.confirm_btn')}}</el-button>
                    </el-form-item>
                </el-form>
            </div>           
        </el-dialog>
    </div>
</template>
<script>
import table from '@/components/Table/table'
import {globalPageOffset} from '@/public/js/common.js'
import {userList,userCount,userAdd,childInfo,userEdit,resetChildPwd,delChild} from '@/projects/home/api/user'
import {getCountrycode} from '@/projects/home/api/system'
import { getTreeData } from '@/projects/home/api/workgroup'
import enable from '@/public/img/home/enable_ico.png'
import disable from '@/public/img/home/disabled_ico.png'
import configBtn from '@/public/img/home/config_ico.png'
//引入check_lib自定义校验库
import checkObj from '@/public/js/check_lib.js';

export default {
    data(){
        return { 
            column:[
                {"name":"","width":"5%","type":"selection"," code":"","prop":"","sortable":false},
                {"name":this.$t('common.list_sn'), "width":"150","type":"id","align":"left","prop":"","sortable":false},
                {"name":this.$t('common.account'),"width":"11%","type":"","align":"center","prop":"username","sortable":"custom","sortData":"username"},
                {"name":this.$t('common.username'),"width":"11%","type":"","align":"center","prop":"nickname","sortable":"custom","sortData":"nickname"}, 
                {"name":this.$t('common.email'),"width":"13%","type":"","align":"center","prop":"email","sortable":"custom","sortData":"email"},    
                {"name":this.$t('common.role_txt'),"width":"10%","type":"","align":"center","prop":"role_txt","sortable":"custom","sortData":"level"},
                {"name":this.$t('common.state'),"width":"7%","type":"","align":"center","prop":"state_txt","sortable":"custom","sortData":"status"},
                {"name":this.$t('common.list_config'),"width":"7%","type":"","align":"center","prop":"config_btn","sortable":false}
            ], //表格类型
            thisgedata:[{}], //表格数据 
            pageoffset:globalPageOffset,   //表格当前行数
            pagelayout:" sizes, prev, pager, next, jumper",    
            pageindex:1, //当前页
            total:0,    //总数据数
            ispage:true,	//是否分页
            tableH:0,
            order_field:"",	//查询类型
            order_sort:"",	//升序&降序
            check_data:[],  //表格选中的数据
            search_data:"",
            account_status:'2',
            count_all:'--',
            count_enable:'--',
            count_disable:'--',
            permiss:'',

            dialogVisible:false,
            accountFrom:{
                account:'',
                name:'',
                pwd:'',
                phone_code:'',
                phone_num:'',
                email:'',
                role:'1',
                status:'1',
                group:[],
                uid:''
            },
            country_code:[],
            treeData:[{}],
            props:{
                label: 'name',
                children: 'child'
            },
            action:''
        }
    },
    components:{
        'table-counter':table
    },
    methods:{
        haClick(command){
            if(command=='reset_pwd'){
                this.batchReset();
            }else if(command=='del_account'){
                this.batchDel();
            }
        },
        setSort(thisSort){
           if(thisSort.order=="ascending"){
                this.order_sort = "asc";
            }else if(thisSort.order=="descending"){
                this.order_sort = "desc";
            }else{
                this.order_sort = "";
            }
            this.order_field=thisSort.prop;
            this.getData();
        },
        setCheckData(thisCheckData){
            this.check_data = thisCheckData;
        },
        setPageOffset(offset){
            this.pageoffset = offset;
            this.getData();
        },
        setPageIndex(index){
            this.pageindex = index;
            this.getData();
        },
        changeType(status){
            this.account_status = status;
            this.pageindex = 1;
            this.getData();
        },
        changeRole(){
            this.pageindex = 1;
            this.getData();
        },
        handleClose(){
            this.dialogVisible = false;
        },
        showAdd(){
            this.action = 'add';
            this.accountFrom.account = "";
            this.accountFrom.name = "";
            this.accountFrom.pwd = "";
            this.accountFrom.phone_code = this.country_code[0].phonecode;
            this.accountFrom.phone_num = "";
            this.accountFrom.email = "";
            this.accountFrom.role = "1";
            this.accountFrom.status = "1";
            this.accountFrom.group = [];
            this.dialogVisible = true;
            
        },
        editAcount(id){
            this.$store.commit('showloadding',{show:true}); 
            childInfo({uid:id}).then(response => {     
                if(response.status==10000){
                    let data = response.data
                    this.action = 'edit';
                    this.accountFrom.account = data.username;
                    this.accountFrom.name = data.nickname;
                    // this.accountFrom.pwd = this.thisgedata[index].username;
                    this.accountFrom.phone_code = data.phonecode;
                    this.accountFrom.phone_num = data.phone;
                    this.accountFrom.email = data.email;
                    this.accountFrom.role = data.level;
                    this.accountFrom.status = data.status;
                    this.accountFrom.group = data.gids;
                    this.accountFrom.uid = data.uid;
                    this.dialogVisible = true;
                }
                this.$store.commit('showloadding',{show:false}); 
            }).catch((error) => {
                this.$store.commit('showloadding',{show:false}); 
                this.$message({
                    message: this.$t('msg.set_error_tips2'),
                    type: 'error',
                    offset:100
                });
            })
        },
        handleTable(data,thistotal){
            var _this = this;
            for(var i=0;i<data.length;i++){
                data[i]['istruedata'] = true;
                if(data[i].status != 0){
                    data[i].state_txt = '<img style="vertical-align:middle" src="'+enable+'" width="15" >';
                }else{
                    data[i].state_txt = '<img style="vertical-align:middle" src="'+disable+'" width="15" >';
                }
                if(data[i].level == "2"){
                    data[i].role_txt = '<span>Admin</span>';
                }else{
                    data[i].role_txt = '<span>Guest</span>';
                }
                data[i].config_btn = '<div style="display:inline-block;cursor:pointer;" onclick="editAcount(\''+data[i].uid+'\')" title="'+this.$t('common.config_txt')+'" class="blue_font">\
                                        <img style="vertical-align:middle" src="'+configBtn+'" width="15" >\
                                    </div>';   
            }
            _this.total = parseInt(thistotal)
            _this.thisgedata = data;
        },
        getData(){
            this.$store.commit('showloadding',{show:true}); 
            let getData = {
                pageIndex:this.pageindex,
                pageOffset:this.pageoffset,
                keyword:this.search_data,
                status:this.account_status,
                role:this.permiss
            }
            if(this.order_sort!=""){
                getData.sortKey = this.order_field;
                getData.sort = this.order_sort;
            }
            userList(getData).then(response => {  
                 this.$store.commit('showloadding',{show:false});       
                if(response.status==10000){
                    let data = response.data;
                    this.handleTable(data.list,data.total);
                }
                
            }).catch((error) => {
                this.$store.commit('showloadding',{show:false}); 
                let err = {};
                let errCode = error.errorCode[0];
                switch (errCode) {
                    case 600400100:
                        err.message = this.$t('msg.pars_err1');
                        break;
                    case 600400193:
                        err.message = this.$t('msg.pars_err1');
                        break;
                    case 600400166:
                        err.message = this.$t('msg.pars_err1');
                        break;
                    case 600400165:
                        err.message = this.$t('msg.pars_err1');
                        break;
                    case 600400159:
                        err.message = this.$t('msg.pars_err1');
                        break;
                    case 600400151:
                        err.message = this.$t('msg.pars_err1');
                        break;
                    case 600400150:
                        err.message = this.$t('msg.pars_err1');
                        break;
                }
                this.$message({
                    message: err.message,
                    type: 'error',
                    offset:100
                });
            })
        },
        getCount(){
            userCount().then(response => {  
                if(response.status==10000){
                    let data = response.data;
                    this.count_all = data.all;
                    this.count_enable = data.enabled;
                    this.count_disable = data.disabled;
                    this.getData();
                }
            }).catch((error) => {
                this.$store.commit('showloadding',{show:false}); 
                let err = {message:""};
                let errCode = error.errorCode[0];
                this.$message({
                    message: err.message,
                    type: 'error',
                    offset:100
                });
            })
        },
        subFun(){
            this.rules = [
                {val: this.accountFrom.account, rule: "check_account"},
                {val: this.accountFrom.name, rule: "check_nickname"},
                {val: this.accountFrom.phone_num, rule: "check_phone"},
                {val: this.accountFrom.email, rule: "check_email"}
            ]
            if(this.action=='add'){
                this.rules.push({val: this.accountFrom.pwd, rule: "check_user_pwd"})
            }
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
            let treeArr = this.$refs.tree.getCheckedKeys();
            let partreeArr = this.$refs.tree.getHalfCheckedKeys();
            let groupArr = treeArr.concat(partreeArr);
            if(treeArr.length<=0){
                this.$message({
                    message: this.$t("msg.project_error_in_empty"),
                    type: 'error',
                    offset:100
                });
                return false;
            }
            this.$store.commit('showloadding',{show:true}); 
            let postData = {
                "nickname":this.accountFrom.name,
                "phonecode":this.accountFrom.phone_code,
                "phone":this.accountFrom.phone_num,
                "email":this.accountFrom.email,
                "role":this.accountFrom.role.toString(),
                "enable":this.accountFrom.status.toString(),
                "gids":groupArr
            }
            console.log(groupArr)
            if(this.action=='add'){
                postData.username = this.accountFrom.account;
                postData.password = this.accountFrom.pwd;
                userAdd(postData).then(response => {     
                    if(response.status==10000){
                        this.$message({
                            message: this.$t('msg.set_success_tips3'),
                            type: 'success',
                            offset:100,
                            duration: 3 * 1000
                        });
                        this.getCount();
                        this.dialogVisible = false;
                    }else{
                        
                    }
                    
                }).catch((error) => {
                    this.requestBack(error)
                })
            }else{
                postData.uid = this.accountFrom.uid;
                userEdit(postData).then(response => {     
                    if(response.status==10000){
                        this.$message({
                            message: this.$t('msg.set_success_tips3'),
                            type: 'success',
                            offset:100,
                            duration: 3 * 1000
                        });
                        this.getCount();
                        this.dialogVisible = false;
                    }else{
                        this.$store.commit('showloadding',{show:false}); 
                    }
                    
                }).catch((error) => {
                    this.requestBack(error)
                })            
            }  
        },
        requestBack(error){
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
                case 600400123:
                    err.message = this.$t('msg.phone_set_error_tips');
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
                    err.message = this.$t('msg.set_error_tips2');
            }
            this.$message({
                message: err.message,
                type: 'error',
                offset:100
            });
        },
        batchReset(){
            this.$confirm(this.$t('msg.reset_pwd_confirm_tips'), this.$t('msg.tips_title'), {
                confirmButtonText: this.$t('msg.confirm_btn'),
                cancelButtonText: this.$t('msg.cancel_btn'),
                type: 'warning'
            }).then(() => {
                let postData = {
                    uids:[],
                    "password":"123456"
                }
                for(var i=0;i<this.check_data.length;i++){
                    if(this.check_data[i].istruedata){
                        postData.uids.push(this.check_data[i].uid)
                    }
                }
                if(postData.uids.length<=0){
                    this.$message.error(this.$t('msg.select_config_account'));
                    return;
                }
                this.$store.commit('showloadding',{show:true}); 
                resetChildPwd(postData).then(response => {
                    if(response.status==10000){
                        this.$message({
                            message: this.$t('msg.reset_pwd_ok_tips')+postData.password,
                            type: 'success',
                            offset:100
                        });
                        this.$store.commit('showloadding',{show:false}); 
                    }
                }).catch((error) => {
                    this.$store.commit('showloadding',{show:false}); 
                    this.$message({
                        message:this.$t('msg.set_error_tips2'),
                        type: 'error',
                        offset:100
                    });
                })
            }).catch(() => {                
            });    
        },
        batchDel(){
            this.$confirm(this.$t('msg.del_device_confirm_tips'), this.$t('msg.tips_title'), {
                confirmButtonText: this.$t('msg.confirm_btn'),
                cancelButtonText: this.$t('msg.cancel_btn'),
                type: 'warning'
            }).then(() => {
                let postData = {
                    uids:[],
                }
                for(var i=0;i<this.check_data.length;i++){
                    if(this.check_data[i].istruedata){
                        postData.uids.push(this.check_data[i].uid)
                    }
                }
                if(postData.uids.length<=0){
                    this.$message.error(this.$t('msg.select_config_account'));
                    return;
                }
                this.$store.commit('showloadding',{show:true}); 
                delChild(postData).then(response => {
                    if(response.status==10000){
                        this.$message({
                            message: this.$t('msg.del_success_tips'),
                            type: 'success',
                            offset:100
                        });
                        this.getCount();
                        
                    }else{
                        this.$store.commit('showloadding',{show:false}); 
                    }
                }).catch((error) => {
                    this.$store.commit('showloadding',{show:false}); 
                    this.$message({
                        message:this.$t('msg.del_error_tips'),
                        type: 'error',
                        offset:100
                    });
                })
            }).catch(() => {                
            });    
        }
    },
    beforeCreate(){
        this.$store.commit('showloadding',{show:true,text:this.$t('common.plase_wait')});
    },
    created(){
        getCountrycode({lang:this.lang}).then(response => {
            if(response.status==10000){
                this.country_code = response.data.list;
                this.accountFrom.phone_code = response.data.list[0].phonecode;
            } 
        }).catch(error => {
            this.$message({
                message: this.$t('msg.country_code_get_err'),
                type: 'error',
                offset:100
            });
        })
        getTreeData({tree:1}).then(response => {        
            if(response.status==10000){
                let Jdata=response.data;
                let curKey;
                for(var i=0;i<Jdata.length;i++){
                    if(Jdata[i].pid == 0){
                        Jdata[i].name=this.$t("common.root_group_name");
                    }
                }
                this.treeData = Jdata;
            }
        }).catch((error) => {
            this.$store.commit('showloadding',{show:false}); 
            let err = {};
            let errCode = error.errorCode[0];
            switch (errCode) {
                default:
                    err.message = this.$t('msg.get_mode_error_tips');
            }
            this.$message({
                message: err.message,
                type: 'error',
                offset:100
            });
        })
    },
    beforeMount(){
       
    },
    mounted(){   
        this.$nextTick(function () {
            this.getCount();
            window.editAcount = this.editAcount;
        })  
    }
}

</script>
<style lang="scss" scoped>
@import '@/projects/home/styles/index.scss';
.el-dropdown-link,.el-dropdown {
    cursor: pointer;
    /* color: #409EFF; */
    padding: 5px;
    background-color: $light_gray_bg;
    border: 1px solid $light_gray_border2; 
    border-radius:4px;
}
.change_type {
    cursor: pointer;
    border: 1px solid $light_gray_border2;
    border-radius:4px;
    line-height: 28px;
}
.type_item { padding: 0 15px;}
.el-icon-arrow-down {
    font-size: 12px;
}
#main_scrol {border-width: 1px; border-style: solid;}
</style>


