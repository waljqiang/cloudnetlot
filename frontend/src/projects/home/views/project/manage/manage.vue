<template>
    <el-row class="layout_box" :gutter="20">
        <el-col :span="7" >
            <div class="mp_box light_gray_border p10">
                <el-scrollbar style="height:100%" id="main_scrol">
                    <div style="line-height:30px">
                        <div class="tit_ico"></div>
                        <span>工作组列表</span> 
                    </div>
                    <el-tree
                        ref="tree"
                        :props="props"
                        :data='treeData'
                        @current-change="handCurrentChange"
                        :show-checkbox = "false"
                        empty-text=""
                        node-key="gid"
                        accordion
                        highlight-current
                        :expand-on-click-node="true">
                        <div class="custom-tree-node" slot-scope="{ node, data }"  @mouseover="showMenuBox(data)" @mouseleave="hideMenuBox()">
                            <span  v-text="data.name" :title="data.name" class="project_tree_name flt" ></span>
                            <!-- <span v-if="parseInt(data.routenums)>0" v-text="' ( '+data.routenums+' ) '"  style="display: inline-block;"></span> -->
                            <div class="flt" style="margin-left:10px">
                                <span v-if="data.pid==0" v-show="currentID==data.gid||showCurrentId==data.gid">
                                    <img v-if="is_master!='1'" class="project_img_ico" src="@/public/img/home/add_ico.png" v-on:click.stop="addProject(node,data)" :title="$t('common.add_project')" />
                                </span>
                                <span v-else v-show="currentID==data.gid||showCurrentId==data.gid">
                                    <img v-if="is_master!='1'" class="project_img_ico" src="@/public/img/home/add_ico.png" v-on:click.stop="addProject(node,data)" :title="$t('common.add_project')" />
                                    <!-- <img v-if="is_master!='1'" class="project_img_ico" src="@/public/img/home/add_ico.png" v-on:click.stop="editProject(node,data)" :title="$t('common.edit_project')" /> -->
                                    <img v-if="is_master!='1'" class="project_img_ico" src="@/public/img/home/del_ico.png"  v-on:click.stop="delProject(node,data)" :title="$t('common.del_project')" />
                                </span>
                            </div>
                        </div>
                    </el-tree>
                </el-scrollbar>
            </div>
        </el-col>
        <el-col :span="17">
            <div class="mp_box light_gray_border p10">
                <div style="line-height:30px">
                    <div class="tit_ico"></div>
                    <span>工作组列表</span> 
                </div>
                <el-scrollbar style="height:calc(100% - 30px)" id="main_scrol">
                    <el-row>
                        <el-col :offset="5" :span="12" >
                            <p>分组信息</p>
                            <el-form class="group_from" label-position="left" size="mini" label-width="30%" :model="currentFrom">
                                <el-form-item label="归属工作组">
                                    <span class="from_content" v-text="currentFrom.p_name"></span>
                                </el-form-item>
                                <el-form-item label="工作组名称">
                                    <span class="from_content" v-text="currentFrom.c_name"></span>
                                </el-form-item>
                                <el-form-item label="分组级别">
                                    <span class="from_content" v-text="currentFrom.level"></span>
                                </el-form-item>
                                <el-form-item label="分组组描述">
                                    <span class="from_content" v-text="currentFrom.p_mark"></span>
                                </el-form-item>
                            </el-form>
                            <p>配置信息</p>
                            <el-form class="group_from" label-position="left" label-width="30%" size="mini" :model="currentFrom">
                                <el-form-item label="模板名称">
                                    <span class="from_content" v-text="currentFrom.tp_name"></span>
                                </el-form-item>
                                <el-form-item label="状态">
                                    <span class="from_content" v-text="currentFrom.tp_status"></span>
                                </el-form-item>
                            </el-form>
                            <p style="margin-top: 15px;">其它信息</p>
                            <el-form class="group_from" label-position="left" label-width="30%" size="mini" :model="currentFrom">
                                <el-form-item label="创建时间">
                                    <span class="from_content" v-text="currentFrom.created_time"></span>
                                </el-form-item>
                            </el-form>
                            <p class="align_r">
                                <el-button size="mini" @click="editProject" type="primary">编辑</el-button>
                            </p>
                        </el-col>
                    </el-row>
                </el-scrollbar>
            </div>
        </el-col>

        <el-dialog
            title=""
            :visible.sync="dialogVisible"
            :close-on-click-modal="false"
            width="600px"
            :show-close="false">
            <div slot="title" class="frt" >
                <div class="custom_close align_c" @click="handleClose" >
                    <i class="el-icon-close" />
                </div>
            </div>
            <div>
                <el-form style="width:80%; margin:0 auto" class="group_from" label-position="left" label-width="30%" size="mini" :model="currentFrom">
                    <el-form-item label="归属工作组">
                        <span class="from_content" v-text="editFrom.p_name"></span>
                    </el-form-item>
                    <el-form-item label="工作组名称">
                        <el-input :disabled="editFrom.pid==0" maxlength="10" v-model="editFrom.c_name"></el-input>
                    </el-form-item>
                    <el-form-item label="分组组描述">
                        <el-input type="textarea" :disabled="editFrom.pid==0" show-word-limit rows="5" maxlength="100" v-model="editFrom.p_mark"></el-input>
                    </el-form-item>
                    <el-form-item label="配置模板">
                        <el-upload
                            class="upload-cur"
                            ref="upload"
                            :headers="headers"
                            :action="uploadUrl"
                            :on-change="handleChange"
                            :on-error="uploadError"
                            :on-success="uploadSuccess"
                            :file-list="fileList"
                            :show-file-list="false"
                            :auto-upload="false">
                            <el-input slot="trigger" readonly style=" width: 100%;" v-model="editFrom.tp_name">
                                <el-button slot="append" size="small" type="primary">选取文件</el-button>
                            </el-input>
                        </el-upload>
                        <el-link :href="downTplUrl" type="primary" style="line-height:16px;font-size:12px" >配置模板下载</el-link>
                    </el-form-item>
                    <el-form-item label="应用配置">
                       <el-checkbox :true-label='1' :false-label="0" v-model="editFrom.tp_status">自动应用配置</el-checkbox>
                    </el-form-item>
                    <el-form-item label="">
                       <el-button size="mini" class="frt" @click="subFun" type="primary">确认</el-button>
                    </el-form-item>
                </el-form>
            </div>           
        </el-dialog>
    </el-row>
