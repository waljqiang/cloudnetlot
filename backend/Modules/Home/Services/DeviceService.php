<?php
namespace Modules\Home\Services;

use App\Services\BaseService;
use App\Repositories\CacheRepository;
use Modules\Home\Services\DeviceParamsService;
use Modules\Home\Repositories\DeviceRepository;
use Modules\Home\Repositories\CommandRepository;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Utils\HashCode;

class DeviceService extends BaseService{
	private $deviceParamsService;
	private $deviceRepository;
	private $cacheRepository;
	private $commandRepository;
	private $deviceClientsNumsRepository;

	public function __construct(DeviceParamsService $deviceParamsService,DeviceRepository $deviceRepository,CacheRepository $cacheRepository,CommandRepository $commandRepository){
		$this->deviceParamsService = $deviceParamsService;
		$this->deviceRepository = $deviceRepository;
		$this->cacheRepository = $cacheRepository;
		$this->commandRepository = $commandRepository;
	}

	public function bind($user,$params){
		$prtid = array_get($params,"prtid");
		$mac = array_get($params,"mac");
		$devUsername = array_get($params,"username");
		$devPassword = array_get($params,"password");
		$gid = array_get($params,"gid","");
		$time = Carbon::now()->timestamp;
		$prtid = array_get($params,"prtid");
		$userWorkgroup = $user->workgroups;
		if(!empty($gid)){
			if(!$userWorkgroup->pluck("id")->contains($gid)){
				throw new \Exception("This workgoup is not exists",config("exceptions.USER_NO_WORKGROUP"));
			}
		}else{
			$gid = $userWorkgroup->filter(function($value){
				return $value->pid == 0;
			})->get(0)->id;
		}
		//获取注册信息
		if(!($registerInfo = $this->cacheRepository->getRegister($prtid,$mac))){
			$registerInfo = $this->deviceRepository->getRegister($prtid,$mac);
			$this->cacheRepository->setRegister($prtid,$mac,$registerInfo,config("public.cache.registerttl"));
		}

		if(empty($registerInfo)){
			throw new \Exception("The product is not exists",config("exceptions.PRT_NO"));
		}
		
		if($registerInfo["aud_status"] != 4 && $registerInfo["developUid"] != $user->id){//未发布的产品只能绑定开发者账号
			throw new \Exception("The unpublish product just only bind to developer",config("exceptions.PRT_STATUS_NO_ALLOW"));
		}

		if(empty($registerInfo["cltid"])){//设备还未与云平台建立连接
			throw new \Exception("The device of the product is not connect to cloudnetlot",config("exceptions.DEV_NO_CONNECT"));
		}

		if(!empty($registerInfo["bindUid"]) && $registerInfo["bindUid"] != $user->id){//绑定其他用户时，需要先解绑
			throw new \Exception("The device is binded to another user",config("exceptions.DEV_BINDED"));
		}

		//发送绑定命令
		DB::beginTransaction();
		if(empty($registerInfo["bind"])){
			$bindCode = getBindCode($user->id,$mac,$gid);
			$registerInfo["bind"] = $bindCode;
			$this->cacheRepository->setRegister($prtid,$mac,$registerInfo,config("public.cache.registerttl"));
			$rs1 = $this->deviceRepository->save([
				"bind" => $bindCode,
				"group_id" => $gid,
				"updated_at" => $time
			],["dev_mac" => $mac]);
		}else{
			$bindCode = $registerInfo["bind"];
			$rs1 = true;
		}
		$topic = getTopic($prtid,$registerInfo["cltid"]);
		$commID = getCommID(config("yunlot.lottype.down"),config("device.typeinfo.bind"),$time);
		$arr =[$devUsername,$devPassword,$mac,$time];
		$token = sort($arr);
		$token = sha1($token);
		$body = [
			"comm_id" => $commID,
			"command" => [
				"type" => "bind",
				"bind" => $bindCode,
				"token" =>  $token
			]
		];
		$command = getCommand(config("device.typeinfo.bind"),$body,$time);	
		$rs2 = $this->afterOperater(
			[
				[
					"user_id" => $user->id,
					"dev_mac" => $mac,
					"comm_id" => $commID,
					"content" => $command,
					"type" => config("device.typeinfo.bind"),
					"status" => 2,
					"created_at" => $time,
					"updated_at" => $time
				]
			]
		);

		if($rs1 && $rs2){
			$rs3 = sendToMqtt([$topic],$command);
			if($rs3){
				DB::commit();
				return ["bind" => $bindCode];
			}else{
				DB::rollback();
				throw new \Exception("Failure",config("exceptions.MQTT_PUBLISH_ERROR"));
			}
		}else{
			DB::rollback();
			throw new \Exception("Failure",config("exceptions.MYSQL_EXEC_ERROR"));
		}
	}

