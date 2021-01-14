<?php
namespace Modules\Home\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Home\Http\Requests\DeviceRequest;
use Modules\Home\Services\DeviceService;

class DeviceController extends Controller{
	private $deviceService;

	public function __construct(DeviceService $deviceService){
		$this->deviceService = $deviceService;
	}

	//绑定设备
	public function bind(DeviceRequest $request){
		return $this->deviceService->bind($request->user(),$request->all());
	}

	//获取设备列表
	public function getList(DeviceRequest $request){
		return $this->deviceService->getList($request->user(),$request->all());
	}

	//设备统计
	public function stastics(DeviceRequest $request){
		return $this->deviceService->stastics($request->user(),$request->all());
	}

	//获取设备信息
	public function getInfos(DeviceRequest $request){
		return $this->deviceService->getInfos($request->user(),$request->all());
	}

	//批量重启设备
	public function restarts(DeviceRequest $request){
		return $this->deviceService->restarts($request->user(),$request->all());
	}

	//按小时统计用户下所有设备上在线用户总数
	public function staticsOnlineClients(DeviceRequest $request){
		return $this->deviceService->staticsOnlineClients($request->user(),$request->all());
	}

	//设备转组
	public function transGroup(DeviceRequest $request){
		return $this->deviceService->transGroup($request->user(),$request->all());
	}

}