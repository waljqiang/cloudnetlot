<?php

//获取token
Route::group(["prefix" => "develop","middleware" => ["cloudnetlotdevelop"],"namespace" => "Modules\Develop\Http\Controllers"],function(){
    Route::post("auth/token","AuthController@getToken")->name("login");//获取access_token
});

//认证相关
Route::group(["prefix" => "develop/auth","middleware" => ["cloudnetlotdevelop","auth:cloudnetlotdevelop"],"namespace" => "Modules\Develop\Http\Controllers"],function(){
	Route::post("token/refresh","AuthController@refreshToken");//刷新access_token
	Route::get("token/destroy","AuthController@destroyToken");//销毁token
});

//用户相关
Route::group(["prefix" => "develop/user","middleware" => ["cloudnetlotdevelop","auth:cloudnetlotdevelop"],"namespace" => "Modules\Develop\Http\Controllers"],function(){
	Route::get("info","UserController@getInfo")->middleware("hash-encode:uid#admin_id#pid");//获取用户信息
	Route::post("develop","UserController@develop");//申请成为开发者
});
//产品相关
Route::group(["prefix" => "develop/product","middleware" => ["cloudnetlotdevelop","auth:cloudnetlotdevelop"],"namespace" => "Modules\Develop\Http\Controllers"],function(){
	Route::post("register","ProductController@register");//注册产品
	Route::post("list","ProductController@getList");//获取产品列表
	Route::get("info","ProductController@getInfo")->middleware("hash-encode:uid");//获取产品详情
	Route::post("save","ProductController@save");//编辑产品
	Route::post("delete","ProductController@delete");//删除产品
	Route::post("publish","ProductController@publish");//发布产品
});
