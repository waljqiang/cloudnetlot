<template>
    <div
        v-loading.fullscreen.lock="fullscreenLoading"
        :element-loading-text="loading_txt"
        :element-loading-background="loading_bgcolor">
        <div class="list_top_box align_r">
             <el-button type="primary" size="mini" @click="showDialog('add')" >添加产品</el-button>
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
            v-bind:unchecked="true"
            v-on:listenPageIndex="setPageIndex"
            v-on:listenPageOffset="setPageOffset"
            v-on:listenCheckData="setCheckData"
            v-on:listenSort="setSort"
            >
        </table-counter>
        <el-dialog
            :title="dialogTitle"
            :close-on-click-modal='false'
            :visible.sync="dialogVisible"
            width="30%"
            :before-close="handleClose">
                <el-form label-width="30%" label-position="left" size="mini" style="width:90%; margin:0 auto" >
                    <el-form-item label="产品名称">
                        <el-input v-model="form.productname"></el-input>
                    </el-form-item>
                    <el-form-item label="产品类型">
                        <el-select :disabled="dialogAction=='add'?false:true" v-model="form.type">
                            <el-option label="路由器" :value='1'></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="产品型号">
                        <el-input :disabled="dialogAction=='add'?false:true" v-model="form.size"></el-input>
                    </el-form-item>
                    <el-form-item label="产品描述">
                        <el-input type="textarea" v-model="form.productdes"></el-input>
                    </el-form-item>
                </el-form>
                <span slot="footer" class="dialog-footer">
                    <el-button @click="dialogVisible = false">取 消</el-button>
                    <el-button type="primary" @click="subDialog">确 定</el-button>
                </span>
        </el-dialog>
    </div>
</template>
<script>
import table from '@/components/Table/table.vue';
import {productList,addProduct,editProduct,infoProduct,delProduct,publish} from '@/projects/develop/api/product'