	//获取设备列表
	public function getList($user,$params){
		$gid = array_get($params,"gid");
		$status = array_get($params,"status",NULL);
		$sortKey = array_get($params,"sortkey","join_time");
		$sort = array_get($params,"sort","desc");
		$pageIndex = array_get($params,"pageIndex",1);
		$pageOffset = array_get($params,"pageOffset",10);
		$keyword = array_get($params,"search","");
		if(!$user->workgroups->pluck("id")->contains($gid)){
			throw new \Exception("The workgroup is not exists",config("exceptions.USER_NO_WORKGROUP"));
		}
		$condition = [
			["group_id",$gid],
			["pid",""]
		];
		if(!empty($keyword)){
			$keyword = "%" . $keyword . "%";
			array_push($condition,[function($query) use ($keyword){
				$query->where([
					["name","like",$keyword],
					["dev_mac","like",$keyword,"or"],
					["type","like",$keyword,"or"]
				]);
			}]);
		}
		$columns = ["user_id","dev_mac","pid","dev_ip","net_ip","name","prt_type","prt_size","type","mode","version","up_time","latitude","longitude","chip","group_id","join_time","created_at"];

		$allDevice = $this->getDevices($user,$condition,[],$columns,$unique = false);

		$devices = $allDevice->map(function($device) use ($user){
			$device->join_time = convUnixToZoneGm($device->join_time,$user->timeZone,$user->isSummerTime);
			$device->created_at = convUnixToZoneGm($device->created_at,$user->timeZone,$user->isSummerTime);
			return $device;
		});

		if(!is_null($status)){
			$devices = $devices->filter(function($device) use ($status){
				return $device->status == $status;
			});
		}
		
		$total = $devices->count();
		$list = "desc" == $sort ? $devices->sortByDesc($sortKey)->values()->forpage($pageIndex,$pageOffset) : $devices->sortBy($sortKey)->values()->forpage($pageIndex,$pageOffset);
		return ["total" => $total,"list" => $list];
	}

	//设备统计
	public function stastics($user,$params){
		$gid = array_get($params,"gid","");
		$keyword = array_get($params,"search","");
		$condition = [
			["pid",""]
		];

		if(!empty($gid)){
			array_push($condition,["group_id",$gid]);
		}

		if(!empty($keyword)){
			$keyword = "%" . $keyword . "%";
			array_push($condition,[function($query) use ($keyword){
				$query->where([
					["name","like",$keyword],
					["dev_mac","like",$keyword,"or"],
					["type","like",$keyword,"or"]
				]);
			}]);
		}

		$devices = $this->getDevices($user,$condition);

		$online = $offline = 0;
		$devices->each(function($device) use (&$online,&$offline){
			if($device->status == config("device.status.online")){
				$online += 1;
			}else{
				$offline += 1;
			}
		});
		$res = [
			"all" => $online + $offline,
			"online" => $online,
			"offline" => $offline
		];
		unset($all);
		unset($online);
		unset($offline);
		return $res; 
	}

	//获取设备信息
	public function getInfos($user,$params){
		$mac = array_get($params,"mac");
		$type = array_get($params,"type");
		$device = $this->getDevices($user,[
			["dev_mac",$mac]
		],[
			"params" => function($query) use ($type){
				$query->whereIn("type",$type);
		}],['*'],true);
		if(empty($device)){
			throw new \Exception("The device is not exists",config("exceptions.DEVICE_NO"));
		}
		return $device->params->mapWithKeys(function($infos) use ($device){
			return $this->deviceParamsService->parseParams($device,$infos);
		});
	}

	//批量重启设备
	public function restarts($user,$params){
		$macs = array_get($params,"macs");
		//子账号权限校验
		if(!$user->is_primary && $user->level != config("public.user.level.child_admin")){
			throw new \Exception("No permission",config("exceptions.NO_PERMISSTION"));
		}
		$devices = $this->getDevices($user,[[function($query) use ($macs){
			$query->whereIn("dev_mac",$macs);
		}]]);

		//校验设备
		$this->checkDevices($macs,$devices,config("device.status.online"));
		//发送重启命令
		$time = Carbon::now()->timestamp;
		$commID = getCommID(config("yunlot.lottype.down"),config("device.typeinfo.time_reboot"),$time);
		$command = getCommand(config("device.typeinfo.time_reboot"),["command" => ["type" => "reboot"]],$time,$commID);
		$records = [];
		$topics = $devices->map(function($device) use (&$records,$user,$commID,$command,$time){
			$records[] = [
				"user_id" => $user->id,
				"dev_mac" => $device->dev_mac,
				"dev_ip" => $device->dev_ip,
				"dev_name" => $device->name,
				"dev_version" => $device->version,
				"dev_type" => $device->type,
				"comm_id" => $commID,
				"content" => $command,
				"type" => config("device.typeinfo.time_reboot"),
				"status" => 2,
				"created_at" => $time,
				"updated_at" => $time
			];
			return getTopic($device->prtid,$device->cltid);
		})->toArray();
		if(!sendToMqtt($topics,$command)){
			throw new \Exception($e->getMessage(),config("exceptions.MQTT_PUBLISH_ERROR"));
		}
		//记录命令
		$this->cacheRepository->setDevicesDynamic($macs,["status" => config("device.status.offline")]);
		$this->afterOperater($records);
		return [];
	}

