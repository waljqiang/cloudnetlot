<?php
//云平台接口
Route::group(["middleware" => ["throttle:60,1","handle-response"],"namespace" => "Modules\Home\Http\Controllers"],function(){
	Route::post("getclient ","SystemController@getClient");//获取客户端信息
	Route::post("user/register","UserController@register")->middleware("hash-encode:uid");//注册用户
	Route::post("user/password/sendmail","UserController@sendPasswordMail");//发送找回密码邮件
	Route::post("user/password/checkmail","UserController@checkPasswordMail");//校验找回密码邮件链接的有效性
	Route::post("user/password/reset","UserController@resetPassword");//重设密码
});

//获取token
Route::group(["middleware" => ["cloudnetlot"],"namespace" => "Modules\Home\Http\Controllers"],function(){
    Route::post("auth/token","AuthController@getToken")->name("login");//获取access_token
});

//认证相关
Route::group(["prefix" => "auth","middleware" => ["cloudnetlot","auth:cloudnetlot"],"namespace" => "Modules\Home\Http\Controllers"],function(){
	Route::post("token/refresh","AuthController@refreshToken");//刷新access_token
	Route::get("token/destroy","AuthController@destroyToken");//销毁token
});

//用户相关
Route::group(["prefix" => "user","middleware" => ["cloudnetlot","auth:cloudnetlot"],"namespace" => "Modules\Home\Http\Controllers"],function(){
	Route::get("info","UserController@getInfo")->middleware("hash-encode:uid#admin_id#pid");//获取用户信息
	Route::post("save","UserController@save");//修改用户信息
	Route::post("password/save","UserController@savePassword");//修改用户密码
	Route::post("child/list","UserController@getChild")->middleware("hash-encode:list.*.uid#list.*.pid");//获取子账号列表
	Route::get("child/info","UserController@getChildInfo")->middleware("hash-decode:uid")->middleware("hash-encode:uid#pid#gids.*");//获取子账号信息
	Route::post("child/add","UserController@addChild")->middleware("hash-decode:gids")->middleware("hash-encode:uid");//创建子账号
	Route::post("child/save","UserController@saveChild")->middleware("hash-decode:uid#gids");//修改子账号信息
	Route::post("child/resetspassword","UserController@resetPasswordForChild")->middleware("hash-decode:uids");//批量重置子账号密码
	Route::post("child/deletes","UserController@deleteChilds")->middleware("hash-decode:uids");//批量删除子账号
	Route::get("child/count","UserController@countChilds");//子账号按状态数据统计
});


//工作组相关
Route::group(["prefix" => "workgroup","middleware" => ["cloudnetlot","auth:cloudnetlot"],"namespace" => "Modules\Home\Http\Controllers"],function(){
	Route::post("upload/config","WorkgroupController@uploadConfig");//上传配置文件
	Route::post("add","WorkgroupController@addWorkgroup")->middleware("hash-decode:gid")->middleware("hash-encode:gid");//添加工作组
	Route::get("info","WorkgroupController@getInfo")->middleware("hash-decode:gid")->middleware("hash-encode:gid#user_id");//获取工作组信息
	Route::get("all","WorkgroupController@getAll");//获取所有工作组
	Route::post("save","WorkgroupController@saveWorkgroup")->middleware("hash-decode:gid");//修改工作组信息
	Route::get("delete","WorkgroupController@deleteWorkgroup")->middleware("hash-decode:gid");//删除工作组
});
Route::group(["prefix" => "workgroup","middleware" => ["auth:cloudnetlot"],"namespace" => "Modules\Home\Http\Controllers"],function(){
	Route::get("download/config","WorkgroupController@downloadConfig");//配置模板下载
});

//操作日志相关
Route::group(["prefix" => "oplog","middleware" => ["cloudnetlot","auth:cloudnetlot"],"namespace" => "Modules\Home\Http\Controllers"],function(){
	Route::get("statistics","OplogController@statisticsNotices");//操作日志统计
	Route::post("list","OplogController@getList")->middleware("hash-encode:list.*.id#list.*.user_id");//获取操作日志列表
	Route::get("info","OplogController@getInfo")->middleware("hash-decode:id")->middleware("hash-encode:id#user_id");//获取操作日志详情
	Route::post("readed","OplogController@readedMessage")->middleware("hash-decode:ids");//置消息为已读
});

