<?php
namespace Modules\Home\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Home\Http\Requests\DeviceRequest;
use Modules\Home\Services\DeviceService;
use Modules\Home\Services\DeviceWifiService;

class DeviceController extends Controller{
	protected $deviceService;

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
	public function statistics(DeviceRequest $request){
		return $this->deviceService->statistics($request->user(),$request->all());
	}

	//获取设备信息
	public function getInfos(DeviceRequest $request){
		return $this->deviceService->getInfos($request->user(),$request->all());
	}

	//重启设备
	public function restart(DeviceRequest $request){
		return $this->deviceService->restart($request->user(),$request->all());
	}

	//定时重启设备
	public function timeRestart(DeviceRequest $request){
		return $this->deviceService->timeRestart($request->user(),$request->all());
	}

	//批量重启设备
	public function restarts(DeviceRequest $request){
		return $this->deviceService->restarts($request->user(),$request->all());
	}

	//按小时统计用户下所有设备上在线用户总数
	public function statisticsOnlineClients(DeviceRequest $request){
		return $this->deviceService->statisticsOnlineClients($request->user(),$request->all());
	}

	//设备转组
	public function transGroup(DeviceRequest $request){
		return $this->deviceService->transGroup($request->user(),$request->all());
	}

	//设备导出
	public function exportLists(Request $request){
		return $this->deviceService->exportLists($request->user(),$request->all());
	}

	//获取单个无线信息
	public function getWifi(DeviceRequest $request,DeviceWifiService $deviceWifiService){
		return $deviceWifiService->getWifi($request->user(),$request->all());
	}

	//设置无线
	public function setWifi(DeviceRequest $request,DeviceWifiService $deviceWifiService){
		return $deviceWifiService->setWifi($request->user(),$request->all());
	}

	//批量设置无线
	public function setWifis(DeviceRequest $request,DeviceWifiService $deviceWifiService){
		return $deviceWifiService->setWifis($request->user(),$request->all());
	}

	//获取无线支持参数
	public function getWifiOptions(DeviceRequest $request,DeviceWifiService $deviceWifiService){
		return $deviceWifiService->getWifiOptions($request->user(),$request->all());
	}

	//上报信息
	public function reports(DeviceRequest $request){
		return $this->deviceService->reports($request->user(),$request->all());
	}

	//批量删除设备
	public function deletes(DeviceRequest $request){
		return $this->deviceService->deletes($request->user(),$request->all());
	}

	//设置设备名称
	public function setName(DeviceRequest $request){
		return $this->deviceService->setName($request->user(),$request->all());
	}

	//获取所有设备型号
	public function getTypes(DeviceRequest $request){
		return $this->deviceService->getTypes($request->user(),$request->all());
	}

}