	//按小时统计用户下所有设备上在线用户总数
	public function staticsOnlineClients($user,$params){
		$start = array_get($params,"start");
		$end = array_get($params,"end");
		$startLocal = Carbon::createFromFormat("Y-m-d H:i:s",$start);
		$endLocal = Carbon::createFromFormat("Y-m-d H:i:s",$end);
		$startUtc = $startLocal->copy()->subMinutes($user->timeZone * 60)->subMinutes($user->summerTime * 60);
		$endUtc = $endLocal->copy()->subMinutes($user->timeZone * 60)->subMinutes($user->summerTime * 60);
		$devices = $this->getDevices($user,[],["clientsStaticsHours" => function($query) use ($startUtc,$endUtc){
			$query->whereBetween("hours",[$startUtc->toDateTimeString(),$endUtc->toDateTimeString()])->orderBy("created_at","ASC");
		}]);

		$keys = [];
		//生成日期数组
		for($date = $startLocal->copy()->startOfHour();$date->lte($endLocal->copy()->endOfHour());$date->addHours(1)){
			$keys[$date->toDateString()][$date->toDateTimeString()] = 0;
		}

		$devices->each(function($device) use ($user,&$keys){
			$device->clientsStaticsHours->each(function($value)use($user,&$keys){
				$timeLocal = Carbon::createFromFormat("Y-m-d H:i:s",$value["hours"])->addMinutes($user->timeZone * 60)->addMinutes($user->summerTime * 60);
				$keys[$timeLocal->toDateString()][$timeLocal->toDateTimeString()] += $value["onlines"];
			});
		});
		//统计计数
		return $keys;
	}

	//设备转组
	public function transGroup($user,$params){
		$gid = array_get($params,"gid");
		dd($gid);
	}

	protected function getDevices($user,$condition = [],$with = [],$columns = ["*"],$unique = false){
		array_push($condition,["user_id",$user->primaryId]);
		$devices = $this->deviceRepository->getInfos($condition,$with,$columns,$unique);
		if($unique){//单个设备
			if(!$user->is_primary){//子账号只可见其工作组下的设备
				$workGroupIDs = $user->workgroups->pluck("id");
				$devices = !empty($devices) && $workGroupIDs->contains($devices->group_id) ? $devices : NULL;
			}
			$res = $devices;
			if(!empty($res)){
				$devicesDynamic = $this->cacheRepository->getDeviceDynamic($res->dev_mac);
				$res->status = $devicesDynamic["status"];
				$res->cpu_use = $devicesDynamic["cpu_use"];
				$res->memory_use = $devicesDynamic["memory_use"];
				$res->runtime = $devicesDynamic["runtime"];
			}
		}else{//多个设备
			if(!$user->is_primary){//子账号只可见其工作组下的设备
				$workGroupIDs = $user->workgroups->pluck("id");
				$devices = $devices->filter(function($device) use ($workGroupIDs){
					return $workGroupIDs->contains($device->group_id);
				});
			}
			$devicesDynamic = $this->cacheRepository->getDevicesDynamic($devices->pluck("dev_mac"));
			$res = $devices->map(function($device) use ($devicesDynamic,$user){
				$currentDevice = $devicesDynamic[$device["dev_mac"]];
				$device->status = $currentDevice["status"];
				$device->cpu_use = $currentDevice["cpu_use"];
				$device->memory_use = $currentDevice["memory_use"];
				$device->runtime = $currentDevice["runtime"];
				return $device;
			});
		}
		return $res;
	}

	protected function checkDevices($macs,$devices,$status = NULL){
		if($devices->count() != count($macs)){
			throw new \Exception("Some device is not belong to you",config("exceptions.DEVICE_NO_USER"));
		}
		if(!is_null($status)){
			$flag = false;
			$devices->each(function($device) use ($status,&$flag){
				if($device->status != $status){
					$flag = true;
					return false;
				}
			});
			if($flag)
				throw new \Exception("The status of some device is not allowed",config("exceptions."));
		}
		return true;
	}

	public function afterOperater($datas){
		return $this->commandRepository->addAll($datas);
	}

}