export default {
    data(){
        return {
            fullscreenLoading:false,
            loading_txt:"请稍后……",
            loading_bgcolor:"rgba(0, 0, 0, 0.7)",

            column:[
                {"name":"","width":"5%","type":"selection"," code":"","prop":"","sortable":false},
                {"name":this.$t('common.list_sn'),"width":"5%", "width":"50","type":"id","prop":"","sortable":false},
                {"name":'产品名称',"width":"17%","type":"","prop":"name","sortable":"custom","sortData":"name"},
                {"name":'产品类型',"width":"17%","type":"","prop":"type_txt","sortable":"custom","sortData":"type"}, 
                {"name":'产品型号',"width":"16%","type":"","prop":"size","sortable":"custom","sortData":"size"},    
                {"name":'状态',"width":"15%","type":"","prop":"status_txt","sortable":"custom","sortData":"status"},
                {"name":'创建时间',"width":"15%","type":"","prop":"created_at","sortable":"custom","sortData":"created_at"},
                {"name":'操作',"width":"10%","type":"","prop":"config_btn","sortable":false}
            ], //表格类型
            thisgedata:[], //表格数据 
            pageoffset:10,   //表格当前行数
            pagelayout:"total,sizes, prev, pager, next, jumper",   
            pageindex:1, //当前页
            total:0,    //总数据数
            ispage:true,	//是否分页
            tableH:0,
            order_field:"created_at",	//查询类型
            order_sort:"desc",	//升序&降序  排序方法,asc: 升序,desc:降序
            check_data:[],  //表格选中的数据 

            dialogVisible:false,
            dialogAction:"",
            dialogTitle:"",
            actionIndex:"",
            form:{
                
            }
        }
    },
    components:{
        'table-counter':table
    },
    methods:{
        setSort(thisSort){
            if(thisSort.order=="ascending"){
                this.order_sort = 'asc';
            }else{
                this.order_sort = 'desc';
            }
            this.order_field = thisSort.prop;
            this.getList();       
        },
        setCheckData(thisCheckData){
            this.check_data = thisCheckData;
        },
        setPageOffset(offset){
            this.pageoffset = offset;
            this.getList();
        },
        setPageIndex(index){
            this.pageindex = index;
            this.getList();
        },
        getList(){
            let getData = {
                "pageIndex":this.pageindex,
                "pageOffset":this.pageoffset,
                "sortKey":this.order_field,
                "sort":this.order_sort,
                "keyword":""
            }
            productList(getData).then(response => {
                this.fullscreenLoading = false;
                if(response.status==10000){
                    let data = response.data;
                    this.handleTable(data.list,data.total);
                }else{
                    this.$message({
                        message:this.$t("msg.get_loading_error"),
                        type: 'error',
                        offset:100
                    });
                }
            }).catch((error) => {
                this.fullscreenLoading = false;
            })
        },
        handleTable(data,thistotal){
            var _this = this;
            for(var i=0;i<data.length;i++){
                data[i]['istruedata'] = true;
                if(data[i].type==1){
                    data[i].type_txt = '路由器';
                }
                var delBtn="";
                var editBtn="";
                var publishBtn="";
                if(data[i].status==1){
                    data[i].status_txt = '未发布';
                    publishBtn = '<button type="primary" icon="el-icon-setting" onclick="publishProduct(\''+data[i].prtid+'\')" >发布</button>';
                    delBtn = '<button type="primary" onclick="del(\''+data[i].prtid+'\')" icon="el-icon-delete">删除</button>';
                    editBtn = '<button type="primary" icon="el-icon-setting" onclick="showDialog(\'edit\',\''+data[i].prtid+'\')" >编辑</button>';
                }else if(data[i].status==2){
                    data[i].status_txt = '等待审核中';
                }else if(data[i].status==3){
                    data[i].status_txt = '发布失败';
                    delBtn = '<button type="primary" onclick="del(\''+data[i].prtid+'\')" icon="el-icon-delete">删除</button>';
                    editBtn = '<button type="primary" icon="el-icon-setting" onclick="showDialog(\'edit\',\''+data[i].prtid+'\')" >编辑</button>';
                }else if(data[i].status==4){
                    data[i].status_txt = '发布成功';
                    delBtn = '<button type="primary" onclick="del(\''+data[i].prtid+'\')" icon="el-icon-delete">删除</button>';
                    editBtn = '<button type="primary" icon="el-icon-setting" onclick="showDialog(\'edit\',\''+data[i].prtid+'\')" >编辑</button>';
                }else{
                }
                data[i].config_btn = `<div>`+delBtn+editBtn+publishBtn+`</div>`;
            }
            _this.total = parseInt(thistotal)
            _this.thisgedata = data;
        },
        handleClose(){
            this.dialogVisible = false;
        },
        showDialog(action,id,index){
            this.dialogAction = action;
            this.actionIndex = index;
            if(action == 'add'){
                this.dialogTitle = "添加";
                this.form = {
                    productname:"",
                    type:1,
                    size:"",
                    productdes:""
                }
            }else{
                this.dialogTitle = "编辑";
                infoProduct({prtid:id}).then(response => {
                    if(response.status==10000){
                        this.form = {
                            productname:response.data.name,
                            type:response.data.type,
                            size:response.data.size,
                            productdes:response.data.describe,
                            prtid:response.data.prtid
                        }
                    }else{
                        
                    }
                }).catch((error) => {
                    
                })
            }
            this.dialogVisible = true;
        },
        subDialog(){
            this.fullscreenLoading = true;
            if(this.dialogAction=='add'){
                addProduct(this.form).then(response => {
                    if(response.status==10000){
                        this.$message({
                            message:'添加成功',
                            type: 'success',
                            offset:100
                        });
                        this.getList();
                        this.dialogVisible = false;
                    }else{
                        this.$message({
                            message:'添加失败',
                            type: 'success',
                            offset:100
                        });   
                    }
                    this.fullscreenLoading = false;
                }).catch((error) => {
                    this.fullscreenLoading = false;
                })
            }else{
                editProduct(this.form).then(response => {
                    if(response.status==10000){
                        this.$message({
                            message:'修改成功',
                            type: 'success',
                            offset:100
                        });
                        this.getList();
                        this.dialogVisible = false;
                    }else{
                        this.$message({
                            message:'修改失败',
                            type: 'error',
                            offset:100
                        });
                    }
                     this.fullscreenLoading = false;
                }).catch((error) => {
                     this.fullscreenLoading = false;
                })
            }    
        },
        del(id){
            this.$confirm('确定要删除吗?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(() => {
                this.fullscreenLoading = true;
                delProduct({prtid:id}).then(response => {
                    if(response.status==10000){
                        this.$message({
                            message:'删除成功',
                            type: 'success',
                            offset:100
                        });

                        this.getList();
                    }else{
                        this.$message({
                            message:'删除失败',
                            type: 'error',
                            offset:100
                        });   
                    }
                    this.fullscreenLoading = false;
                }).catch((error) => {
                    this.fullscreenLoading = false;
                })
            }).catch(() => {
                   
            }); 
        },
        publishProduct(id){
           this.fullscreenLoading = true;
            publish({prtid:id}).then(response => {
                if(response.status==10000){
                    this.$message({
                        message:'发布成功',
                        type: 'success',
                        offset:100
                    });
                    this.getList();
                }else{
                    this.$message({
                        message:'发布失败',
                        type: 'error',
                        offset:100
                    }); 
                }
                 this.fullscreenLoading = false; 
            }).catch((error) => {
                this.fullscreenLoading = false;
            }) 
        }
    },
    beforeMount(){
        
    },
    mounted(){   
        this.$nextTick(function () {
            this.fullscreenLoading = true;
            this.getList();
            window.showDialog = this.showDialog;
            window.del = this.del;
            window.publishProduct = this.publishProduct;
        })  
    },
    beforeDestroy(){
        window.showDialog = undefined;
        window.del = undefined;
        window.publishProduct = undefined;
    }
}

</script>
<style lang="scss" scoped>

</style>


