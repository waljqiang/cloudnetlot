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

		if(empty($registerInfo["cltid"])){
			throw new \Exception("The device of the product is not connect to cloudnetlot",config("exceptions.DEV_NO_CONNECT"));
		}

		if(!empty($registerInfo["bindUid"]) && $registerInfo["bindUid"] != $user->id){
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

		//写命令表
		$rs2 = $this->commandRepository->add([
			"user_id" => $user->id,
			"dev_mac" => $mac,
			"comm_id" => $commID,
			"content" => $command,
			"type" => config("device.typeinfo.bind"),
			"status" => 2,
			"created_at" => $time,
			"updated_at" => $time
		]);
		

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
			["user_id",$user->primaryId],
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

		$allDevice = $this->deviceRepository->getInfos($condition,[],$columns);
		$devicesDynamic = $this->cacheRepository->getDevicesDynamic($allDevice->pluck("dev_mac"));
		$hashids = app("Hashids");
		$devices = $allDevice->map(function($device) use ($devicesDynamic,$user,$hashids){
			$currentDevice = $devicesDynamic[$device["dev_mac"]];
			$device->status = $currentDevice["status"];
			$device->group_id = $hashids->encodeHash($device->group_id);
			$device->join_time = convUnixToZoneGm($device->join_time,$user->timeZone,$user->isSummerTime);
			$device->created_at = convUnixToZoneGm($device->created_at,$user->timeZone,$user->isSummerTime);
			$device->cpu_use = $currentDevice["cpu_use"];
			$device->memory_use = $currentDevice["memory_use"];
			$device->runtime = $currentDevice["runtime"];
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
		if(empty($gid)){
			$gids = $user->workgroups->pluck("id")->toArray();
		}else{
			$gids[] = $gid;
		}
		$condition = [
			["user_id",$user->primaryId],
			[function($query) use ($gids){
				$query->whereIn("group_id",$gids);
			}]
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
		$gwIDs = $this->deviceRepository->getInfos($condition)->pluck("dev_mac");
		$devicesDynamic = $this->cacheRepository->getDevicesDynamic($gwIDs);
		$all = $online = $offline = 0;
		if(!empty($devicesDynamic)){
			foreach ($devicesDynamic as $deviceDynamic) {
				$all += 1;
				if($deviceDynamic["status"] == config("device.status.online")){
					$online += 1;
				}else{
					$offline += 1;
				}
			}
		}
		return ["all" => $all,"online" => $online,"offline" => $offline]; 
	}

	//获取设备信息
	public function getInfos($user,$params){
		$mac = array_get($params,"mac");
		$type = array_get($params,"type");
		$device = $this->deviceRepository->getInfos([
			["user_id",$user->primaryId],
			["dev_mac",$mac]
		],["params" => function($query) use ($type){
			$query->whereIn("type",$type);
		}],['*'],true);
		$deviceDynamic = $this->cacheRepository->getDeviceDynamic($device->dev_mac);
		$device->cpu_use = $deviceDynamic["cpu_use"];
		$device->memory_use = $deviceDynamic["memory_use"];
		$device->runtime = $deviceDynamic["runtime"];
		$device->status = $deviceDynamic["status"];
		$res = $device->params->mapWithKeys(function($infos) use ($device){
			return $this->deviceParamsService->parseParams($device,$infos);
		});
		return $res;
	}

}