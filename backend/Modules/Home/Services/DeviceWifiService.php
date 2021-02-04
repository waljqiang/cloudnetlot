<?php
namespace Modules\Home\Services;

use Modules\Home\Services\DeviceBaseService;
use App\Repositories\CacheRepository;
use Modules\Home\Services\DeviceParamsService;
use Modules\Home\Repositories\DeviceRepository;
use Modules\Home\Repositories\CommandRepository;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DeviceWifiService extends DeviceBaseService{
	protected $deviceParamsService;
	protected $deviceRepository;
	protected $cacheRepository;
	protected $commandRepository;

	public function __construct(DeviceParamsService $deviceParamsService,DeviceRepository $deviceRepository,CacheRepository $cacheRepository,CommandRepository $commandRepository){
		$this->deviceParamsService = $deviceParamsService;
		$this->deviceRepository = $deviceRepository;
		$this->cacheRepository = $cacheRepository;
		$this->commandRepository = $commandRepository;
	}

	//获取单个无线信息
	public function getWifi($user,$params){
		$mac = array_get($params,"mac");
		$radio = array_get($params,"radio");
		//校验设备
		$device = $this->getDevices($user,[
			["dev_mac",$mac]
		],[
			"params" => function($query){
				$query->where("type",config("device.typeinfo.wifi"));
			}
		],["*"],true);
		$this->checkDevices($mac,$device);
		if($device->status != config("device.status.online")){
			return [];
		}
		$wifi = $device->wifi;
		if(!isset($wifi["radios"][$radio])){
			throw new \Exception("The radio[" . $radio . "] of the device is not exists",config("exceptions.WIFI_RADIO_NO"));
		}
		$wifiRadio = $wifi["radios"][$radio];
		foreach ($wifiRadio["vap"] as $k => $vap) {
			unset($wifiRadio["vap"][$k]["users"]);
		}
		unset($wifiRadio["support"]);
		unset($wifiRadio["client"]);
		$wifiRadio["timer"] = $wifi["timer"];
		return $wifiRadio;
	}

	//设置无线
	public function setWifi($user,$params){
		$mac = array_get($params,"mac");
		$radio = array_get($params,"radio");
		$vaps = array_get($params,"vaps");
		$options = array_get($params,"options");
		//校验账号权限
		$this->checkUserPermission($user,config("public.user.level.child_admin"));
		//校验设备
		$device = $this->getDevices($user,[
			["dev_mac",$mac]
		],[
			"params" => function($query){
				$query->where("type",config("device.typeinfo.wifi"));
			}
		],["*"],true);
		$this->checkDevices($mac,$device,config("device.status.online"));
		$wifi = $device->wifi;
		$this->checkWifi($wifi,$radio,$vaps);
		$time = Carbon::now()->timestamp;
		list($newData,$commID,$command) = $this->deviceParamsService->parseOptions($wifi,$radio,$vaps,$options,$time);
		//发送命令
		$topic = getTopic($device->prtid,$device->cltid);
		if(!sendToMqtt([$topic],$command)){
			throw new \Exception("Mqtt publish failure",config("exceptions.MQTT_PUBLISH_ERROR"));
		}
		//记录命令
		$this->afterOperater([
			[
				"user_id" => $user->id,
				"dev_mac" => $device->dev_mac,
				"dev_ip" => $device->dev_ip,
				"dev_name" => $device->name,
				"dev_version" => $device->version,
				"dev_type" => $device->type,
				"comm_id" => $commID,
				"content" => $command,
				"type" => config("device.typeinfo.wifi"),
				"status" => 2,
				"created_at" => $time,
				"updated_at" => $time
			]
		]);
		return [];
	}

	//批量设置无线
	public function setWifis($user,$params){
		$macs = array_get($params,"macs");
		$radio = array_get($params,"radio");
		$vaps = array_get($params,"vaps");
		$options = array_get($params,"options");
		//校验账号权限
		$this->checkUserPermission($user,config("public.user.level.child_admin"));
		//校验设备
		$devices = $this->getDevices($user,[
			[function($query)use($macs){
				$query->whereIn("dev_mac",$macs);
			}]
		],[
			"params" => function($query){
				$query->where("type",config("device.typeinfo.wifi"));
			}
		]);
		$this->checkDevices($macs,$devices,config("device.status.online"));
		$time = Carbon::now()->timestamp;
		list($commID,$command) = $this->deviceParamsService->parseOptions([],$radio,$vaps,$options,$time,2);
		$topics = [];
		$datas = [];
		$records = [];
		$devices->each(function($device)use($user,$radio,$vaps,$options,$time,$commID,$command,&$datas,&$topics,&$records){
			$wifi = $device->wifi;
			try{
				$this->checkWifi($wifi,$radio,$vaps);
				list($newData) = $this->deviceParamsService->parseOptions($wifi,$radio,$vaps,$options,$time,1);
				$datas[$device->dev_mac] = $newData;
				$topics[] = getTopic($device->prtid,$device->cltid);
				$records[] = [
					"user_id" => $user->id,
					"dev_mac" => $device->dev_mac,
					"dev_ip" => $device->dev_ip,
					"dev_name" => $device->name,
					"dev_version" => $device->version,
					"dev_type" => $device->type,
					"comm_id" => $commID,
					"content" => $command,
					"type" => config("device.typeinfo.wifi"),
					"status" => 2,
					"created_at" => $time,
					"updated_at" => $time
				];
			}catch(\Exception $e){
				$topics[] = getTopic($device->prtid,$device->cltid);
			}
		});
		if(!sendToMqtt($topics,$command)){
			throw new \Exception("Mqtt publish failure",config("exceptions.MQTT_PUBLISH_ERROR"));
		}
		//记录命令
		$this->afterOperater($records);
		return [];
	}

	//获取无线支持参数
	public function getWifiOptions($user,$params){
		$mac = array_get($params,"mac");
		$radio = array_get($params,"radio");
		//校验设备
		$device = $this->getDevices($user,[
			["dev_mac",$mac]
		],[
			"params" => function($query){
				$query->where("type",config("device.typeinfo.wifi"));
			}
		],["*"],true);
		$this->checkDevices($mac,$device);
		$wifi = $device->wifi;
		if(!isset($wifi["radios"][$radio])){
			throw new \Exception("The radio[" . $radio . "] of the device is not exists",config("exceptions.WIFI_RADIO_NO"));
		}
		$support = $wifi["radios"][$radio]["support"];
		$supportChannel = isset($support["country_code"]) ? $support["country_code"] : config("device.wifi.support.country_code");
		$channel = [];
		$encode = isset($support["encode"]) ? $support["encode"] : config("device.wifi.support.encode");
		$phymode = isset($support["phymode"]) ? $support["phymode"] : config("device.wifi.support.phymode");
		$power = isset($support["power"]) ? $support["power"] : config("device.wifi.support.power");
		foreach($supportChannel as $value){
			if($value["code"] == $wifi["radios"][$radio]["country_code"]){
				$channel = $value["channel"];
			}
		}
		return compact("channel","encode","phymode","power");
	}

	private function checkWifi($wifi,$radio,$vaps){
		if(!isset($wifi["radios"][$radio])){
			throw new \Exception("The radio[" . $radio . "] of the device is not exists",config("exceptions.WIFI_RADIO_NO"));
		}
		foreach ($vaps as $vap) {
			if(!isset($wifi["radios"][$radio]["vap"][$vap])){
				throw new \Exception("The vap[" . $vap . "] of the radio is not exists",config("exceptions.RADIO_VAP_NO"));
			}
		}
		$support = $wifi["radios"][$radio]["support"];
		if(isset($options["channel"])){
			$supportChannel = isset($support["country_code"]) ? $support["country_code"] : config("device.wifi.support.country_code");
			foreach($supportChannel as $value){
				if($value["code"] == $wifi["radios"][$radio]["country_code"]){
					if(!in_array($options["channel"],$value["channel"])){
						throw new \Exception("Unsupport channel",config("exceptions.UN_SUPPORT_CHANNEL"));
					}
				}
			}
		}
		$supportEncode = isset($support["encode"]) ? $support["encode"] : config("device.wifi.support.encode");
		if(isset($options["encode"]) && !in_array($options["encode"],$support["encode"])){
			throw new \Exception("Unsupport encode",config("exceptions.UN_SUPPORT_ENCODE"));
		}
		$supportPhymode = isset($support["phymode"]) ? $support["phymode"] : config("device.wifi.support.phymode");
		if(isset($options["phymode"]) && !in_array($options["phymode"],$supportPhymode)){
			throw new \Exception("Unsupport phymode",config("exceptions.UN_SUPPORT_PHYMODE"));
		}
	}

}