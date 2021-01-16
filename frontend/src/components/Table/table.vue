<template>
   <div>
       <el-table
            ref="multipleTable"
            :row-key="getRowKey"
            :height="tableH"
            size="mini"
            :empty-text="emptytext"
            :highlight-current-row='isrowcurrent'
            :row-class-name="tableRowClassName"
            :header-row-class-name = "tableHeaderClass"
            :cell-class-name="tdClassName" @sort-change="sortChange" @selection-change="handleSelectionChange" :data="tableData"  >
            <template v-for="(colObj,index) in column" >
                <el-table-column   v-if="colObj.type=='selection'" :key="index" :reserve-selection="true" align="center"   :prop="colObj.prop" :type="colObj.type" :min-width="colObj.width"  :selectable="selectable" >
                </el-table-column>
                <el-table-column v-else-if="colObj.type=='id'" :key="index" :align="colObj.align"  :label="colObj.name" :width="colObj.width" :sortable="colObj.sortable" >
                    <template slot-scope="scope" >
                        {{ (pageindex - 1) * pageoffset + scope.$index+1 }}
                    </template>
                </el-table-column>
                <el-table-column v-else  :type="colObj.type" :key="index" :align="colObj.align" :prop="colObj.sortData" :label="colObj.name" :min-width="colObj.width"  :sortable="colObj.sortable" :show-overflow-tooltip="false" >
                    <template   slot-scope="scope">
                        <div v-html="scope.row[colObj.prop]" ></div>
                    </template>
                </el-table-column>
            </template>
        </el-table>
        <template>
        <el-pagination v-if="ispage" v-show="total>0"
            class='tableAlign '
            background
            small
            @size-change="handleSizeChange"
            @current-change="handleCurrentChange"
            :current-page="pageindex"
            :page-size="pageoffset"
            :page-sizes="sizes"
            :layout="pagelayout"
            :total="total">
            </el-pagination>
    </template></div>
</template>
<script>
import {globalPageOffset} from '@/public/js/common.js'
export default {
    props: ['column', 'thisdata','pagesizes','pagelayout','pageoffset','pageindex','total', 'ispage', 'tableheight', 'unchecked','rowcurrent'],
    data: function data() {
        return {
            tableData: [],
            tableH: this.tableheight ? this.tableheight : (this.pageoffset) * 25 +35,
            isrowcurrent:this.rowcurrent ? true:false,
            getRowKeys: function(row){
                return row.m_sn;
            },
            sizes:[globalPageOffset, 50, 100],
            emptytext:this.$t("msg.emptytext")
            //thisPageIndex:this.pageindex ? this.pageindex:1
        };
    },
    mounted:function(){
        this.$nextTick(function () {
            this.addTrList();
        })
    },
    watch: {
        thisdata(newQuestion, oldQuestion) {
            this.$refs.multipleTable.clearSelection();
            this.addTrList();
        },
        pageindex(newQuestion, oldQuestion) {
            this.$refs.multipleTable.clearSelection();
            this.addTrList();
        },
        pageoffset(newQuestion, oldQuestion) {
            this.$refs.multipleTable.clearSelection();
            this.addTrList();
        }
    },
    methods: {
        getRowKey(row){
            return row.id;
        }, 
        tableHeaderClass: function() {
           return 'table_header';
        },
        indexMethod: function(index) {
            //序号
            var table_sn = (this.pageindex - 1) * this.pageoffset + 1;
            return table_sn + index;
        },
        tableRowClassName: function(_ref) {
            var row = _ref.row;
            var rowIndex = _ref.rowIndex;
            if (rowIndex % 2 == 0) {
                return 'tr_even row_style'; //偶数行 
            }else {
                return 'tr_odd row_style'; //单数行
            }
        },
        tdClassName: function(_ref2) {
            var row = _ref2.row,
                column = _ref2.column,
                rowIndex = _ref2.rowIndex,
                columnIndex = _ref2.columnIndex;

            if (!row.istruedata) {
                return 'dom_off';
            }
        },
        sortChange: function(sortObj) {
            //排序回调
            this.$emit("listenSort",sortObj)
        },
        handleSizeChange: function(val){
            //pageoffset 变化
             this.$emit("listenPageOffset",val)
        },
        handleCurrentChange: function(thisPageIndex) {
            //分页回调
            this.$emit("listenPageIndex",thisPageIndex)
        
        },
        handleSelectionChange: function(val) {
            console.log(val)
            //checkbox 变化
           this.$emit("listenCheckData",val)
        },
        selectable: function(row) {
            //设置默认不可选择的tr
            if (this.unchecked) {
                if (row.state == 0) {
                    return 0;
                } else if (row.state == 1) {
                    return 1;
                } else {
                    return 1;
                }
            } else {
                return 1;
            }
        },
        toggleSelection:function(rows){
                rows.forEach(row => {
                    if(row.ischecked==1){
                        this.$refs.multipleTable.toggleRowSelection(row);
                    }
                });
        },
        addTrList: function() {
            //添加空数据
            this.tableData = [];
            for (var i = 0; i < this.pageoffset; i++){
                var item=this.thisdata[i];
                if(this.total-i>0&&item){
                    this.tableData.push(this.thisdata[i])
                }else{
                    if(this.ispage){
                        this.tableData.push({ istruedata: false });
                    }
                }
            }
           // this.toggleSelection(this.tableData);
            return this.tableData;
        }
    }
}
</script>
<style scoped>

</style>

