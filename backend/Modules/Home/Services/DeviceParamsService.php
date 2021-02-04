<?php
namespace Modules\Home\Services;

use Modules\Home\Services\DeviceBaseService;
use App\Repositories\CacheRepository;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DeviceParamsService extends DeviceBaseService{
	protected $cacheRepository;

	public function __construct(CacheRepository $cacheRepository){
		$this->cacheRepository = $cacheRepository;
	}

	public function parseParams($device,$paramsInfo){
		$parameters = json_decode($paramsInfo->params,true);
		$deviceType = config("device.typeinfo");
		$deviceTypeFlip = array_flip($deviceType);
		if($device->status == config("device.status.online")){
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
		}else{
			switch($paramsInfo->type){
				case $deviceType["system"]://系统信息
					$res[$deviceTypeFlip[$paramsInfo->type]] = $this->parseSystem($device,$parameters);
					break;
				case $deviceType["network"]://网络信息
				case $deviceType["user"]://终端用户信息
				case $deviceType["time_reboot"]://定时重启信息
				case $deviceType["upgrade"]://升级信息
				case $deviceType["wifi"]://无线信息
					$res[$deviceTypeFlip[$paramsInfo->type]] = [];
					break;
				default:
					throw new \Exception("Unsupport the typeinfo of the device",config("exceptions.DEV_TYPEINFO_IN"));
					break;
			}
		}
		return $res;
	}

	/**
	 * 功能描述
	 *
	 * @param  [type]  $wifi    [description]
	 * @param  [type]  $radio   [description]
	 * @param  [type]  $vaps    [description]
	 * @param  [type]  $options [description]
	 * @param  [type]  $time    [description]
	 * @param  integer $result  1:只返回newData2：只返回命令3：全部返回
	 * @param  string  $commID  [description]
	 * @return [type]           [description]
	 */
	public function parseOptions($wifi,$radio,$vaps,$options,$time,$result=3,$commID = ""){
		$baseOptions = array_intersect_key($options,[
			"ssid" => "",
        	"enable" => "",
        	"encode" => "",
        	"password" => "",
        	"vlan_id" => "",
        	"ssid_hide" => ""
		]);
		$advanceOptions = array_intersect_key($options,[
	        "channel" => "",
	        "power" => "",
	        "phymode" => "",
	        "user_isolate" => "",
	        "frag_threshold" => "",
	        "rts_threshold" => "",
	        "beacon_interval" => "",
	        "coveragethreshold" => "",
	        "shortgi" => "",
	        "max_sta" => ""
		]);
		$timerOptions = [];
		if(isset($options["timer_enable"])){
			$timerOptions["enable"] = $options["timer_enable"];
			if($options["timer_enable"] == 1){
				$timerOptions["start"] = $options["timer_start"];
				$timerOptions["end"] = $options["end"];
			}
		}
		$wifiComand = ["type" => "wifi","radio" => [$radio]];

		if($result == 1){
			if(!empty($baseOptions)){
				foreach ($vaps as $vap) {
					$wifi["radios"][$radio]["vap"][$vap] = array_merge($wifi["radios"][$radio]["vap"][$vap],$baseOptions);
				}
			}
			if(!empty($advanceOptions)){
				$wifi["radios"][$radio] = array_merge($wifi["radios"][$radio],$advanceOptions);
			}
			if(!empty($timerOptions)){
				$wifi["timer"] = array_merge($wifi["timer"],$timerOptions);
			}
			return [$wifi];
		}elseif($result == 2){
			if(!empty($baseOptions)){
				foreach ($vaps as $vap) {
					$wifiComand["vap"] = array_merge(["vap" => $vaps],$baseOptions);
				}
			}
			if(!empty($advanceOptions)){
				$wifiComand = array_merge($wifiComand,$advanceOptions);
			}
			if(!empty($timerOptions)){
				$wifiComand["timer"] = $timerOptions;
			}
			$commID = empty($commID) ? getCommID(config("yunlot.lottype.down"),config("device.typeinfo.wifi"),$time) : $commID;
			$command = getCommand(config("device.typeinfo.time_reboot"),["command" => $wifiComand],$time,$commID);
			return [$commID,$command];
		}else{
			if(!empty($baseOptions)){
				foreach ($vaps as $vap) {
					$wifi["radios"][$radio]["vap"][$vap] = array_merge($wifi["radios"][$radio]["vap"][$vap],$baseOptions);
					$wifiComand["vap"] = array_merge(["vap" => $vaps],$baseOptions);
				}
			}
			if(!empty($advanceOptions)){
				$wifi["radios"][$radio] = array_merge($wifi["radios"][$radio],$advanceOptions);
				$wifiComand = array_merge($wifiComand,$advanceOptions);
			}
			if(!empty($timerOptions)){
				$wifi["timer"] = array_merge($wifi["timer"],$timerOptions);
				$wifiComand["timer"] = $timerOptions;
			}
			$commID = empty($commID) ? getCommID(config("yunlot.lottype.down"),config("device.typeinfo.wifi"),$time) : $commID;
			$command = getCommand(config("device.typeinfo.time_reboot"),["command" => $wifiComand],$time,$commID);
			return [$wifi,$commID,$command];
		}
	}

	//处理系统信息
	private function parseSystem($device,$system){
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
		]),["cpu_use" => $device->cpu_use,"memory_use" => $device->memory_use,"runtime" => $device->runtime,"status" => $device->status]);
	}

	//处理无线信息
	private function parseWifi($device,$wifi){
		$total = $wifi["total"];
		$list = [];
		if($total > 0 && !empty($wifi["radios"])){
			foreach ($wifi["radios"] as $radio) {
				$radio["timer"] = $wifi["timer"];
				unset($radio["support"]);
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