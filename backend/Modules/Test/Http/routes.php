<?php
Route::group(['middleware' => ['cloudnetlot'],'prefix' => 'test','namespace' => 'Modules\Test\Http\Controllers'],function(){
    Route::post('testmac',[
    	'uses' => 'TestController@testMac',
    	'middleware' => ['handle-mac:before,mac,macs','handle-mac:after,dev_mac,macs']
    ]);
    Route::get('testlimit','TestController@testLimit')->middleware('limit:100,300');
    Route::get('testmqtt','TestController@testMqtt');
    Route::get('testmail','TestController@testMail');
    Route::get('testyunlot','TestController@testyunlot');
    Route::get('testoplogcache','TestController@testOplogCache');
    Route::get('testlog','TestController@testLog');
});
