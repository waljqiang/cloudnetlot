<template>
    <div>
        <div class="" style="height:38px">
            <!-- <div class="el-dropdown-link frt" style="">
                <i class="el-icon-delete del_btns" />
            </div> -->
            <el-button class="el-dropdown-link mr20 frt" @click="read(1)" >
                全部设为已读
            </el-button>
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
            :visible.sync="dialogVisible"
            width="600"
            :show-close="false"
            :before-close="handleClose">
            <div slot="title" class="frt" >
                <div class="custom_close align_c" @click="handleClose" >
                    <i class="el-icon-close" />
                </div>
            </div>
            <div style="">
                <div class="msg_title">
                    详情
                </div>  
            </div>
            <div v-if="infos!=''" class="msg_content">
                <el-row :gutter="20">
                    <el-col :offset="2" :span="4">用户</el-col>
                    <el-col :span="18">{{infos.username==''?'-----':infos.username}}</el-col>
                </el-row>
                <el-row :gutter="20">
                    <el-col :offset="2" :span="4">设备MAC</el-col>
                    <el-col :span="18">{{infos.dev_mac==''?'-----':infos.dev_mac}}</el-col>
                </el-row>
                <el-row :gutter="20">
                    <el-col :offset="2" :span="4">设备IP</el-col>
                    <el-col :span="18">{{infos.dev_ip==''?'-----':infos.dev_ip}}</el-col>
                </el-row>
                <el-row :gutter="20">
                    <el-col :offset="2" :span="4">设备型号</el-col>
                    <el-col :span="18">{{infos.dev_type==''?'-----':infos.dev_type}}</el-col>
                </el-row>
                <el-row :gutter="20">
                    <el-col :offset="2" :span="4">设备名称</el-col>
                    <el-col :span="18">{{infos.dev_name==''?'-----':infos.dev_name}}</el-col>
                </el-row>
                <el-row :gutter="20">
                    <el-col :offset="2" :span="4">版本号</el-col>
                    <el-col :span="18">{{infos.version==''?'-----':infos.version}}</el-col>
                </el-row>
                
            </div>
            <div class="msg_content">
                <el-row :gutter="20">
                    <el-col :offset="2" :span="4">操作行为</el-col>
                    <el-col :span="18">{{infos.opTypeTxt}}</el-col>
                </el-row>
                    <el-row :gutter="20">
                    <el-col :offset="2" :span="4">状态</el-col>
                    <el-col :span="18">
                        <span v-if="infos.status=='3'" class="red_font">
                            {{infos.opStatusTxt}}
                            <span class="blue_font ml20 cur">
                                [再次执行]
                            </span>
                        </span>
                        <span v-else>
                            {{infos.opStatusTxt}}  
                        </span>
                    </el-col>
                    
                </el-row>
                <el-row :gutter="20">
                    <el-col :offset="2" :span="4">操作时间</el-col>
                    <el-col :span="18">{{infos.created_at==''?'-----':infos.created_at}}</el-col>
                </el-row>
            </div>
            
        </el-dialog>
    </div>
</template>
<script>
import table from '@/components/Table/table'
import {globalPageOffset} from '@/public/js/common.js'
import {oplogStatics,oplogList,getinfo,setRead} from '@/projects/home/api/log'

