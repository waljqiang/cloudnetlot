(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-cb74dac4"],{"0286":function(t,e,a){"use strict";var s=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",[a("el-table",{ref:"multipleTable",attrs:{"row-key":t.getRowKey,height:t.tableH,size:"mini","empty-text":t.emptytext,"highlight-current-row":t.isrowcurrent,"row-class-name":t.tableRowClassName,"header-row-class-name":t.tableHeaderClass,"cell-class-name":t.tdClassName,data:t.tableData},on:{"sort-change":t.sortChange,"selection-change":t.handleSelectionChange}},[t._l(t.column,(function(e,s){return["selection"==e.type?a("el-table-column",{key:s,attrs:{"reserve-selection":!0,align:"center",prop:e.prop,type:e.type,"min-width":e.width,selectable:t.selectable}}):"id"==e.type?a("el-table-column",{key:s,attrs:{align:e.align,label:e.name,width:e.width,sortable:e.sortable},scopedSlots:t._u([{key:"default",fn:function(e){return[t._v(" "+t._s((t.pageindex-1)*t.pageoffset+e.$index+1)+" ")]}}],null,!0)}):a("el-table-column",{key:s,attrs:{type:e.type,align:e.align,prop:e.sortData,label:e.name,"min-width":e.width,sortable:e.sortable,"show-overflow-tooltip":!1},scopedSlots:t._u([{key:"default",fn:function(s){return[a("div",{domProps:{innerHTML:t._s(s.row[e.prop])}})]}}],null,!0)})]}))],2),[t.ispage?a("el-pagination",{directives:[{name:"show",rawName:"v-show",value:t.total>0,expression:"total>0"}],staticClass:"tableAlign ",attrs:{background:"",small:"","current-page":t.pageindex,"page-size":t.pageoffset,"page-sizes":t.sizes,layout:t.pagelayout,total:t.total},on:{"size-change":t.handleSizeChange,"current-change":t.handleCurrentChange}}):t._e()]],2)},i=[],n=(a("4160"),a("159b"),a("a3a6")),o={props:["column","thisdata","pagesizes","pagelayout","pageoffset","pageindex","total","ispage","tableheight","unchecked","rowcurrent"],data:function(){return{tableData:[],tableH:this.tableheight?this.tableheight:25*this.pageoffset+35,isrowcurrent:!!this.rowcurrent,getRowKeys:function(t){return t.m_sn},sizes:[n["a"],50,100],emptytext:this.$t("msg.emptytext")}},mounted:function(){this.$nextTick((function(){this.addTrList()}))},watch:{thisdata:function(t,e){this.$refs.multipleTable.clearSelection(),this.addTrList()},pageindex:function(t,e){this.$refs.multipleTable.clearSelection(),this.addTrList()},pageoffset:function(t,e){this.$refs.multipleTable.clearSelection(),this.addTrList()}},methods:{getRowKey:function(t){return t.id},tableHeaderClass:function(){return"table_header"},indexMethod:function(t){var e=(this.pageindex-1)*this.pageoffset+1;return e+t},tableRowClassName:function(t){t.row;var e=t.rowIndex;return e%2==0?"tr_even row_style":"tr_odd row_style"},tdClassName:function(t){var e=t.row;t.column,t.rowIndex,t.columnIndex;if(!e.istruedata)return"dom_off"},sortChange:function(t){this.$emit("listenSort",t)},handleSizeChange:function(t){this.$emit("listenPageOffset",t)},handleCurrentChange:function(t){this.$emit("listenPageIndex",t)},handleSelectionChange:function(t){console.log(t),this.$emit("listenCheckData",t)},selectable:function(t){return this.unchecked?0==t.state?0:(t.state,1):1},toggleSelection:function(t){var e=this;t.forEach((function(t){1==t.ischecked&&e.$refs.multipleTable.toggleRowSelection(t)}))},addTrList:function(){this.tableData=[];for(var t=0;t<this.pageoffset;t++){var e=this.thisdata[t];this.total-t>0&&e?this.tableData.push(this.thisdata[t]):this.ispage&&this.tableData.push({istruedata:!1})}return this.tableData}}},l=o,r=a("2877"),c=Object(r["a"])(l,s,i,!1,null,"038a410a",null);e["a"]=c.exports},1386:function(t,e,a){"use strict";a.r(e);var s=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",[a("div",{staticStyle:{height:"38px"}},[a("el-button",{staticClass:"el-dropdown-link mr20 frt",on:{click:function(e){return t.read(1)}}},[t._v(" 全部设为已读 ")])],1),a("table-counter",{attrs:{column:t.column,thisdata:t.thisgedata,pagesizes:!1,pagelayout:t.pagelayout,pageoffset:t.pageoffset,pageindex:t.pageindex,total:t.total,ispage:t.ispage,tableheight:t.tableH,unchecked:!1},on:{listenPageIndex:t.setPageIndex,listenPageOffset:t.setPageOffset,listenCheckData:t.setCheckData,listenSort:t.setSort}}),a("el-dialog",{attrs:{title:"",visible:t.dialogVisible,width:"600","show-close":!1,"before-close":t.handleClose},on:{"update:visible":function(e){t.dialogVisible=e}}},[a("div",{staticClass:"frt",attrs:{slot:"title"},slot:"title"},[a("div",{staticClass:"custom_close align_c",on:{click:t.handleClose}},[a("i",{staticClass:"el-icon-close"})])]),a("div",{},[a("div",{staticClass:"msg_title"},[t._v(" 详情 ")])]),""!=t.infos?a("div",{staticClass:"msg_content"},[a("el-row",{attrs:{gutter:20}},[a("el-col",{attrs:{offset:2,span:4}},[t._v("用户")]),a("el-col",{attrs:{span:18}},[t._v(t._s(""==t.infos.username?"-----":t.infos.username))])],1),a("el-row",{attrs:{gutter:20}},[a("el-col",{attrs:{offset:2,span:4}},[t._v("设备MAC")]),a("el-col",{attrs:{span:18}},[t._v(t._s(""==t.infos.dev_mac?"-----":t.infos.dev_mac))])],1),a("el-row",{attrs:{gutter:20}},[a("el-col",{attrs:{offset:2,span:4}},[t._v("设备IP")]),a("el-col",{attrs:{span:18}},[t._v(t._s(""==t.infos.dev_ip?"-----":t.infos.dev_ip))])],1),a("el-row",{attrs:{gutter:20}},[a("el-col",{attrs:{offset:2,span:4}},[t._v("设备型号")]),a("el-col",{attrs:{span:18}},[t._v(t._s(""==t.infos.dev_type?"-----":t.infos.dev_type))])],1),a("el-row",{attrs:{gutter:20}},[a("el-col",{attrs:{offset:2,span:4}},[t._v("设备名称")]),a("el-col",{attrs:{span:18}},[t._v(t._s(""==t.infos.dev_name?"-----":t.infos.dev_name))])],1),a("el-row",{attrs:{gutter:20}},[a("el-col",{attrs:{offset:2,span:4}},[t._v("版本号")]),a("el-col",{attrs:{span:18}},[t._v(t._s(""==t.infos.version?"-----":t.infos.version))])],1)],1):t._e(),a("div",{staticClass:"msg_content"},[a("el-row",{attrs:{gutter:20}},[a("el-col",{attrs:{offset:2,span:4}},[t._v("操作行为")]),a("el-col",{attrs:{span:18}},[t._v(t._s(t.infos.opTypeTxt))])],1),a("el-row",{attrs:{gutter:20}},[a("el-col",{attrs:{offset:2,span:4}},[t._v("状态")]),a("el-col",{attrs:{span:18}},["3"==t.infos.status?a("span",{staticClass:"red_font"},[t._v(" "+t._s(t.infos.opStatusTxt)+" "),a("span",{staticClass:"blue_font ml20 cur"},[t._v(" [再次执行] ")])]):a("span",[t._v(" "+t._s(t.infos.opStatusTxt)+" ")])])],1),a("el-row",{attrs:{gutter:20}},[a("el-col",{attrs:{offset:2,span:4}},[t._v("操作时间")]),a("el-col",{attrs:{span:18}},[t._v(t._s(""==t.infos.created_at?"-----":t.infos.created_at))])],1)],1)])],1)},i=[],n=(a("a9e3"),a("0286")),o=a("a3a6"),l=a("b775");function r(t){return Object(l["a"])({url:"backend/oplog/list",method:"post",data:t})}function c(t){return Object(l["a"])({url:"backend/oplog/info",method:"get",params:t})}function d(t){return Object(l["a"])({url:"backend/oplog/readed",method:"post",data:t})}var h=a("1661"),g=a.n(h),p=a("e7fc"),m=a.n(p),u=a("a674"),b=a.n(u),f=["","修改设备系统信息","修改设备网络信息","修改设备无线信息","修改设备终端信息","重启设备","升级设备","绑定设备",""],A=["命令未发送","命令已发送","命令执行失败","命令执行成功"],w={data:function(){return{column:[{name:"",width:"5%",type:"selection"," code":"",prop:"",sortable:!1},{name:this.$t("common.list_sn"),width:"150",type:"id",align:"left",prop:"",sortable:!1},{name:"内容",width:"50%",type:"",align:"left",prop:"content_text",sortable:!1},{name:"状态",width:"10%",type:"",align:"center",prop:"status_text",sortable:!1},{name:"时间",width:"15%",type:"",align:"left",prop:"created_at",sortable:"custom",sortData:"created_at"},{name:"查看",width:"10%",type:"",align:"center",prop:"config_btn",sortable:!1}],thisgedata:[{}],pageoffset:o["a"],pagelayout:" sizes, prev, pager, next, jumper",pageindex:1,total:0,ispage:!0,tableH:0,order_field:"",order_sort:"",check_data:[],search_data:"",dialogVisible:!1,infos:""}},components:{"table-counter":n["a"]},methods:{setSort:function(t){console.log(t),"ascending"==t.order?this.order_sort="asc":"descending"==t.order?this.order_sort="desc":this.order_sort="",this.order_field=t.prop,this.getData()},setCheckData:function(t){this.check_data=t},setPageOffset:function(t){this.pageoffset=t,this.getData()},setPageIndex:function(t){this.pageindex=t,this.getData()},handleTable:function(t,e){for(var a=this,s=0;s<t.length;s++){t[s]["istruedata"]=!0;var i=t[s].type;t[s]["content_text"]=f[i-1]+"["+t[s].dev_mac+"]","0"==t[s].readed?t[s].status_text='<img style="vertical-align:middle" src="'+g.a+'" width="15" >':t[s].status_text='<img style="vertical-align:middle" src="'+m.a+'" width="15" >',t[s].config_btn='<div style="display:inline-block;cursor:pointer;" onclick="showDialog(\''+t[s].id+"','"+t[s].readed+'\')" title="'+this.$t("common.config_txt")+'" class="blue_font"><img style="vertical-align:middle" src="'+b.a+'" width="15" ></div>'}a.total=parseInt(e),a.thisgedata=t},getData:function(){var t=this,e={status:3,pageIndex:this.pageindex,pageOffset:this.pageoffset};""!=this.order_sort&&(e.sortKey=this.order_field,e.sort=this.order_sort),r(e).then((function(e){if(1e4==e.status){var a=e.data;t.handleTable(a.list,a.total)}t.$store.commit("showloadding",{show:!1})})).catch((function(e){t.$store.commit("showloadding",{show:!1});var a={},s=e.errorCode[0];switch(s){case 600400100:a.message=t.$t("msg.pars_err1");break;case 600400169:a.message=t.$t("msg.pars_err1");break}t.$message({message:a.message,type:"error",offset:100})}))},showDialog:function(t,e){var a=this;this.$store.commit("showloadding",{show:!0});var s={id:t};c(s).then((function(s){if(1e4==s.status){a.infos=s.data;var i=s.data.type,n=s.data.status;a.infos["opTypeTxt"]=f[Number(i)-1]+"["+s.data.dev_mac+"]",a.infos["opStatusTxt"]=A[Number(n)-1],a.dialogVisible=!0,"0"==e&&a.read(0,t)}a.$store.commit("showloadding",{show:!1})})).catch((function(t){a.$store.commit("showloadding",{show:!1});var e={},s=t.errorCode[0];switch(s){case 600400100:e.message=a.$t("msg.pars_err1");break;case 600400192:e.message=a.$t("msg.pars_err1");break}a.$message({message:e.message,type:"error",offset:100})}))},read:function(t,e){var a=this,s={ids:[]};if(0==t)s.ids=[e];else{for(var i=[],n=0;n<this.check_data.length;n++)this.check_data[n].istruedata&&i.push(this.check_data[n].id);if(i.length<=0)return void this.$message.error("请至少选择一条数据");s.ids=i,this.$store.commit("showloadding",{show:!0})}d(s).then((function(e){1e4==e.status&&0!=t&&(a.$message({message:"操作成功",type:"success",offset:100}),a.getData()),a.$store.commit("showloadding",{show:!1})})).catch((function(t){a.$store.commit("showloadding",{show:!1});var e={},s=t.errorCode[0];switch(s){case 600400100:e.message=a.$t("msg.pars_err1");break;case 600400192:e.message=a.$t("msg.pars_err1");break}a.$message({message:e.message,type:"error",offset:100})}))},handleClose:function(){this.dialogVisible=!1}},beforeCreate:function(){this.$store.commit("showloadding",{show:!0,text:this.$t("common.plase_wait")})},beforeMount:function(){},mounted:function(){this.$nextTick((function(){this.getData(),window.showDialog=this.showDialog}))}},I=w,v=(a("8eac"),a("2877")),y=Object(v["a"])(I,s,i,!1,null,"7ccce3ce",null);e["default"]=y.exports},1661:function(t,e){t.exports="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAA8AAAAPCAYAAAA71pVKAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA3FpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMTQyIDc5LjE2MDkyNCwgMjAxNy8wNy8xMy0wMTowNjozOSAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD0ieG1wLmRpZDo3NWMyZTE0Mi0xMDM4LTQzNDktOWIyYy00ODI0NGJkN2I4MWIiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6QTNEQTRCN0U1NjQ1MTFFQjg2RDE5M0QzNUFDNDU5RTYiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6QTNEQTRCN0Q1NjQ1MTFFQjg2RDE5M0QzNUFDNDU5RTYiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIChXaW5kb3dzKSI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOmM2YTBkZGM3LWU0MGItZGQ0MS04NTY0LTIyNmY1NWQ0MDZhNCIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDo3NWMyZTE0Mi0xMDM4LTQzNDktOWIyYy00ODI0NGJkN2I4MWIiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz5K0zIVAAABtElEQVR42sSTT0sbQRjGfzPZbWI2mxjDioRAtVYpUgShYAWPguC1X6PHfoveSj9AL4Va6KXgscdiFJXeWqqCQqEBiYoa3ZhkpvNOEyoFKbSHzrK88848z/vnmRllreVvh+YfRvD09c57hVpWCv5UhGzrPs5itwI3WXn2ZC5zfmUYjjSdHqhbiHcycNIyxEOa5+925rVSyrRSw8evTY7P2mQdQAL0zK9ffCFetNqs7zYRvPC0CBbnNIvTZTZ3j+lep5SyjtRvQaz4ttNm7dMR85Nln1l4egCI8wGz9yqs1huQplRycO2yihX/7cZ3Jqpl8rkAY39TW0pL4pDZWolX9W/YtM39It6KP1Mt8ehuxE1hg4EYoQtT0JZ8qEhGirz4sMdYXtO4NEzUEiK3HmmDFNu7mTl0YmQxvFk/5MvRJUsPR10Lo+io4K34si77ghO8z+xU48o1t33QpJoMMzlW8D0tPEj8mcq861p6PJ2w37igvtdkZrwiakvZrtSsZqo24s454wld87OXgTAZV19SDKnEZU5bPY8XQNCxOny59tlH6t+cW2+Yks8LZhGe+m8P44cAAwBS/bX7usIrqgAAAABJRU5ErkJggg=="},"22f3":function(t,e,a){},"8eac":function(t,e,a){"use strict";a("22f3")},a3a6:function(t,e,a){"use strict";function s(){return document.documentElement.clientHeight||document.body.clientHeight}a.d(e,"a",(function(){return n}));var i=Math.floor((s()-245)/25),n=i>10?i:10},a674:function(t,e){t.exports="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAA8AAAAPCAYAAAA71pVKAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyFpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMTQyIDc5LjE2MDkyNCwgMjAxNy8wNy8xMy0wMTowNjozOSAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIChXaW5kb3dzKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDoyOUJCNTM4NzU2NDQxMUVCQUNDNTg4RUEyQTk2RjQ0OSIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDoyOUJCNTM4ODU2NDQxMUVCQUNDNTg4RUEyQTk2RjQ0OSI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjI5QkI1Mzg1NTY0NDExRUJBQ0M1ODhFQTJBOTZGNDQ5IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjI5QkI1Mzg2NTY0NDExRUJBQ0M1ODhFQTJBOTZGNDQ5Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+8rAxJAAAAYdJREFUeNpi3Lh58/s/f/8K/P/3j+E/AypgYmRkYGRkfB0UECAGE/v/H0nV2vXr/+MDL1++/L9m3bplyJphmAlm0v0HD4H4EcODh4/ANAgsWbaMQUxMjMHGyipyzfr1y9EcxsACc4Sigjy6HAMzMzOYFhcXZ7Czto5Yv3EjK5AbAtfMgOFTBNDV1mbYsHEDwz+g64ICAhmYmJiCgWGAsBmmF+ZURQU5MH3zxk2GWdOnwRUe2L2bwdbBAdX01WvXYgTShfPn/+dmZf4/9e4/CgaJdXR0OcNjA5tz586exRDbMg1DHCT29NH9PUBDUDWDnA1zOjL4hztIQAHGgOJXBoyEglszEwMFAKuzU9MzGBbXZGEoBok5ubkxGP7Y9X9ekjLQ2dB4k5eTAaU9hn/ANK6jq8uQnpnFMBPNAJDGAImpDAxpigzzZ93/zwKMeIgTmFB9oK2jwzBpKmqIz09WAWuEAcZFS5Yc4ObmtgelIgakHMMIyVEINjMbA8Pfn4zvN5XAFDECBBgAWsLTKhUEZjUAAAAASUVORK5CYII="},e7fc:function(t,e){t.exports="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAA8AAAAPCAYAAAA71pVKAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA3FpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMTQyIDc5LjE2MDkyNCwgMjAxNy8wNy8xMy0wMTowNjozOSAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD0ieG1wLmRpZDo3NWMyZTE0Mi0xMDM4LTQzNDktOWIyYy00ODI0NGJkN2I4MWIiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6N0M4MUI5NDE1NjQ1MTFFQjhDMEZGMTgzRjBGNTEzOEMiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6N0M4MUI5NDA1NjQ1MTFFQjhDMEZGMTgzRjBGNTEzOEMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIChXaW5kb3dzKSI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOmM2YTBkZGM3LWU0MGItZGQ0MS04NTY0LTIyNmY1NWQ0MDZhNCIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDo3NWMyZTE0Mi0xMDM4LTQzNDktOWIyYy00ODI0NGJkN2I4MWIiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz6h2RZAAAABi0lEQVR42pRRK4sCURT+ZhxFwaAgCKYJFk3iVINsdJvFsIgos8P6I0w2izYZVzSY3CSIySRYDC7WLWJdEDUIvnU9F+biY9xlPzjhfI97zpwRYIJqterdbrcdo7fZbC+qqn7d+oRbol6va6vVSksmk7Bardjtdmg0GnA4HB/pdLpgGq7Vat71et2Zz+c4HA6sTqcTBEGAxWJh5Xa7YbfbnzOZzDcPVyqVt/1+r06nU+RyOTxCPp+Hx+OBJEnvmqbpkq7rBUVRnmRZRqlUYqZyuXwXzGaz7BMSiQQmk8nrOadI5/VYkHA8HrnRDIZO/sFgEJYMYTQa0VF+nUw6+UKhEON4mIjFYoHxeGw6mfhAIMCDBPHSEI1G2cu9Xu8qSD3xpF9Cup0Qj8fRbrdRLBb5r/L7/YyfzWaPw06nE61WC6IoIpVKcb7f7zM+FothuVzer03H6Ha78Pl8iEQiVxOoJ55046hX4eFwyA5CK5qBeNLJd7d2MBjEX3C5XKx4eLPZfDabzTD+Ccr9CDAAWNCkiXDzoX8AAAAASUVORK5CYII="}}]);