//设备相关
Route::group(["prefix" => "device","middleware" => ["cloudnetlot","auth:cloudnetlot"],"namespace" => "Modules\Home\Http\Controllers"],function(){
	Route::post("bind","DeviceController@bind")->middleware("hash-decode:gid");//绑定设备
	Route::post("list","DeviceController@getList")->middleware("hash-decode:gid")->middleware("hash-encode:list.*.user_id#list.*.group_id");//获取设备列表
	Route::get("statistics","DeviceController@statistics")->middleware("hash-decode:gid");//设备统计
	Route::post("infos","DeviceController@getInfos");//获取设备信息
	Route::post("restart","DeviceController@restart");//重启设备
	Route::post("timerestart","DeviceController@timeRestart");//定时重启设备
	Route::post("restarts","DeviceController@restarts");//批量重启设备
	Route::post("statistics/clients/onlines","DeviceController@statisticsOnlineClients");
	Route::post("transgroup","DeviceController@transGroup")->middleware("hash-decode:gid");//设备转组
	Route::get("list/export","DeviceController@exportLists")->middleware("hash-decode:gid");//设备列表导出
	Route::post("wifi/info","DeviceController@getWifi");//获取单个无线信息
	Route::post("setwifi","DeviceController@setWifi");//设置无线
	Route::post("setwifis","DeviceController@setWifis");//批量设置无线
	Route::post("getwifioptions","DeviceController@getWifiOptions");//获取无线支持参数
	Route::post("reports","DeviceController@reports");//上报信息
	Route::post("deletes","DeviceController@deletes");//批量删除设备
	Route::post("setname","DeviceController@setName");//修改设备名称
	Route::get("types","DeviceController@getTypes")->middleware("hash-decode:gid");//获取所有设备型号
});
//拓扑图
Route::group(["prefix" => "topgraphy","middleware" => ["cloudnetlot","auth:cloudnetlot"],"namespace" => "Modules\Home\Http\Controllers"],function(){
	Route::get("init","TopgraphyController@init")->middleware("hash-decode:gid");//初始化拓扑图数据
	Route::get("info","TopgraphyController@getInfo")->middleware("hash-decode:gid");//获取拓扑图
	Route::post("rebuild","TopgraphyController@rebuild")->middleware("hash-decode:gid");//从新生成拓扑图
	Route::get("virturedevice/mac","TopgraphyController@generateVirtureDeviceMac");//生成虚拟设备MAC
	Route::post("save","TopgraphyController@save")->middleware("hash-decode:gid");//保存拓扑图
	Route::get("dynamics","TopgraphyController@getDynamics")->middleware("hash-decode:gid")->middleware("hash-encode:*.uid");//获取拓扑图动态信息
	Route::get("unedits","TopgraphyController@getUnedits")->middleware("hash-decode:gid");//获取用户未编辑的设备MAC
});

//产品相关
Route::group(["prefix" => "product","middleware" => ["cloudnetlot","auth:cloudnetlot"],"namespace" => "Modules\Home\Http\Controllers"],function(){
	Route::get("statisticswithdevices","ProductController@statisticsWithDevices");//统计各产品下的设备数
});

//升级
Route::group(["prefix" => "upgrade","middleware" => ["cloudnetlot","auth:cloudnetlot"],"namespace" => "Modules\Home\Http\Controllers"],function(){
	Route::post("upload","UpgradeController@upload");//上传升级包
	Route::post("localpackages","UpgradeController@getLocalPackages");//获取本地升级包列表
	Route::post("deletelocalpackages","UpgradeController@deleteLocalPackages");//删除本地升级包
	Route::post("upgrades","UpgradeController@upgrades");//升级
	Route::post("getorders","UpgradeController@getOrders")->middleware("hash-encode:*.user_id");//获取升级记录单列表
});
