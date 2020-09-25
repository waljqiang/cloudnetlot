<?php
namespace Modules\Home\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Home\Http\Requests\WorkgroupRequest;
use Modules\Home\Services\WorkgroupService;

class WorkgroupController extends Controller{
	//获取工作组信息
	public function getInfo(WorkgroupRequest $request,WorkgroupService $workgroupService){
		return $workgroupService->getInfo($request->user(),$request->all());
	}
	//获取工作组列表
	public function getAll(WorkgroupRequest $request,WorkgroupService $workgroupService){
		return $workgroupService->getAll($request->user(),$request->all());
	}

	//上传配置文件
	public function uploadConfig(WorkgroupRequest $request,WorkgroupService $workgroupService){
		return $workgroupService->uploadConfig($request->user(),$request->all());
	}

	//添加工作组
	public function addWorkgroup(WorkgroupRequest $request,WorkgroupService $workgroupService){
		return $workgroupService->addWorkgroup($request->user(),$request->all());
	}

	//修改工作组
	public function saveWorkgroup(WorkgroupRequest $request,WorkgroupService $workgroupService){
		return $workgroupService->saveWorkgroup($request->user(),$request->all());
	}

	//删除工作组
	public function deleteWorkgroup(WorkgroupRequest $request,WorkgroupService $workgroupService){
		return $workgroupService->deleteWorkgroup($request->user(),$request->all());
	}
}