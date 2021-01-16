<template>
    <el-row :gutter="20">
        <el-col :span="7" >
            <div class="mp_box light_gray_border p10">
                <div style="line-height:50px">
                    <div class="tit_ico"></div>
                   <span>{{$t('common.registered')}}</span> 
                </div>
                <el-tree
                    ref="tree"
                    :props="props"
                    :data='treeData'
                    @node-expand="handleNodeExpand"
                    @current-change="handCurrentChange"
                    :show-checkbox = "false"
                    :default-expanded-keys="['1']"
                    empty-text=""
                    node-key="id"
                    accordion
                    highlight-current
                    :expand-on-click-node="true">
                    <div class="custom-tree-node" slot-scope="{ node, data }"  @mouseover="showMenuBox(data)" @mouseleave="hideMenuBox()">
                        <i class="iconfont flt"></i>  
                        <span  v-text="data.name" :title="data.name" class="project_tree_name flt" ></span>
                        <!-- <span v-if="parseInt(data.routenums)>0" v-text="' ( '+data.routenums+' ) '"  style="display: inline-block;"></span> -->
                        <div class="flt" style="margin-left:10px">
                            <!-- <span v-if="data.id==1" v-show="currentID==data.id||showCurrentId==data.id">
                                <img v-if="is_master=='1'" class="project_img_ico" src="@/../static/images/common/project_add.png" v-on:click.stop="addProject(node,data)" :title="$t('common.add_project')" />
                            </span> -->
                            <!-- <span v-else v-show="currentID==data.id||showCurrentId==data.id">
                                <img v-if="is_master=='1'" class="project_img_ico" src="@/../static/images/common/project_add.png" v-on:click.stop="addProject(node,data)" :title="$t('common.add_project')" />
                                <img v-if="is_master=='1'" class="project_img_ico" src="@/../static/images/common/project_edit.png" v-on:click.stop="editProject(node,data)" :title="$t('common.edit_project')" />
                                <img v-if="is_master=='1'" class="project_img_ico" src="@/../static/images/common/project_del.png"  v-on:click.stop="delProject(node,data)" :title="$t('common.del_project')" />
                            </span> -->
                        </div>
                    </div>
                    
                </el-tree>
            </div>
        </el-col>
        <el-col :span="17">
            <div class="mp_box light_gray_border"></div>
        </el-col>
    </el-row>