import unread from '@/public/img/home/unread.png'
import readed from '@/public/img/home/readed.png'
import see from '@/public/img/home/see.png'
let typeArr = ['','修改设备系统信息','修改设备网络信息','修改设备无线信息','修改设备终端信息','重启设备','升级设备','绑定设备',''];
let statusArr = ['命令未发送','命令已发送','命令执行失败','命令执行成功'];
export default {
    data(){
        return { 
            column:[
                {"name":"","width":"5%","type":"selection"," code":"","prop":"","sortable":false},
                {"name":this.$t('common.list_sn'),"width":"150","type":"id","align":"left","prop":"","sortable":false},
                {"name":"内容","width":"50%","type":"","align":"left","prop":"content_text","sortable":false}, 
                {"name":"状态","width":"10%","type":"","align":"center","prop":"status_text","sortable":false},    
                {"name":"时间","width":"15%","type":"","align":"left","prop":"created_at","sortable":"custom","sortData":"created_at"},
                 {"name":"查看","width":"10%","type":"","align":"center","prop":"config_btn","sortable":false}
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

            dialogVisible:false,
            infos:'',
            
        }
    },
    components:{
        'table-counter':table
    },
    methods:{
        setSort(thisSort){console.log(thisSort)
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
        handleTable(data,thistotal){
            var _this = this;
            for(var i=0;i<data.length;i++){
                data[i]['istruedata'] = true;
                 //1:加密,2:系统,3:网络,4:无线,5:用户,6:重启,7:升级,8:绑定,9:设备信息解析失败
                let opType = data[i].type;    
                data[i]['content_text'] = typeArr[opType-1]+"["+data[i].dev_mac+"]";
                if(data[i].readed == "0"){//un
                    data[i].status_text = '<img style="vertical-align:middle" src="'+unread+'" width="15" >';
                }else{//read
                    data[i].status_text = '<img style="vertical-align:middle" src="'+readed+'" width="15" >';
                }
                data[i].config_btn = '<div style="display:inline-block;cursor:pointer;" onclick="showDialog(\''+data[i].id+'\',\''+data[i].readed +'\')" title="'+this.$t('common.config_txt')+'" class="blue_font"><img style="vertical-align:middle" src="'+see+'" width="15" ></div>';   
            }
            _this.total = parseInt(thistotal)
            _this.thisgedata = data;
        },
        getData(){
            let getData = {
                status:3,
                pageIndex:this.pageindex,
                pageOffset:this.pageoffset
            }
            if(this.order_sort!=""){
                getData.sortKey = this.order_field;
                getData.sort = this.order_sort;
            }
            oplogList(getData).then(response => {      
                if(response.status==10000){
                    let data = response.data;   
                    this.handleTable(data.list,data.total);
                }
                this.$store.commit('showloadding',{show:false}); 
            }).catch((error) => {
                this.$store.commit('showloadding',{show:false}); 
                let err = {};
                let errCode = error.errorCode[0];
                switch (errCode) {
                    case 600400100:
                        err.message = this.$t('msg.pars_err1');
                        break;
                    case 600400169:
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
        showDialog(id,readed){
            this.$store.commit('showloadding',{show:true}); 
            let getData = {
                id:id,
            }
            getinfo(getData).then(response => {      
                if(response.status==10000){
                    this.infos = response.data;
                    let opType = response.data.type;
                    let opStatus = response.data.status;
                    this.infos['opTypeTxt'] = typeArr[Number(opType)-1]+"["+response.data.dev_mac+"]";
                    this.infos['opStatusTxt'] = statusArr[Number(opStatus)-1];
                    //1:加密,2:系统,3:网络,4:无线,5:用户,6:重启,7:升级,8:绑定,9:设备信息解析失败
                    // 1：未发送，2：已发送，3：执行失败，4：执行成功                  
                    this.dialogVisible = true;
                    if(readed=='0'){
                        this.read(0,id);
                    }
                }
                
                this.$store.commit('showloadding',{show:false}); 
            }).catch((error) => {
               this.$store.commit('showloadding',{show:false}); 
               let err = {};
                let errCode = error.errorCode[0];
                switch (errCode) {
                    case 600400100:
                        err.message = this.$t('msg.pars_err1');
                        break;
                    case 600400192:
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
        read(type,id){ 
            let getData = {
                ids:[],
            }
            if(type==0){
                getData.ids = [id];
            }else{
                let batchIds = [];
                for(var i=0;i<this.check_data.length;i++){
                    if(this.check_data[i].istruedata){
                        batchIds.push(this.check_data[i].id)
                    }
                }
                if(batchIds.length<=0){
                    this.$message.error('请至少选择一条数据');
                    return;
                }
                getData.ids = batchIds;
                this.$store.commit('showloadding',{show:true}); 
            }
            setRead(getData).then(response => {      
                if(response.status==10000){
                    if(type!=0){
                        this.$message({
                            message: "操作成功",
                            type: 'success',
                            offset:100
                        });
                        this.getData();
                    }
                }
                this.$store.commit('showloadding',{show:false}); 
            }).catch((error) => {
               this.$store.commit('showloadding',{show:false}); 
               let err = {};
                let errCode = error.errorCode[0];
                switch (errCode) {
                    case 600400100:
                        err.message = this.$t('msg.pars_err1');
                        break;
                    case 600400192:
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
        handleClose(){
            this.dialogVisible = false;
        }
    },
    beforeCreate(){
        this.$store.commit('showloadding',{show:true,text:this.$t('common.plase_wait')});
    },
    beforeMount(){
    },
    mounted(){
        this.$nextTick(function () {
            this.getData();
            window.showDialog = this.showDialog;
        })  
    }
}

</script>
<style lang="scss" scoped>
@import '@/projects/home/styles/index.scss';
.el-dropdown-link {
    cursor: pointer;
    /* color: #409EFF; */
    padding: 5px;
    background-color: $light_gray_bg;
    border: 1px solid #e5edf0;
    border-radius:4px;
    height:28px;
}
.del_btns {
    color: $blue_font;
    font-size: 15px;
}

.msg_title { background-color: $light_gray_bg; padding: 5px 20px;}
.msg_content { padding: 30px 0 10px 0; line-height: 25px;}
</style>


