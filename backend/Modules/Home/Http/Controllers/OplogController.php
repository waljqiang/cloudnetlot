<?php
namespace Modules\Home\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Home\Services\OplogService;
use Modules\Home\Http\Requests\OplogRequest;

class OplogController extends Controller{
	//获取操作日志列表
	public function getList(OplogRequest $request,OplogService $oplogService){
		return $oplogService->getList($request->user(),$request->all());
	}
	//置操作日志为已读
	public function readedMessage(OplogRequest $request,OplogService $oplogService){
		return $oplogService->readedMessage($request->user(),$request->all());
	}
}