</template>
<script>
import { getTreeData,addTree,getNodeInfo,delNode,saveTree } from '@/projects/home/api/workgroup'
export default {
    data(){
        return {
            treeData:[{}],
            //is_master:store.getters.roles.is_master,
            props:{
                label: 'name',
                children: 'child'
            },
            form:{
                p_name:'',
                p_parent:'',
                p_address:'',
                p_person:'',
                p_mark:'',
                p_addtime:''
            },
            currentID:0,
            showCurrentId:0,
            action:'view',
            nodeData:[],
            thisNode:{},
            addID:""
        }
    },
     methods: {
        handleNodeExpand(data,node,obj){
            let getData = {
                nodeid:data.id,
                pageIndex:1,
                pageOffset:999
            }
            if(data.children&&data.children.length>0){
                data.children.length=0;
                node.childNodes.length=0;
            }
            
            getTreeData(getData).then(response => {        
                if(response.code==10000){
                    let Jdata=response.data.list;
                    for(var i=0;i<Jdata.length;i++){
                        if(parseInt(Jdata[i].childnums)>0){ 
                            Jdata[i].children=[{}]
                        }
                        this.$refs.tree.append(Jdata[i],node)
                    }
                    if(node!=null&&node!=undefined){
                        node.expanded = true
                    }
                    if(obj==1){
                        this.$refs.tree.setCurrentKey(this.addID);
                        this.handCurrentChange({id:this.addID});
                    }
                }else{
                    this.$message.error(this.$t("msg.get_mode_error_tips")); 
                }
            }).catch((error) => {
                this.$message.error(this.$t("msg.get_mode_error_tips"));  
            })
        },
        handCurrentChange(data,node){
            this.currentID = data.id;
            let getData = {nodeid:data.id}
          //  this.showInfo(getData)
            this.action='view';
        },
        showInfo(getData){
            getNodeInfo(getData).then(response => {  
                if(response.code==10000){
                    let r = response.data;
                    if(getData.nodeid=='1'){
                        this.form.p_name = this.lang=='zh'?r.name:r.name_en;
                        this.form.p_parent = this.lang=='zh'?r.parent_name:r.parent_name_en;
                    }else{
                        this.form.p_name = r.name;
                        if(r.pid=='1'){
                            this.form.p_parent = this.lang=='zh'?r.parent_name:r.parent_name_en;
                        }else{
                            this.form.p_parent = r.parent_name;
                        }
                    }
                    this.form.p_address = r.address;
                    this.form.p_person = r.leader;
                    this.form.p_addtime = r.add_time;
                    this.form.p_mark = r.note;
                    
                }else{
                    this.$message.error(this.$t("msg.get_loading_error")); 
                }
            }).catch((error) => {
                this.$message.error(this.$t("msg.get_loading_error"));  
            })
        },
        showMenuBox(data){
           this.showCurrentId = data.id;
        },
        hideMenuBox(data){
            this.showCurrentId = this.currentID;
        },
        addProject(pnode,pdata){
            this.$refs.tree.setCurrentKey(pdata.id);
            this.nodeData = pdata;
            this.thisNode = pnode;
            this.action = 'add'; 
            this.form.p_name = '';
            this.form.p_parent = pdata.name;
            this.form.p_address = '';
            this.form.p_person = '';
            this.form.p_addtime = '';
            this.form.p_mark = '';   
        },
        editProject(pnode,editData){
            this.$refs.tree.setCurrentKey(editData.id);
            this.nodeData = editData;
            this.action = 'edit';
            let getData = {nodeid:editData.id}
            this.showInfo(getData)
        },
        delProject(pnode,deleteData){
             this.$confirm(this.$t('msg.del_project_confirm_tips'), this.$t('msg.tips_title'), {
                    confirmButtonText: this.$t('msg.confirm_btn'),
                    cancelButtonText: this.$t('msg.cancel_btn'),
                    type: 'warning'
                }).then(() => {
                    let loading = this.$loading({
                        lock: true,
                        text: this.$t('msg.post_loading'),
                        spinner: 'el-icon-loading',
                        background: 'rgba(0, 0, 0, 0.7)'
                    }); 
                    let delData ={nodeid:deleteData.id};
                    delNode(delData).then(response => { 
                        loading.close();   
                        if(response.code==10000){
                            this.$message.success(this.$t('msg.del_success_tips'));
                            if(this.$refs.tree.getCurrentKey()==deleteData.id){
                                this.$refs.tree.setCurrentKey(pnode.parent.data.id);
                                let getData = {nodeid:pnode.parent.data.id};
                                this.showInfo(getData);
                                this.action='view';
                            }
                            this.$refs.tree.remove(pnode);
                        }else{
                            if(response.code==600600606){
                               this.$message.error(this.$t('msg.project_error_in_device'));  
                            }else if(response.code==600600605){
                                this.$message.error(this.$t('msg.project_error_in_child'));  
                            }else if(response.code==600600609){
                                this.$message.error(this.$t('msg.project_error_in_role'));  
                            }else{
                                this.$message.error(this.$t('msg.del_error_tips')); 
                            }    
                        }
                    }).catch((error) => {
                        loading.close();
                        this.$message.error(this.$t('msg.del_error_tips'));  
                    })
                }).catch(() => {
             
                });
        },
        saveData(){
            let reg = /^[\a-\z\A-\Z0-9\u4E00-\u9FA5\-\_]{1,30}$/;
            if(!reg.test(this.form.p_name)){
                this.$message.error(this.$t('msg.project_error_in_name'));
                return;
            }
            let addData = {
                nodeid: this.nodeData.id,
                name_d:this.form.p_name,
                address:this.form.p_address,
                leader:this.form.p_person,
                note:this.form.p_mark
            }
            let loading = this.$loading({
                lock: true,
                text: this.$t("msg.post_loading"),
                spinner: 'el-icon-loading',
                background: 'rgba(0, 0, 0, 0.7)'
            }); 
            if(this.action=='add'){
                addTree(addData).then(response => {  
                    loading.close();   
                    if(response.code==10000){
                        this.$message.success(this.$t('msg.add_success'));
                        this.handleNodeExpand(this.nodeData,this.thisNode,1);
                        this.addID = response.data.nodeid;
                    }else{
                        
                        if(response.code==600600509){
                            this.$message.error(this.$t("msg.role_name_tips"));
                        }else if(response.code==600600430){
                            this.$message.error(this.$t('msg.project_error_in_level'));  
                        }else{
                            this.$message.error(this.$t("msg.set_error_tips")); 
                        }
                    }
                }).catch((error) => {
                    loading.close();
                    this.$message.error(this.$t("msg.set_error_tips"));  
                })
            }
            if(this.action=='edit'){
                saveTree(addData).then(response => {  
                    loading.close();   
                    if(response.code==10000){
                        this.$message.success(this.$t("msg.set_success_tips"));
                        this.nodeData.name = this.form.p_name;
                        this.handCurrentChange({id:addData.nodeid})
                    }else{
                        this.$message.error(this.$t("msg.set_error_tips")); 
                        if(response.code==600600509	){
                            this.$message.error(this.$t("msg.role_name_tips"));
                        }
                    }
                }).catch((error) => {
                    loading.close();
                    this.$message.error(this.$t("msg.set_error_tips"));  
                })
            }
            
        }
       
    },
    created(){  //初始化根节点  beforeMount
        let getData = {
            tree:1,
        }
        getTreeData().then(response => {        
            if(response.status==10000){
                console.log(response)
                 let Jdata=response.data;
                // for(var i=0;i<Jdata.length;i++){
                //     if(Jdata[i].id == '1'){
                //         if(this.lang!='zh'){
                //             Jdata[i].name=Jdata[i].name_en;
                //         }
                //         Jdata[i].children = undefined;
                //     }
                // }
                 this.treeData = Jdata;
            }else{
            
                this.$message.error(this.$t("msg.get_mode_error_tips")); 
            }
        }).catch((error) => {
        
            this.$message.error(this.$t("msg.get_mode_error_tips"));  
        })
        // let infoData = {nodeid:1}  
        // getNodeInfo(infoData).then(response => {
        //     if(response.code==10000){
        //         loading.close();
        //         let Jdata=response.data;
        //         if(this.lang!='zh'){
        //             Jdata.name = Jdata.name_en;
        //         }
        //         Jdata.key = Jdata.id;
        //         this.form.p_name = this.lang=='zh'?Jdata.name:Jdata.name_en;
        //         this.form.p_parent = this.lang=='zh'?Jdata.parent_name:Jdata.parent_name_en;
        //         this.form.p_address = Jdata.address;
        //         this.form.p_person = Jdata.leader;
        //         this.form.p_addtime = Jdata.add_time;
        //         this.form.p_mark = Jdata.note;  
        //     }else{
        //         loading.close();
        //         this.$message.error(this.$t("msg.get_mode_error_tips")); 
        //     }
        // }).catch((error) => {
        //     loading.close();
        //     this.$message.error(this.$t("msg.get_mode_error_tips"));  
        // })
    },

    mounted(){   
        this.$nextTick(function () { 
            setTimeout(() => {
                // let node=this.$refs.tree.store.nodesMap[1];
                // this.handleNodeExpand({id:'1',children:[]},node);
                // this.$refs.tree.setCurrentKey('1'); 
            }, 1000);  
        })
    }
}
</script>
<style  lang="scss" scoped>
@import '@/projects/home/styles/index.scss';
.el-row { height: calc(100vh - 140px); padding: 10px 0;}
.el-col {height: 100%;}
.mp_box { border-width: 1px; border-style: solid; height: 100%;}
</style>