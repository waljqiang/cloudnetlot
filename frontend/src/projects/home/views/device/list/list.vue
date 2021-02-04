<template >
    <div class="">
        <div class="page-left light_gray_border show_left flt">
            <el-scrollbar style="height:100%" id="main_scrol">
                <div class="light_gray_bg" style="height:35px;line-height:35px;text-indent:12px;" >
                    <div class="tit_ico"></div>
                    <span>设备工作组</span> 
                </div>
                <el-tree
                    ref="tree"
                    :props="props"
                    :data='treeData'
                    @current-change="handCurrentChange"
                    :default-expanded-keys="def_expand"
                    :show-checkbox = "false"
                    empty-text=""
                    node-key="gid"
                    accordion
                    highlight-current
                    :expand-on-click-node="true">
                    <div class="custom-tree-node" slot-scope="{ node, data }" >
                        <span  v-text="data.name" :title="data.name" class="project_tree_name flt" ></span>
                    </div>
                </el-tree>
            </el-scrollbar>
            
        </div>
        <div class="page-center show_left_center frt">
            <div class="" style="height:38px">
                <el-dropdown :hide-on-click="false" @visible-change="dropChange" :show-timeout="0" class="frt" >
                    <span class="">
                        <img src="@/public/img/home/filter.png" class="va" width="15" height="15" >
                        <i class="el-icon-arrow-down el-icon--right"></i>
                    </span>
                    <el-dropdown-menu slot="dropdown">
                        <template v-for="(item,index) in filter_column" >
                            <el-dropdown-item :key="index" v-if="item.filter==true" class="blue_bg white_font white_border dropdown_li" >
                                <el-checkbox v-model="item.checked">{{item.name}}</el-checkbox>
                            </el-dropdown-item>
                        </template>
                        <el-dropdown-item class="align_c"  divided>
                              <el-button size="mini" type="primary" @click="setLocalObj" >应用</el-button>
                        </el-dropdown-item>
                        
                    </el-dropdown-menu>
                </el-dropdown>
                <el-dropdown v-if="is_master!='1'" :hide-on-click="false" :show-timeout="0" class="frt mr20" @command="haClick" >
                    <span class="">
                        <img src="@/public/img/home/batch_ico.png" class="va" width="15" height="15" >
                        <i class="el-icon-arrow-down el-icon--right"></i>
                    </span>
                    <el-dropdown-menu slot="dropdown">
                        <el-dropdown-item  class="blue_bg white_font white_border dropdown_li" command="apply">应用配置</el-dropdown-item>
                        <el-dropdown-item  class="red_bg white_font white_border dropdown_li_danger" command="wifi">无线设置</el-dropdown-item>
                        <el-dropdown-item  class="red_bg white_font white_border dropdown_li_danger" command="reboot">设备重启</el-dropdown-item>
                        <el-dropdown-item  class="red_bg white_font white_border dropdown_li_danger" command="alarm">告警设置</el-dropdown-item>
                        <el-dropdown-item  class="red_bg white_font white_border dropdown_li_danger" command="transfer">设备转组</el-dropdown-item>
                        <el-dropdown-item  class="red_bg white_font white_border dropdown_li_danger" command="export">信息导出</el-dropdown-item>
                    </el-dropdown-menu>
                </el-dropdown>

                <div class=" frt mr20">
                    <el-input placeholder="输入名称/MAC/型号" size="mini" class="blue_font" v-model="search_data" >
                        <el-button slot="append" @click="searchList" >
                            <img src="@/public/img/home/search.png" class="va" width="15" height="15" >
                        </el-button>
                    </el-input>
                </div>                
                <div class="frt mr20 change_type">
                    <div :class="account_status=='2'?'type_item light_gray_bg flt':'type_item flt'" @click="changeType('2')">
                        <span class="mr20">设备总数</span>
                        <span class="blue_font mr20">{{dev_all}}</span>
                    </div>
                    <div :class="account_status=='1'?'type_item light_gray_bg flt':'type_item flt'"   @click="changeType('1')">
                        <span class="mr20">在线设备</span>
                        <span class="blue_font mr20">{{dev_online}}</span>
                    </div>
                    <div :class="account_status=='0'?'type_item light_gray_bg flt':'type_item flt'"   @click="changeType('0')">
                        <span class="mr20">离线设备</span>
                        <span class="red_font mr20">{{dev_offline}}</span>
                    </div>
                </div>
            </div> 
            <table-counter 
                ref="devTable"
                :key="componentKey"
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
        <el-drawer
            :visible.sync="drawer"
            :show-close="false"
            size="570px">
            <div slot="title" class="" >
                <div class="custom_close align_c frt" @click="drawerClose" >
                    <i class="el-icon-close" />
                </div>
            </div>
            <dev-conf v-if="drawer" :types="confTypes" :devmac="confMac" @list="getDevList" @count="getDevCount" ></dev-conf>
        </el-drawer>
        <el-dialog
            title="提示"
            :visible.sync="dialogVisible"
            :close-on-click-modal="false"
            width="600px"
            :show-close="false">
            <div slot="title" class="frt" >
                <div class="custom_close align_c" style="margin-top:5px" @click="dialogClose" >
                    <i class="el-icon-close" />
                </div>
            </div>
            <div style="height:400px">
                <el-scrollbar style="height:100%" id="main_scrol">
                    <el-tree
                        :props="props"
                        :data='treeData'
                        @current-change="handleNodeClick"
                        :default-expanded-keys="def_expand"
                        :show-checkbox = "false"
                        empty-text=""
                        node-key="gid"
                        accordion
                        highlight-current
                        :expand-on-click-node="true">
                        <div class="custom-tree-node" slot-scope="{ node, data }" >
                            <span  v-text="data.name" :title="data.name" class="project_tree_name flt" ></span>
                        </div>
                    </el-tree>
                </el-scrollbar>
            </div>
        </el-dialog>
    </div>
