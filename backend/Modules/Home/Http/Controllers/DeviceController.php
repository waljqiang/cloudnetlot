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

}