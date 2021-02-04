<template>
    <div class="content_box2 light_gray_bg">
        <el-scrollbar id="main_scrol" style="height:  100%;width: auto;">
            <div class="flt white_bg amount_box" style="width:30%">
                <el-row style="width:95%">
                    <el-col class="align_r" :span="5">
                        <img src="@/projects/home/assets/common/access_dev.png" />
                    </el-col>
                    <el-col class="align_c middle_inner" :span="6">
                        <p>
                            <span class="blue_font">{{devTotal}}</span>
                            <br>
                            <span>设备总数</span>
                        </p>
                    </el-col>
                    <el-col class="align_c middle_inner " :span="6">
                        <p>
                            <span class="green_font">{{devOnline}}</span>
                            <br>
                            <span>在线设备</span>
                        </p>
                    </el-col>
                    <el-col class="align_c middle_inner" :span="6">
                        <p>
                            <span class="red_font">{{devOffline}}</span>
                            <br>
                            <span>离线设备</span>
                        </p>
                    </el-col>
                    <el-col class="align_c middle_inner" :span="1">
                        &nbsp;
                    </el-col>
                </el-row>
            </div>
            <div class="flt" style="width:70%" >
                <div class="frt white_bg amount_box" style="width:calc(100% - 20px);">
                    <el-row >
                        <el-col class="align_c" :offset="1" :span="2">
                            <img src="@/projects/home/assets/common/inspection.png" />
                        </el-col>
                        <el-col class="align_c middle_inner"  :span="2">
                            <p>
                                <span class="blue_font">0</span>
                                <br>
                                <span>巡检报告</span>
                            </p>
                        </el-col>
                        <el-col class="align_c" :offset="1" :span="2">
                            <img src="@/projects/home/assets/common/fault.png" />
                        </el-col>
                        <el-col class="align_c middle_inner" :span="2">
                            <p>
                                <span class="red_font">0</span>
                                <br>
                                <span>故障设备</span>
                            </p>
                        </el-col>
                        <el-col class="align_c" :offset="1" :span="2">
                            <img src="@/projects/home/assets/common/update.png" />
                        </el-col>
                        <el-col class="align_c middle_inner"  :span="2">
                            <p>
                                <span class="blue_font">0</span>
                                <br>
                                <span>可升级设备</span>
                            </p>
                        </el-col>
                        <el-col class="align_c" :offset="1" :span="3">
                            <img src="@/projects/home/assets/common/timed.png" />
                        </el-col>
                        <el-col class="align_l middle_inner" :span="5">
                            <div class="timed_box">
                                <div>
                                    <p>
                                        <span>自动备份</span>
                                        <span style="margin-left:20px">已启用</span>
                                        <img style="margin-left:10px"  class="cur" src="@/public/img/home/config_ico.png" />                    
                                    </p>
                                    <p>
                                        <span>定时巡检</span>
                                        <span style="margin-left:20px">已启用</span>
                                        <img style="margin-left:10px" class="cur" src="@/public/img/home/config_ico.png" />   
                                        <!-- <el-row style="width:100%">
                                            <el-col :span="9">自动备份</el-col>
                                            <el-col class="blue_font" :span="6">已启用</el-col>
                                            <el-col class="cur" :span="9"><img src="@/public/img/home/config_ico.png" /></el-col>
                                        </el-row> -->
                                    </p>
                                </div>
                                
                            </div>
                            
                        </el-col>
                    </el-row>
                </div>
            </div>
            <div class="clear"></div>
            <div class="flt" style="width:30%">
                <div class="pie_chart white_bg">
                    <div >
                        <div class="tit_ico"></div>
                        <span>设备统计</span> 
                        <i class="el-icon-refresh blue_font cur frt" style="font-weight:800;  font-size:16px" @click="initPieChart" />
                    </div>
                    <div id="pieChart" style="width:100%;">

                    </div>
                </div>
                <div class="log_box white_bg" style="min-height:335px">
                    <div >
                        <div class="tit_ico"></div>
                        <span>操作记录</span> 
                        <span class="blue_font cur frt" @click="goUrl('/maintain/log')">
                            查看更多
                            <i class="el-icon-d-arrow-right" />
                        </span>
                        
                    </div>
                    <div class="el-table__empty-block" style="height:300px" v-if="activities.length==0">
                        <span class="el-table__empty-text">暂无数据</span>
                    </div>
                    <el-timeline v-else >
                        <el-timeline-item
                            :hide-timestamp="true"
                            v-for="(activity, index) in activities"
                            :key="index"
                            :timestamp="activity.timestamp">
                                <el-card class="box-card">
                                    <el-form label-position="left" size="mini" label-width="40%" >
                                        <el-form-item label="账号">
                                            <span v-text="activity.username"></span>
                                        </el-form-item>
                                        <el-form-item label="设备MAC">
                                            <span v-text="activity.dev_mac"></span>
                                        </el-form-item>
                                        <el-form-item label="操作描述">
                                            <span >
                                                {{typeArr[activity.type-1]}}
                                            </span>
                                        </el-form-item>
                                        <el-form-item label="时间">
                                            <span v-text="activity.created_at"></span>
                                        </el-form-item>
                                    </el-form>
                                </el-card>
                                
                                <!-- {{activity.content}} -->
                        </el-timeline-item>
                    </el-timeline>
                </div>
            </div>
            
            <div class="flt" style="width:70%">
                 <div class="frt white_bg log_box" style="width:calc(100% - 20px);">
                     <div style="margin-bottom:10px" >
                        <div class="tit_ico"></div>
                        <span>最新接入设备</span> 
                        <span class="blue_font cur frt"  @click="goUrl('/device/list')">
                            查看更多
                            <i class="el-icon-d-arrow-right" />
                        </span>
                    </div>
                    <div class="table_par">
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
                    <div style="margin:35px 0 15px 0" >
                        <div class="tit_ico"></div>
                        <span>用户活跃度</span> 
                    </div>
                    <div style="position:relative">
                        <el-select v-model="lineTimeType" size="mini" @change="initLineChart" style="position:absolute;width:auto;z-index:2" >
                            <el-option
                            v-for="item in lineTimeList"
                            :key="item.val"
                            :label="item.name"
                            :value="item.val">
                            </el-option>
                        </el-select>
                        <div id="lineChart" style="width:100%; height:295px">

                        </div>
                    </div>
                     
                 </div>
            </div>
            <div class="clear"></div>            
        </el-scrollbar>
    </div>
