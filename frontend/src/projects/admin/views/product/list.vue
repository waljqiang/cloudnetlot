<template>
    <div
        v-loading.fullscreen.lock="fullscreenLoading"
        :element-loading-text="loading_txt"
        :element-loading-background="loading_bgcolor">
        <el-tabs v-model="activeName" >
            <el-tab-pane label="产品列表" name="first">
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
            </el-tab-pane>
        </el-tabs>
        <el-dialog
            title="产品审核"
            :visible.sync="dialogVisible"
            width="30%"
            >
            <el-radio v-model="radio" :label="1">通过申请</el-radio>
            <el-radio v-model="radio" :label="0">拒绝申请</el-radio>
            <span slot="footer" class="dialog-footer">
                <el-button @click="dialogVisible = false">取 消</el-button>
                <el-button type="primary" @click="audit">确 定</el-button>
            </span>
        </el-dialog>
        
    </div>
</template>
<script>
import table from '@/components/Table/table.vue';
import {productList,auditRequest} from '@/projects/admin/api/product'

export default {
    data(){
        return {
            fullscreenLoading:false,
            loading_txt:"请稍后……",
            loading_bgcolor:"rgba(0, 0, 0, 0.7)",

            column:[
                {"name":"","width":"5%","type":"selection"," code":"","prop":"","sortable":false},
                {"name":this.$t('common.list_sn'),"width":"5%", "width":"50","type":"id","prop":"","sortable":false},
                {"name":'产品名称',"width":"15%","type":"","prop":"prtname","sortable":"custom","sortData":"name"},
                {"name":'产品类型',"width":"15%","type":"","prop":"type_txt","sortable":"custom","sortData":"type"}, 
                {"name":'产品型号',"width":"11%","type":"","prop":"size","sortable":"custom","sortData":"size"},    
                {"name":'状态',"width":"12%","type":"","prop":"status_txt","sortable":"custom","sortData":"status"},
                {"name":'所属用户',"width":"12%","type":"","prop":"username","sortable":"custom","sortData":"username"},
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

            activeName:"first",
            dialogVisible:false,
            radio:1,
            pid:""
            
        }
    },
    components:{
        'table-counter':table
    },
    methods:{
        goDetails(pid){
            //this.column.splice(3,1)
            this.$router.push({name: 'product_info', query:{id:pid}})
        },
        setSort(thisSort){
            if(thisSort.order=="ascending"){
                this.order_sort = 'asc';
            }else{
                this.order_sort = 'desc';
            }
            this.order_field=thisSort.prop;
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
        showWindow(pid){
            this.pid = pid;
            this.dialogVisible = true;
        },
        audit(){
            this.fullscreenLoading = true;
            let auditData = {
                "prtid":this.pid,
                "enable":this.radio
            }
            auditRequest(auditData).then(response => {
                this.fullscreenLoading = false;
                if(response.status==10000){
                    this.$message({
                        message:'操作成功',
                        type: 'success',
                        offset:100
                    });
                    this.dialogVisible = false;
                }else{
                     this.$message({
                        message:'操作失败',
                        type: 'error',
                        offset:100
                    });
                }
                
                
            })
            .catch((error) => {
                this.fullscreenLoading = false;
            })
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
                var auditBtn="";
                if(data[i].type==1){
                    data[i].type_txt = '路由器';
                } 
                if(data[i].status==1){
                    data[i].status_txt = '未发布';
                }else if(data[i].status==2){
                    data[i].status_txt = '等待审核';
                    auditBtn='<button type="primary" icon="el-icon-setting" onclick="showWindow(\''+data[i].prtid+'\')" >审核</button>';
                }else if(data[i].status==3){
                    data[i].status_txt = '发布失败';
                }else if(data[i].status==4){
                    data[i].status_txt = '发布成功';
                }else{
                }
                data[i].config_btn = `<div>\
                                    `+auditBtn+`<button type="primary" icon="el-icon-setting" onclick="goDetails('`+data[i].prtid+`')" >详情</button>\
                                </div>`;
            }
            _this.total = parseInt(thistotal)
            _this.thisgedata = data;
        }
         
    },
    beforeMount(){
        
    },
    mounted(){   
        this.$nextTick(function () {
            this.fullscreenLoading = true;
            this.getList();
            window.showWindow = this.showWindow;
            window.goDetails = this.goDetails;
        })  
    },
    beforeDestroy(){
        window.showWindow = undefined;
        window.goDetails = undefined;
    }
}

</script>
<style lang="scss" scoped>

</style>


