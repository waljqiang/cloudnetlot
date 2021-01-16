<template>
    <div>
        <div class="" style="height:38px">
            <el-dropdown :hide-on-click="false" class="frt" @command="haClick" >
                <span class="">
                     <img src="@/public/img/home/batch_ico.png" class="va" width="15" height="15" >
                     <i class="el-icon-arrow-down el-icon--right"></i>
                </span>
                <el-dropdown-menu slot="dropdown">
                    <el-dropdown-item  class="blue_bg white_font white_border dropdown_li" command="reset_pwd">{{$t('common.reset_pwd')}}</el-dropdown-item>
                    <el-dropdown-item  class="red_bg white_font white_border dropdown_li_danger" command="del_account">{{$t('common.del_account')}}</el-dropdown-item>
                </el-dropdown-menu>
            </el-dropdown>
            <div class="el-dropdown-link frt mr20" style="height:28px;">
                    <img src="@/public/img/home/add_user.png" class="va" width="15" height="15" >
            </div>
            <div class=" frt mr20">
                <el-input placeholder="请输入内容" size="mini" class="blue_font" v-model="search_data" >
                    <el-button slot="append" >
                        <img src="@/public/img/home/search.png" class="va" width="15" height="15" >
                    </el-button>
                </el-input>
            </div>
            <div class="frt mr20">
                <el-select v-model="permiss" size="mini" @change="getData" >
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
                    <span class="mr20">禁用账号</span>
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
    </div>
</template>
<script>
import table from '@/components/Table/table'

import {globalPageOffset} from '@/public/js/common.js'
import {userList,userCount} from '@/projects/home/api/user'
import enable from '@/public/img/home/disabled_ico.png'
import disable from '@/public/img/home/enable_ico.png'
import configBtn from '@/public/img/home/config_ico.png'

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
        }
    },
    components:{
        'table-counter':table
    },
    methods:{
        haClick(command){
             console.log(command)
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
            this.getData();
        },
        editAcount(){

        },
        handleTable(data,thistotal){
            var _this = this;
            for(var i=0;i<data.length;i++){
                data[i]['istruedata'] = true;
                if(data[i].status != "0"){
                    data[i].state_txt = '<img style="vertical-align:middle" src="'+enable+'" width="15" >';
                }else{
                    data[i].state_txt = '<img style="vertical-align:middle" src="'+disable+'" width="15" >';
                }
                if(data[i].level == "2"){
                    data[i].role_txt = '<span>Admin</span>';
                }else{
                    data[i].role_txt = '<span>Guest</span>';
                }
                data[i].config_btn = '<div style="display:inline-block;cursor:pointer;" onclick="editAcount(\''+data[i].id+'\')" title="'+this.$t('common.config_txt')+'" class="blue_font">\
                                        <img style="vertical-align:middle" src="'+configBtn+'" width="13" >\
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
        }
    },
    beforeCreate(){
        this.$store.commit('showloadding',{show:true,text:this.$t('common.plase_wait')});
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
<style scoped>
.el-dropdown-link,.el-dropdown {
    cursor: pointer;
    /* color: #409EFF; */
    padding: 5px;
    background-color: #f4f5f6;
    border: 1px solid #e5edf0;
    border-radius:4px;
}
.change_type {
    cursor: pointer;
    border: 1px solid #e5edf0;
    border-radius:4px;
    line-height: 28px;
}
.type_item { padding: 0 15px;}
.el-icon-arrow-down {
    font-size: 12px;
}
</style>


