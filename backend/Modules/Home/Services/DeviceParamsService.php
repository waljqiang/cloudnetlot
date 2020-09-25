<?php
namespace Modules\Home\Services;

use App\Services\BaseService;
use App\Repositories\CacheRepository;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DeviceParamsService extends BaseService{
	private $cacheRepository;

	public function __construct(CacheRepository $cacheRepository){
		$this->cacheRepository = $cacheRepository;
	}

	public function parseParams($device,$paramsInfo){
		$parameters = json_decode($paramsInfo->params,true);
		$deviceType = config("device.typeinfo");
		$deviceTypeFlip = array_flip($deviceType);
		switch($paramsInfo->type){
			case $deviceType["system"]://系统信息
				$res[$deviceTypeFlip[$paramsInfo->type]] = $this->parseSystem($device,$parameters);
				break;
			case $deviceType["network"]://网络信息
			case $deviceType["user"]://终端用户信息
			case $deviceType["time_reboot"]://定时重启信息
			case $deviceType["upgrade"]://升级信息
				$res[$deviceTypeFlip[$paramsInfo->type]] = $parameters;
				break;
			case $deviceType["wifi"]://无线信息
				$res[$deviceTypeFlip[$paramsInfo->type]] = $this->parseWifi($device,$parameters);
				break;
			default:
				throw new \Exception("Unsupport the typeinfo of the device",config("exceptions.DEV_TYPEINFO_IN"));
				break;
		}
		return $res;
	}

	//处理系统信息
	private function parseSystem($device,$system){
		$deviceDynamic = $this->cacheRepository->getDeviceDynamic($device->dev_mac);
		return array_merge(array_intersect_key($system,[
			"name" => "",
			"type" => "",
			"mac" => "",
			"dev_ip" => "",
			"cpu" => "",
			"flash" => "",
			"ram" => "",
			"mode" => "",
			"version" => ""
		]),$deviceDynamic);
	}

	//处理网络信息
	private function parseWifi($device,$wifi){
		$total = $wifi["total"];
		$list = [];
		if($total > 0 && !empty($wifi["radios"])){
			foreach ($wifi["radios"] as $radio) {
				unset($radio["phymode"]);
				unset($radio["suport"]);
				unset($radio["client"]);
				foreach ($radio["vap"] as $k => $vap) {
					unset($radio["vap"][$k]["users"]);
				}
				$list[] = $radio;
			}
		}
		return ["total" => $total,"list" => $list];
	}

}