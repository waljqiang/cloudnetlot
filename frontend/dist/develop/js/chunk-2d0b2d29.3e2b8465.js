(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-2d0b2d29"],{"266e":function(e,t,s){"use strict";s.r(t);var n=function(){var e=this,t=e.$createElement,s=e._self._c||t;return s("div",{directives:[{name:"loading",rawName:"v-loading.fullscreen.lock",value:e.fullscreenLoading,expression:"fullscreenLoading",modifiers:{fullscreen:!0,lock:!0}}],staticClass:"form_box",attrs:{"element-loading-text":e.loading_txt,"element-loading-background":e.loading_bgcolor}},[s("el-tabs",{model:{value:e.active,callback:function(t){e.active=t},expression:"active"}},[s("el-tab-pane",{attrs:{label:"用户信息",name:"first"}},[s("el-form",{attrs:{"label-width":"200px"}},[s("el-form-item",{attrs:{label:"用户名"}},[s("span",{domProps:{textContent:e._s(e.infos.username)}})]),s("el-form-item",{attrs:{label:"昵称"}},[s("span",{domProps:{textContent:e._s(e.infos.nickname)}})]),s("el-form-item",{attrs:{label:"邮箱"}},[s("span",{domProps:{textContent:e._s(e.infos.email)}})]),s("el-form-item",{attrs:{label:"地址"}},[s("span",{domProps:{textContent:e._s(e.infos.address)}})]),s("el-form-item",{attrs:{label:"status"}},[0==e.infos.status?s("span",[e._v("未申请开发者")]):1==e.infos.status?s("span",[e._v("申请审核中")]):2==e.infos.status?s("span",[e._v("审核未通过")]):s("span",[e._v("审核通过")])]),s("el-form-item",{attrs:{label:"开发者appid"}},[s("span",{domProps:{textContent:e._s(e.infos.develop.appid)}})]),s("el-form-item",{attrs:{label:"开发者秘钥"}},[s("span",{domProps:{textContent:e._s(e.infos.develop.secret)}})]),s("el-form-item",{attrs:{label:"开发者姓名"}},[s("span",{domProps:{textContent:e._s(e.infos.develop.name)}})]),s("el-form-item",{attrs:{label:"开发者所属企业名称"}},[s("span",{domProps:{textContent:e._s(e.infos.develop.enterprise)}})])],1)],1)],1)],1)},o=[],a=s("1855"),l={data:function(){return{fullscreenLoading:!1,loading_txt:"请稍后……",loading_bgcolor:"rgba(0, 0, 0, 0.7)",active:"first",infos:a["a"].state.user.infos}},methods:{},mounted:function(){console.log(a["a"].state.user.infos)}},r=l,i=s("2877"),d=Object(i["a"])(r,n,o,!1,null,"ddcf2328",null);t["default"]=d.exports}}]);