</template>
<script>
import store from '@/projects/home/store'
import {getToken } from '@/utils/auth'
import { getTreeData,getNodeInfo,addTree,delNode,saveTree,download } from '@/projects/home/api/workgroup'
export default {
    data(){
        return {
            treeData:[{}],
            is_master:store.state.user.is_primary,
            props:{
                label: 'name',
                children: 'child'
            },
            headers:{},
            uploadUrl:sessionStorage.getItem('domain')+'/backend/workgroup/upload/config',
            downTplUrl:sessionStorage.getItem('domain')+'/backend/workgroup/download/config?token='+getToken(),
            currentFrom:{
                p_name:'-----',
                c_name:'-----',
                level:'-----',
                p_mark:'-----',
                g_id:'',
                tp_id:'-----',
                tp_name:'-----',
                tp_status:'-----',
                created_time:'',
                p_p_id:'',
                pid:'',
                auto:''
            },
            editFrom:{
                p_name:'-----',
                c_name:'-----',
                p_mark:'-----',
                tp_name:'-----',
                tp_status:'-----',
                pid:'',
                uid:'',
                tp_id:'',
            },
            currentID:0,
            showCurrentId:0,
            action:'view',
            nodeData:[],
            thisNode:{},
            addID:"",

            dialogVisible:false,
            fileList:[],
        }
    },
     methods: {
        handCurrentChange(data,node){
            this.nodeData = data;
            this.currentID = data.gid;
            let getData = {gid:data.gid}
            let parent_id = node.parent.data.pid;
            this.showInfo(getData,parent_id)
            this.action='view';
        },
        handleChange(file,fileList) {
            if(fileList.length>1){
                fileList.shift();
            }
            this.fileList = fileList;
            this.editFrom.tp_name = file.name;
        },
        submitUpload() {
            this.$refs.upload.submit();
        },
        uploadSuccess(response, file, fileList) {
            this.editFrom.tp_name = "";
            if(response.status==10000){
                this.editFrom.tp_id = response.data.fid;
                this.saveData();
            }  
        },
        uploadError(error, file, fileList) {
            fileList = [];
            this.fileList = [];
            this.$store.commit('showloadding',{show:false}); 
            let err = {};
            let errCode = error.errorCode[0];
            switch (errCode) {
                case 6004001141:
                    err.message = this.$t('msg.up_error');
                    break;
                case 600400140:
                    err.message = this.$t('msg.up_type_error');
                    break;
                case 600400139:
                    err.message = this.$t('msg.up_error');
                    break;
                default:
                    err.message = this.$t('msg.up_error');
            }
            this.$message({
                message: err.message,
                type: 'error',
                offset:100
            });
        },
        showInfo(getData,p_p_id){
            getNodeInfo(getData).then(response => {  
                if(response.status==10000){
                    let r = response.data;
                    let tplStatus = ['未应用配置','手动应用配置','自动应用配置']
                    this.currentFrom.g_id = r.gid;
                    this.currentFrom.p_name = r.pname;
                    this.currentFrom.c_name = r.name;
                    this.currentFrom.level = r.level;
                    this.currentFrom.p_mark = r.description;
                    this.currentFrom.tp_id = r.config_id;
                    
                    this.currentFrom.tp_status = tplStatus[r.auto];
                    this.currentFrom.auto = r.auto;
                    this.currentFrom.created_time = r.created_at;
                    this.currentFrom.pid = r.pid;
                    this.currentFrom.p_p_id = p_p_id;
                        
                        if(p_p_id==0){
                            this.currentFrom.p_name = this.$t("common.root_group_name");
                        }
                        if(r.pid==0){
                            this.currentFrom.c_name = this.$t("common.root_group_name");
                            this.currentFrom.p_name = '-----';
                            this.currentFrom.p_mark = '系统默认工作组,用户不能编辑和删除';
                        }
                        if(r.config_id==''){
                            this.currentFrom.tp_name = '-----'
                        }else{
                            this.currentFrom.tp_name = r.config_id;
                        }
                    r.p_p_id = p_p_id;
                    //this.showEditInfo(r);
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
        showEditInfo(r){
            this.editFrom.p_name = r.pname;
            this.editFrom.c_name = r.name;
            this.editFrom.p_mark = r.description;
            
            this.editFrom.tp_name = r.config_id;
            this.editFrom.tp_status = r.auto;
            this.editFrom.pid = r.pid;
            this.editFrom.uid = r.gid;
            this.editFrom.tp_id = r.config_id;
            if(r.pid==0){
                this.editFrom.c_name = this.$t("common.root_group_name");
                this.editFrom.p_name = '-----';
            }else{
                if(r.p_p_id==0){
                    this.editFrom.p_name = this.$t("common.root_group_name");
                }
            }
            
        },
        showMenuBox(data){
           this.showCurrentId = data.gid;
        },
        hideMenuBox(data){
            this.showCurrentId = this.currentID;
        },
        handleClose(){
            this.dialogVisible = false;
        },
        addProject(pnode,pdata){
            this.$refs.tree.setCurrentKey(pdata.gid);
            this.nodeData = pdata;
            this.thisNode = pnode;
            
            this.editFrom.p_name = pdata.name;
            this.editFrom.c_name = '';
            this.editFrom.p_mark = '';
            this.editFrom.tp_name = '';
            this.editFrom.tp_status = '0';
            this.editFrom.pid = pdata.gid;
            this.editFrom.uid = '';
            this.editFrom.tp_id = '';
            this.action='add';
            this.dialogVisible = true;     
        },
        editProject(){
            let r = {
                pname:this.currentFrom.p_name,
                name:this.currentFrom.c_name,
                description:this.currentFrom.p_mark,
                config_id:this.currentFrom.tp_id,
                auto:this.currentFrom.auto,
                pid:this.currentFrom.pid,
                gid:this.currentFrom.g_id,
                p_p_id:this.currentFrom.p_p_id,
            }
            this.showEditInfo(r);
            this.action='edit';
            this.dialogVisible = true;     
        },
        delProject(pnode,deleteData){
            let parent_id = pnode.parent.data.pid;
             this.$confirm(this.$t('msg.del_project_confirm_tips'), this.$t('msg.tips_title'), {
                    confirmButtonText: this.$t('msg.confirm_btn'),
                    cancelButtonText: this.$t('msg.cancel_btn'),
                    type: 'warning'
                }).then(() => {
                    this.$store.commit('showloadding',{show:true,text:this.$t('common.plase_wait')});
                    let delData ={gid:deleteData.gid};
                    delNode(delData).then(response => {
                        if(response.status==10000){
                            this.$message.success(this.$t('msg.del_success_tips'));
                            if(this.$refs.tree.getCurrentKey()==deleteData.gid){
                                this.$refs.tree.setCurrentKey(pnode.parent.data.gid);
                                let getData = {gid:pnode.parent.data.gid};
                                this.showInfo(getData,parent_id);
                                this.action='view';
                            }
                            this.$refs.tree.remove(pnode);
                            this.$store.commit('showloadding',{show:false});
                        }
                    }).catch((error) => {
                        // 600400157	工作组中不能有设备
                        // 600400156	工作组有子工作组
                        // 600400155	有子账号拥有此工作组
                        // 600400154	根工作组不能修改或删除
                        // 600400152	工作组ID必须
                        // 600400143	没有权限
                        // 600400142	没有此工作组
                        this.$store.commit('showloadding',{show:false}); 
                        let err = {};
                        let errCode = error.errorCode[0];
                        switch (errCode) {
                            case 600400155:
                                err.message = this.$t('msg.project_error_in_role')
                                break;
                            case 600400156:
                                err.message = this.$t('msg.project_error_in_child');
                                break;
                            case 600400157:
                                err.message = this.$t('msg.project_error_in_device');
                                break;
                            default:
                                err.message = this.$t('msg.del_error_tips');
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
        subFun(){
            this.$store.commit('showloadding',{show:true,text:this.$t('common.plase_wait')}); 
            if(this.fileList.length>0){
                this.submitUpload();
            }else{
                this.saveData();
            }
            
        },
        saveData(){
            let reg = /^[\a-\z\A-\Z0-9\u4E00-\u9FA5\-\_\@]{1,10}$/;
            if(!reg.test(this.editFrom.c_name)){
                this.$message.error(this.$t('msg.project_error_in_name'));
                return;
            }
            let postData = {
                name:this.editFrom.c_name,
                description:this.editFrom.p_mark,
                auto:this.editFrom.tp_status
            }
            if(this.action=='add'){
                postData.gid = this.editFrom.pid;
                if(this.editFrom.tp_id!=''){
                    postData.config_id=this.editFrom.tp_id;
                }
                addTree(postData).then(response => {  
                    if(response.status==10000){
                        this.$message({
                            message: this.$t('msg.add_success'),
                            type: 'success',
                            offset:100,
                            duration: 3 * 1000
                        });
                        let newChild = {
                            child: [],
                            gid: response.data.gid,
                            name: postData.name,
                            pid: postData.gid
                        }
                        this.appendC(newChild);
                        this.dialogVisible = false;    
                    }
                    this.$store.commit('showloadding',{show:false}); 
                }).catch((error) => {
                    this.$store.commit('showloadding',{show:false}); 
                    let err = {};
                    let errCode = error.errorCode[0];
                    switch (errCode) {
                        case 600400146:
                            err.message = this.$t('msg.role_name_tips');
                            return;
                        case 600400144:
                            err.message = this.$t('msg.project_error_in_level');
                            return;
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
            if(this.action=='edit'){
                postData.gid = this.editFrom.uid;
                if(this.editFrom.tp_id!=''){
                    postData.config_id=this.editFrom.tp_id;
                }
                saveTree(postData).then(response => {  
                    this.$store.commit('showloadding',{show:false});  
                    if(response.status==10000){
                        this.$message({
                            message: this.$t('msg.set_success_tips'),
                            type: 'success',
                            offset:100,
                            duration: 3 * 1000
                        });
                        this.nodeData.name = postData.name;
                        let enode = {
                            parent:{
                                data:{
                                    pid:this.currentFrom.p_p_id
                                }
                            }
                        }
                        this.handCurrentChange(this.nodeData,enode);
                        this.dialogVisible = false;
                    }
                }).catch((error) => {
                    this.$store.commit('showloadding',{show:false}); 
                    let err = {};
                    let errCode = error.errorCode[0];
                    switch (errCode) {
                        case 600400146:
                            err.message = this.$t('msg.role_name_tips');
                            return;
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
         appendC(newChild) {
            if (!this.nodeData.child) {
              this.$set(this.nodeData, 'child', []);
            }
            this.nodeData.child.push(newChild);
        },
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
                        this.showInfo({gid:curKey},Jdata[i].pid);
                    }
                }
                this.treeData = Jdata;
                setTimeout(() => {
                    this.$refs.tree.setCurrentKey(curKey);    
                }, 500);
                
                this.$store.commit('showloadding',{show:false});
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
        this.headers ={Authorization : 'Bearer '+getToken()}
    },

    mounted(){   
        this.$nextTick(function () { 
       
        })
    }
}
</script>
<style  lang="scss" scoped>
@import '@/projects/home/styles/index.scss';
.layout_box { height: calc(100vh - 140px); padding: 10px 0;}
.layout_box .el-col {height: 100%;}
.mp_box { border-width: 1px; border-style: solid; height: 100%;}

.project_img_ico { vertical-align: middle; margin-right:10px ;}
.project_tree_name {width: 75%; overflow: hidden;}
.custom-tree-node {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 14px;
    padding-right: 20px;
}
.group_from { padding-left: 20px;}
.from_content{ color:$gray_font ;}
p { margin-top: 50px;}
p:first-child { margin-top: 10px;}
#main_scrol .el-form-item--mini.el-form-item {margin-bottom: 0px;}

</style>
