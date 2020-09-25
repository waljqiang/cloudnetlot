<?php

//获取token
Route::group(["prefix" => "admin","middleware" => ["cloudnetlotadmin"],"namespace" => "Modules\Admin\Http\Controllers"],function(){
    Route::post("auth/token","AuthController@getToken")->name("login");//获取access_token
});
//认证相关
Route::group(["prefix" => "admin/auth","middleware" => ["cloudnetlotadmin","auth:cloudnetlotadmin"],"namespace" => "Modules\Admin\Http\Controllers"],function(){
	Route::post("token/refresh","AuthController@refreshToken");//刷新access_token
	Route::get("token/destroy","AuthController@destroyToken");//销毁token
});

//开发者平台相关
Route::group(["prefix" => "admin/develop","middleware" => ["cloudnetlotadmin","auth:cloudnetlotadmin"],"namespace" => "Modules\Admin\Http\Controllers"],function(){
	Route::post("list","DevelopController@getList");//获取开发者列表
	Route::post("info","DevelopController@getInfo")->middleware("hash-decode:uid")->middleware("hash-encode:uid");//获取开发者信息
	Route::post("approve","DevelopController@approve")->middleware("hash-decode:uid");//审批开发者申请
	Route::post("product/list","ProductController@getList");//获取产品列表
	Route::get("product/info","ProductController@getInfo")->middleware("hash-encode:uid");//获取产品详情
	Route::post("product/approve","ProductController@approve");//审核产品发布申请
});