</template>
<script>
import table from '@/components/Table/table'
import {globalPageOffset} from '@/public/js/common.js'
import {statistics} from '@/projects/home/api/product'
import {oplogList} from '@/projects/home/api/log'
import {devList,userActivity,devStatistics} from '@/projects/home/api/device'

// 引入 echarts 核心模块，核心模块提供了 echarts 使用必须要的接口。
import * as echarts from 'echarts/core';
// 引入柱状图图表，图表后缀都为 Chart
import {
    PieChart,LineChart
} from 'echarts/charts';
// 引入提示框，标题，直角坐标系组件，组件后缀都为 Component
import {
    // TitleComponent,
    TooltipComponent,
    GridComponent,
    LegendComponent
} from 'echarts/components';
// 引入 Canvas 渲染器，注意引入 CanvasRenderer 或者 SVGRenderer 是必须的一步
import { CanvasRenderer } from 'echarts/renderers';

// 注册必须的组件
echarts.use(
    [ TooltipComponent, GridComponent, PieChart, CanvasRenderer,LegendComponent,LineChart]
);
var myPieChart;
var myLineChart;
export default {
    data(){
        return {
            devTotal:'--',
            devOnline:'--',
            devOffline:'--',
            column:[
                {"name":this.$t('common.list_sn'), "width":"50","type":"id","align":"left","prop":"","sortable":false},
                {"name":this.$t('device.dev_mac'),"width":"18%","type":"","align":"center","prop":"dev_mac","sortable":false,},
                {"name":this.$t('device.dev_ip'),"width":"17%","type":"","align":"center","prop":"dev_ip","sortable":false}, 
                {"name":this.$t('device.dev_name'),"width":"12%","type":"","align":"center","prop":"name_txt","sortable":false},    
                {"name":this.$t('device.dev_type'),"width":"12%","type":"","align":"center","prop":"type","sortable":false},
                {"name":this.$t('device.dev_mode'),"width":"11%","type":"","align":"center","prop":"mode_txt","sortable":false},
                {"name":this.$t('device.dev_version'),"width":"15%","type":"","align":"center","prop":"ver_txt","sortable":false},
                {"name":this.$t('device.dev_access_time'),"width":"15%","type":"","align":"center","prop":"time_txt","sortable":false}
            ], //表格类型
            thisgedata:[{}], //表格数据 
            pageoffset:10,   //表格当前行数
            pagelayout:" sizes, prev, pager, next, jumper",    
            pageindex:1, //当前页
            total:0,    //总数据数
            ispage:false,	//是否分页
            tableH:0,
            order_field:"",	//查询类型
            order_sort:"",	//升序&降序
            check_data:[],  //表格选中的数据
            search_data:"",

            typeArr : ['','修改设备系统信息','修改设备网络信息','修改设备无线信息','修改设备终端信息','重启设备','升级设备','绑定设备','','上报信息'],
            activities: [],
            lineTimeList:[
                {
                    name:'近一天',
                    val:'24'
                },
                {
                    name:'近两天',
                    val:'48'
                },
                {
                    name:'近三天',
                    val:'72'
                },
                {
                    name:'近四天',
                    val:'96'
                },
                {
                    name:'近五天',
                    val:'120'
                },
                {
                    name:'近六天',
                    val:'144'
                },
                {
                    name:'近七天',
                    val:'168'
                }
            ],
            lineTimeType:'72'
             
        }
    },
    components:{
        'table-counter':table,
    },
    methods:{
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
            this.getDevList();
        },
        setPageIndex(index){
            this.pageindex = index;
            this.getDevList();
        },

        goUrl(url){
            this.$router.push({ path: url })  
        },
        getDate(day){
            var date = new Date();
             date.setTime(date.getTime()-Number(day)*60*60*1000);
            var Y = date.getFullYear() + '-';
            var M = (date.getMonth()+1 < 10 ? ''+(date.getMonth()+1) : date.getMonth()+1) + '-';
            var D = date.getDate() + ' ';
            var h = date.getHours() + ':';
            var m = date.getMinutes() + ':';
            var s = date.getSeconds(); 
            return Y+M+D;
        },
        loadHour(){
            let hArr = [];
            for(var i=0;i<24;i++){
                if(i<10){
                    hArr.push("0"+i+":00");
                }else{
                    hArr.push(i+":00");
                }
                
            }
            return hArr;
        },
        getDevList(){
            let getData = {
                'pageIndex':'1',
                'pageOffset':'10',
                'sortkey':'join_time',
                'sort':'desc'
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
        handleTable(data,thistotal){
            var _this = this;
            for(var i=0;i<data.length;i++){
                data[i]['istruedata'] = true;
                let modeTxt = ['网关','中继','WISP','WDS','AP']
                data[i].mode_txt = modeTxt[Number(data[i].mode)-1];
                data[i].name_txt = '<div class="txt_el" title="'+data[i].name+'" >'+data[i].name+'</div>';
                data[i].ver_txt = '<div class="txt_el" title="'+data[i].version+'" >'+data[i].version+'</div>';
                data[i].time_txt = '<div class="txt_el" title="'+data[i].join_time+'">'+data[i].join_time+'</div>';
            }
            _this.total = parseInt(thistotal)
            _this.thisgedata = data;
        },
        initPieChart(){
            myPieChart.showLoading({
                text : '加载中...',  //加载时候的文本
                color:'#389ff3',      //加载时候小圆圈的颜色
              //  textColor:'white',  //加载时候文本颜色
                //maskColor:'#082042' //加载时候的背景颜色
            });
            statistics().then(response => {      
                if(response.status==10000){
                    // 指定图表的配置项和数据
                    let option = {
                         grid: {
                            top: '0',
                            left: '0%',
                            right: '0%',
                            bottom: '0%',
                            
                        },
                        tooltip: {
                            trigger: 'item',
                            backgroundColor:'rgba(50,50,50,0.7)',
                            borderWidth:0,
                            textStyle:{
                                color:"#fff"
                            },
                            formatter:function(p) {
                                let str = p.data.name+"  "+p.data.value+'<br/>';
                                for(var item in p.data.devices_statics){
                                    str += item+"  "+p.data.devices_statics[item]+'<br/>';
                                }
                                    
                                return  str;
                            }
                        },
                        legend: {
                            type: "scroll",
                            orient: 'vertical',
                            top: 20,
                            right: 10,
                            bottom: 20,
                        },
                        color:['#389ff3','#00c58b', '#ff9c00', '#ff6969', '#91c7ae','#749f83',  '#ca8622', '#bda29a','#6e7074', '#546570', '#c4ccd3'],
                        series: [
                            {
                                name: '产品统计',
                                type: 'pie',
                                width: "100%",
                                radius: ['60%', '90%'],
                                center: ['35%', '50%'],
                                avoidLabelOverlap: true,
                                roseType: 'radius',

                                label: {
                                    show: false,
                                    position: "center",
                                    fontSize: 14,
                                },
                                emphasis: {
                                    label: {
                                        show: true,
                                        fontSize: '15',
                                     fontWeight: 'bold'
                                    }
                                },
                                labelLine: {
                                    show: false
                                },
                                data: this.objArrtransArr(response.data,'devices_total','name')
                            }
                        ]
                    };
                    // 使用刚指定的配置项和数据显示图表。
                    myPieChart.setOption(option,true);
                    window.onresize = myPieChart.resize;
                    myPieChart.hideLoading(); 
                }
                
            }).catch((error) => {
                this.$message({
                    message:'',
                    type: 'error',
                    offset:100
                });
            })             
        },
        initLineChart(){
            myLineChart.showLoading({
                text : '加载中...',  //加载时候的文本
                color:'#389ff3',      //加载时候小圆圈的颜色
              //  textColor:'white',  //加载时候文本颜色
                //maskColor:'#082042' //加载时候的背景颜色
            });
            let postData = {
                "end":this.getDate('24')+" 23:59:59",
                "start":this.getDate(this.lineTimeType)+" 00:00:00"
            }
            userActivity(postData).then(response => {      
                if(response.status==10000){
                    // 指定图表的配置项和数据
                    let option = {
                        grid: {
                            top: '45px',
                            left: '0%',
                            right: '0%',
                            bottom: '0%',
                            containLabel: true
                        },
                         tooltip: {
                            trigger: 'axis',
                            backgroundColor:'rgba(50,50,50,0.7)',
                            borderWidth:0,
                            textStyle:{
                                color:"#fff"
                            },
                        },
                        legend: {
                            type: "scroll",
                            orient: 'horizontal',
                            padding:[0,0,0,200],
                            top: 0,
                            right: 10,
                        },
                        color:['#389ff3','#00c58b', '#ff9c00', '#ff6969', '#91c7ae','#749f83',  '#ca8622', '#bda29a','#6e7074', '#546570', '#c4ccd3'],
                         xAxis: {
                           type: 'category',
                            boundaryGap: true,
                            data: this.loadHour()
                        },
                        yAxis: {
                            type: 'value'
                        },
                        series: this.formatterLineData(response.data)
                    };
                    // 使用刚指定的配置项和数据显示图表。
                    myLineChart.setOption(option,true);
                   // myLineChart.resize = window.onresize
                    // window.onresize = myLineChart.resize;
                    myLineChart.hideLoading(); 
                }
                
            }).catch((error) => {
                this.$message({
                    message:'',
                    type: 'error',
                    offset:100
                });
            })             
        },
        formatterLineData(data){
            let lineData = [];
            for(var key in data){
                let ketObj = {
                    name: key,
                    smooth: true,
                    symbol: 'none',
                    type: 'line',
                    data: []
                }
                for(var index in data[key] ){
                    ketObj.data.push(data[key][index])
                }
                lineData.push(ketObj)
            }
            return lineData;
        },
        objArrtransArr(olddata, oldval, oldname) {
            olddata.forEach(item => {
            // 定义数组内部对象形式
                item.value = item[oldval];
                item.name = item[oldname];
                
            });
            return olddata;
        },
        getLogList(){
            let getData = {
                status:0,
                pageIndex:1,
                pageOffset:2,
                sortkey:'created_at',
                sort:'desc'
            }
            oplogList(getData).then(response => {   
                if(response.status==10000){
                    this.activities = response.data.list;
                }
            }).catch((error) => {
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
        getDevCount(){
            devStatistics().then(response => {      
                if(response.status==10000){
                    this.devTotal=response.data.all;
                    this.devOnline=response.data.online;
                    this.devOffline=response.data.offline;
                }
            }).catch((error) => {
                this.$message({
                    message: '',
                    type: 'error',
                    offset:100
                });
            })  
        }
    },
    beforeCreate(){
        this.$store.commit('showloadding',{show:true}); 
    },
    created(){
        
    },
    beforeMount(){
       
    },
    mounted(){   
        this.$nextTick(function () {
            document.getElementById('pieChart').style.height = document.getElementById('pieChart').offsetWidth*0.55+'px';
            // 基于准备好的dom，初始化echarts实例
            myPieChart = echarts.init(document.getElementById('pieChart'));
            myLineChart = echarts.init(document.getElementById('lineChart'));
            this.getDevList();
            this.getLogList();
            this.getDevCount();
            this.initPieChart();
            this.initLineChart();
           // this.$store.commit('showloadding',{show:false}); 
        })  
    }
}

</script>
<style lang="scss" scoped>
@import '@/projects/home/styles/index.scss';
.content_box2 { 
    position: fixed;    
    width: calc(100% - 60px);
    min-width: 1200px;
    height: calc(100vh - 60px);
    padding: 20px;
    top: 60px;
    left: 60px;
    overflow:hidden
}
.amount_box { height: 105px; border-radius: 5px; line-height: 105px;}
.amount_box img{ vertical-align: middle;}
.middle_inner> p {display: inline-block;vertical-align: middle;line-height: 22px;}
.middle_inner> p> span:first-child {font-size: 18px;}
.timed_box {display: table; height: 105px; width: 100%;}
.timed_box>div {display: table-cell; vertical-align: middle; line-height: 22px;}
.timed_box>div>p {margin: 1px;}
.pie_chart,.log_box { width: 100%; margin-top: 15px;  border-radius: 5px; padding: 15px; line-height: 18px;}
.el-timeline { padding:  15px 0 0 15px;}
.el-card:hover {border:1px solid $blue_border;}
.el-form-item--mini.el-form-item { margin-bottom: 1px;}
.el-timeline-item {padding-bottom:10px}

// .table_par { border: 1px solid $light_gray_border;}
</style>
<style scoped>
.tableAlign {display: none;}
</style>