</template>
<script>
import table from '@/components/Table/table'
import dev_config from '../components/index'
import {globalPageOffset} from '@/public/js/common.js'
import store from '@/projects/home/store'
import {getToken } from '@/utils/auth'
import { getTreeData } from '@/projects/home/api/workgroup'
import {devList,userActivity,devStatistics,setname,reboot,deletes,setDevItem} from '@/projects/home/api/device'
import enable from '@/public/img/home/enable_ico.png'
import disable from '@/public/img/home/offline_ico.png'
import configBtn from '@/public/img/home/config_ico.png'
import delBtn from '@/public/img/home/del_ico.png'
export default {
    data() {
        return {
            lang:this.$i18n.locale,
            is_master:store.state.user.is_primary,
            treeData:[{}],
            def_expand:[], 
            props:{
                label: 'name',
                children: 'child'
            },
            componentKey:0,
            column:[ ], //表格类型
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
            
            dev_all:'--',
            dev_online:'--',
            dev_offline:'--',

            filter_column:[
                {filter:false,checked:true,"name":"","width":"5%","type":"selection"," code":"","prop":"","sortable":false},
                {filter:false,checked:true,"name":this.$t('common.list_sn'), "width":"50","type":"id","align":"left","prop":"","sortable":false},
                {filter:true,checked:true,"name":this.$t('device.dev_mac'),"width":"16%","type":"","align":"center","prop":"dev_mac","sortable":false,},
                {filter:true,checked:true,"name":this.$t('device.dev_ip'),"width":"15%","type":"","align":"center","prop":"dev_ip","sortable":false}, 
                {filter:true,checked:true,"name":this.$t('device.dev_name'),"width":"11%","type":"","align":"center","prop":"name_txt","sortable":'custom',"sortData":"name"},    
                {filter:true,checked:true,"name":this.$t('device.dev_type'),"width":"10%","type":"","align":"center","prop":"type","sortable":'custom',"sortData":"type"},
                {filter:true,checked:true,"name":this.$t('device.dev_mode'),"width":"10%","type":"","align":"center","prop":"mode_txt","sortable":false},
                {filter:true,checked:true,"name":this.$t('device.dev_version'),"width":"13%","type":"","align":"center","prop":"ver_txt","sortable":false},
                {filter:true,checked:true,"name":this.$t('device.dev_access_time'),"width":"13%","type":"","align":"center","prop":"time_txt","sortable":'custom',"sortData":"join_time"},
                {filter:true,checked:true,"name":this.$t('device.dev_state'),"width":"6%","type":"","align":"center","prop":"state_txt","sortable":false},
                {filter:false,checked:true,"name":this.$t('common.list_config'),"width":"6%","type":"","align":"center","prop":"config_btn","sortable":false}
            ],

            drawer: false,
            confTypes:'conf',
            confMac:'',

            dialogVisible:false

        };
    },
    components:{
        'table-counter':table,
        'dev-conf':dev_config,
    },
    methods: {
        handCurrentChange(data,node){
            this.nodeData = data;
            this.currentID = data.gid;
            this.account_status = '2';
            this.search_data = '';
            this.getDevList();
            this.getDevCount();
        },
        haClick(command){
            if(command=='export'){
                this.downFile();
            }else{
                if(this.check_data.length<=0){
                    this.$message.error(this.$t('msg.change_set_dev'));
                    return;
                }
                let batchIds = [];
                for(var i=0;i<this.check_data.length;i++){
                    if(this.check_data[i].dev_mac!=undefined){
                        batchIds.push(this.check_data[i].dev_mac);
                    }
                }
                if(command=='apply'){
               
                }else if(command=='reboot'){
                    this.nowReboot(batchIds)
                }else if(command=='wifi'){
                    this.confTypes='batch_wifi';
                    this.confMac=batchIds;
                    this.drawer = true; 
                }else if(command=='transfer'){
                    this.dialogShow()
                }else if(command=='alarm'){
                    this.confTypes='batch_warn';
                    this.drawer = true; 
                }
            }
            
        },
        changeType(status){
            this.account_status = status;
            this.pageindex = 1;
            this.getDevList();
        },
        dropChange(state){
            if(!state){
                let obj=JSON.parse(localStorage.getItem("filter_storage"));
                this.filter_column = obj;
            }
        },
        setLocalObj(){
            let obj = JSON.stringify(this.filter_column); //转化为JSON字符串
            localStorage.setItem("filter_storage", obj);
            this.getLocalObj();
        },
        getLocalObj(){
           let obj=JSON.parse(localStorage.getItem("filter_storage"));
           this.filter_column = obj;
           this.setTableColumn();
        },
        setTableColumn(){
            let newColumn = [];
            for(let i=0;i<this.filter_column.length;i++){
                if(this.filter_column[i].checked){
                    newColumn.push(this.filter_column[i])
                }  
            }
            this.column = newColumn;
            this.componentKey += 1;
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
            this.getDevList();
        },
        setCheckData(thisCheckData){
            this.check_data = thisCheckData;
        },
        setPageOffset(offset){
            this.pageoffset = offset;
            this.getDevList();
        },
        setPageIndex(index){
            this.pageindex = index;
            this.getDevList();
        },
        searchList(){
            this.pageindex = 1;
            this.getDevList();
        },
        getDevList(){
            this.$store.commit('showloadding',{show:true}); 
            let getData = {
                'gid':this.$refs.tree.getCurrentKey(),
                'status':this.account_status=='2'?'':this.account_status,
                'pageIndex':this.pageindex,
                'pageOffset':this.pageoffset,
                "search":this.search_data
            }
            if(this.order_sort!=""){
                getData.sortKey = this.order_field;
                getData.sort = this.order_sort;
            }
            devList(getData).then(response => {
                this.$store.commit('showloadding',{show:false});       
                if(response.status==10000){
                    let data = response.data;
                    this.handleTable(data.list,data.total);
                }
            }).catch((error) => {
                this.$store.commit('showloadding',{show:false}); 
                this.$message({
                    message: '',
                    type: 'error',
                    offset:100
                });
            })
        },
        handleTable(data,thistotal){console.log(data)
            var _this = this;
            for(var i=0;i<data.length;i++){
                data[i]['istruedata'] = true;
                let modeTxt = ['网关','中继','WISP','WDS','AP']
                data[i].mode_txt = modeTxt[Number(data[i].mode)-1];
                
                data[i].ver_txt = '<div class="txt_el" title="'+data[i].version+'" >'+data[i].version+'</div>';
                data[i].time_txt = '<div class="txt_el" title="'+data[i].join_time+'">'+data[i].join_time+'</div>';
                if(data[i].status != 0){
                    if(this.is_master!='1'){
                        data[i].config_btn = '<div style="display:inline-block;cursor:pointer;" onclick="delDev(\''+data[i].dev_mac+'\')" title="'+this.$t('common.del_txt')+'" class="blue_font">\
                                        <img style="vertical-align:middle" src="'+delBtn+'" width="15" >\
                                    </div>';
                    }else{
                         data[i].name_txt = '<div class="txt_el " title="'+data[i].name+'" >'+data[i].name+'</div>';
                    }
                    data[i].name_txt = '<div class="txt_el blue_font cur" title="'+data[i].name+'" onclick="editName('+i+')" ><i class="el-icon-edit"></i>   '+data[i].name+'</div>';
                    data[i].state_txt = '<img style="vertical-align:middle" title="'+this.$t('device.dev_online')+'" src="'+enable+'" width="15" >';
                    data[i].config_btn = '<div style="display:inline-block;cursor:pointer;" onclick="showDrawer(\''+data[i].dev_mac+'\')" title="'+this.$t('common.config_txt')+'" class="blue_font">\
                                        <img style="vertical-align:middle" src="'+configBtn+'" width="15" >\
                                    </div>';
                }else{
                    data[i].name_txt = '<div class="txt_el " title="'+data[i].name+'" >'+data[i].name+'</div>';
                    data[i].state_txt = '<img style="vertical-align:middle" title="'+this.$t('device.dev_offline')+'" src="'+disable+'" width="15" >';
                    if(this.is_master!='1'){
                        data[i].config_btn = '<div style="display:inline-block;cursor:pointer;" onclick="delDev(\''+data[i].dev_mac+'\')" title="'+this.$t('common.del_txt')+'" class="blue_font">\
                                        <img style="vertical-align:middle" src="'+delBtn+'" width="15" >\
                                    </div>';
                    }else{
                         data[i].config_btn = '';
                    }
                    
                }
            }
            _this.total = parseInt(thistotal)
            _this.thisgedata = data;
        },
        getDevCount(){
            let getData = {
                gid:this.$refs.tree.getCurrentKey(),
            }
            devStatistics(getData).then(response => {      
                if(response.status==10000){
                    this.dev_all=response.data.all;
                    this.dev_online=response.data.online;
                    this.dev_offline=response.data.offline;
                }
            }).catch((error) => {
                this.$message({
                    message: '',
                    type: 'error',
                    offset:100
                });
            })  
        },
        downFile(){
            let downUrl = sessionStorage.getItem('domain')+'/backend/device/list/export?gid='+this.$refs.tree.getCurrentKey()+'&token='+getToken()+'&lang='+this.lang;
            window.location.href = downUrl;
        },
        applyCon(){
            
        },
        dialogShow(){
            this.dialogVisible = true;
        },
        dialogClose(){
            this.dialogVisible = false;
        },
        delDev(mac){            
            this.$confirm(this.$t('msg.confirm_select_dev_del'), this.$t('msg.tips_title'), {
                    confirmButtonText: this.$t('msg.confirm_btn'),
                    cancelButtonText: this.$t('msg.cancel_btn'),
                    type: 'warning'
                }).then(() => {
                    this.$store.commit('showloadding',{show:true}); 
                    let delData = {
                        'macs':mac
                    }
                    deletes(delData).then(response => {  
                        this.$store.commit('showloadding',{show:false});   
                        if(response.status==10000){
                            this.$message.success(this.$t('msg.set_success_tips'));
                            this.getDevList();
                            this.getDevCount();
                        } 
                    }).catch((error) => {  
                        this.$store.commit('showloadding',{show:false});         
                        this.$message.error(this.$t('msg.set_error_tips'));
                    })
                }).catch(() => {
                });    
        },
        nowReboot(batchIds){
            this.$confirm(this.$t('msg.dev_reboot_tips'), this.$t('msg.tips_title'), {
                confirmButtonText: this.$t('msg.confirm_btn'),
                cancelButtonText: this.$t('msg.cancel_btn'),
                type: 'warning'
            }).then(() => {
                this.$store.commit('showloadding',{show:true}); 
                let rebootData = {
                    'mac':batchIds
                }
                console.log(rebootData)
                reboot(rebootData).then(response => {
                    this.$store.commit('showloadding',{show:false}); 
                    if(response.status==10000){
                        this.$message.success(this.$t('msg.set_success_tips'));
                        this.getDevList();
                        this.getDevCount();
                    }  
                }).catch((error) => {
                    this.$store.commit('showloadding',{show:false}); 
                    this.$message({
                        message: error.message,
                        type: 'error',
                        offset:100
                    });
                })
            }).catch(() => {
                
            });
        },
        drawerClose(){
            this.drawer = false; 
        },
        showDrawer(mac){
            this.confTypes='conf';
            this.confMac=mac;
            this.drawer = true; 
        },
        editName(index){
            this.$prompt('', this.$t("msg.edit_name"), {
                confirmButtonText: this.$t("msg.confirm_btn"),
                cancelButtonText: this.$t("msg.cancel_btn"),
                inputPattern: /^[\a-\z\A-\Z0-9\u4E00-\u9FA5\-\_]{1,10}$/,
                inputValue:this.thisgedata[index].name,
                inputErrorMessage: this.$t("msg.dev_name_error")
            }).then(({ value }) => {
                let saveData = {
                    mac:this.thisgedata[index].dev_mac,
                    name:value
                }
               this.$store.commit('showloadding',{show:true}); 
                setname(saveData).then(response => {  
                    this.$store.commit('showloadding',{show:false});
                    if(response.status==10000){
                        this.thisgedata[index].name = value;
                        this.thisgedata[index].name_txt = '<div class="txt_el blue_font cur" title="'+value+'" onclick="editName('+index+')" ><i class="el-icon-edit"></i>   '+value+'</div>';
                    }
                }).catch((error) => {
                    this.$store.commit('showloadding',{show:false});
                    let err = {};
                    let errCode = error.errorCode[0];
                    switch (errCode) {
                        case 600400233:
                            err.message = this.$t('msg.dev_name_error');
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
            }).catch(() => {
                  
            });
        },
        handleNodeClick(data,node,obj){ //点击node
            this.$confirm(this.$t('msg.group_dev_top_tips'), this.$t('msg.tips_title'), {
                confirmButtonText: this.$t('msg.confirm_btn'),
                cancelButtonText: this.$t('msg.cancel_btn'),
                type: 'warning'
            }).then(() => {
                this.$store.commit('showloadding',{show:true}); 
                let batchIds = [];
                for(var i=0;i<this.check_data.length;i++){
                    if(this.check_data[i].dev_mac!=undefined){
                        batchIds.push(this.check_data[i].dev_mac);
                    }
                    
                }
                let subData = {
                    macs:batchIds,
                    gid:node.key
                }
                console.log(subData)
                setDevItem(subData).then(response => {  
                    this.$store.commit('showloadding',{show:false}); 
                    if(response.status==10000){
                        // this.handtreeID = node.key;
                        this.$message.success(this.$t('msg.set_success_tips'));
                        if(batchIds.length==this.thisgedata.length&&this.pageindex!=1){
                            this.pageindex = 1;
                        }
                        this.getDevList();
                        this.getDevCount();
                    }
                    this.dialogClose(); 
                }).catch((error) => {
                    this.$store.commit('showloadding',{show:false}); 
                    this.$message.error(this.$t('msg.set_error_tips'));
                })  
            }).catch((error) => {

            })   
        }
    },
    beforeCreate(){
        
    },
    created(){  //初始化根节点  beforeMount
       this.$store.commit('showloadding',{show:true,text:this.$t('common.plase_wait')});
        getTreeData({tree:1}).then(response => {        
            if(response.status==10000){
                let Jdata=response.data;
                let curKey;
                for(var i=0;i<Jdata.length;i++){
                    if(Jdata[i].pid == 0){
                        Jdata[i].name=this.$t("common.root_group_name");
                        curKey = Jdata[i].gid;
                        this.def_expand.push(curKey); 
                    }
                }
                this.treeData = Jdata;
                setTimeout(() => {
                    this.$refs.tree.setCurrentKey(curKey);  
                    this.getDevList();
                    this.getDevCount();
                }, 500);
               // this.$store.commit('showloadding',{show:false});
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
        if(localStorage.getItem("filter_storage")==null){
            let obj = JSON.stringify(this.filter_column); //转化为JSON字符串
            localStorage.setItem("filter_storage", obj);
            this.getLocalObj();
        }else{
            this.getLocalObj();
        }    
    },
    mounted(){
        window.showDrawer = this.showDrawer;
        window.delDev = this.delDev;
        window.editName = this.editName;
    }
}
</script>
<style lang="scss" scoped>
@import '@/projects/home/styles/index.scss';
.page-left {
    overflow: hidden;
    -webkit-transition: all .3s ease-in;
    -moz-transition: all .3s ease-in;
    transition: all .3s ease-in;
    height: calc(100vh - 226px);
    border-width: 1px;
    border-style: solid;
    margin-top: 38px;
}
.show_left{
    width: 260px;
}
.hide_left {
    width: 10px;
}
.page-center {
    overflow: hidden;
    -webkit-transition: all .3s ease-in;
    -moz-transition: all .3s ease-in;
    transition: all .3s ease-in;
}
.show_left_center{
    width: calc(100% - 280px);
}


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
.custom_close { margin-top: 0px;}
</style>