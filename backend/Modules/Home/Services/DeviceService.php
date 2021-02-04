<?php
namespace Modules\Home\Services;

use Modules\Home\Services\DeviceBaseService;
use App\Repositories\CacheRepository;
use Modules\Home\Services\DeviceParamsService;
use Modules\Home\Repositories\DeviceRepository;
use Modules\Home\Repositories\CommandRepository;
use Modules\Home\Repositories\TopgraphyRepository;
use Modules\Home\Repositories\DeviceRelationRepository;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Utils\HashCode;
use Maatwebsite\Excel\Facades\Excel;

class DeviceService extends DeviceBaseService{
	protected $deviceParamsService;
	protected $deviceRepository;
	protected $cacheRepository;
	protected $commandRepository;
	protected $deviceClientsNumsRepository;
	protected $topgraphyRepository;
	protected $deviceRelationRepository;

	public function __construct(DeviceParamsService $deviceParamsService,DeviceRepository $deviceRepository,CacheRepository $cacheRepository,CommandRepository $commandRepository,TopgraphyRepository $topgraphyRepository,DeviceRelationRepository $deviceRelationRepository){
		$this->deviceParamsService = $deviceParamsService;
		$this->deviceRepository = $deviceRepository;
		$this->cacheRepository = $cacheRepository;
		$this->commandRepository = $commandRepository;
		$this->topgraphyRepository = $topgraphyRepository;
		$this->deviceRelationRepository = $deviceRelationRepository;
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
		//账号权限校验
		$this->checkUserPermission($user,config("public.user.level.child_admin"));
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
			$this->cacheRepository->setRegister($prtid,$mac,"",config("public.cache.registerttl"));
			throw new \Exception("The unpublish product just only bind to developer",config("exceptions.PRT_STATUS_NO_ALLOW"));
		}

		if(empty($registerInfo["cltid"])){//设备还未与云平台建立连接
			$this->cacheRepository->setRegister($prtid,$mac,"",config("public.cache.registerttl"));
			throw new \Exception("The device of the product is not connect to cloudnetlot",config("exceptions.DEV_NO_CONNECT"));
		}

		if(!empty($registerInfo["bindUid"]) && $registerInfo["bindUid"] != $user->id){//绑定其他用户时，需要先解绑
			$this->cacheRepository->setRegister($prtid,$mac,"",config("public.cache.registerttl"));
			throw new \Exception("The device is binded to another user",config("exceptions.DEV_BINDED"));
		}

		//发送绑定命令
		DB::beginTransaction();
		$bindCode = getBindCode($user->primary_id,$mac,$gid);
		$registerInfo["bind"] = $bindCode;
		$this->cacheRepository->setRegister($prtid,$mac,$registerInfo,config("public.cache.registerttl"));
		$rs1 = $this->deviceRepository->save([
			"bind" => $bindCode,
			"group_id" => $gid,
			"updated_at" => $time
		],["dev_mac" => $mac]);
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
				DB::rollBack();
				throw new \Exception("Failure",config("exceptions.MQTT_PUBLISH_ERROR"));
			}
		}else{
			DB::rollBack();
			throw new \Exception("Failure",config("exceptions.MYSQL_EXEC_ERROR"));
		}
	}

	//获取设备列表
	public function getList($user,$params){
		$gid = array_get($params,"gid","");
		$status = array_get($params,"status",NULL);
		$sortKey = array_get($params,"sortkey","join_time");
		$sort = array_get($params,"sort","desc");
		$pageIndex = array_get($params,"pageIndex",1);
		$pageOffset = array_get($params,"pageOffset",10);
		$keyword = array_get($params,"search","");
		$type = array_get($params,"type","");
		
		$condition = [
			["pid",""]
		];
		if(!empty($gid)){
			if(!in_array($gid, $user->gids)){
				throw new \Exception("The workgroup is not exists",config("exceptions.USER_NO_WORKGROUP"));
			}
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
		$columns = ["user_id","dev_mac","pid","dev_ip","net_ip","name","prt_type","prt_size","type","mode","version","up_time","latitude","longitude","chip","group_id","join_time","created_at"];

		$allDevice = $this->getDevices($user,$condition,[],$columns);

		$devices = $allDevice->map(function($device) use ($user){
			$device->join_time = convUnixToZoneGm($device->join_time,$user->timeZone,$user->isSummerTime);
			$device->created_at = convUnixToZoneGm($device->created_at,$user->timeZone,$user->isSummerTime);
			return $device;
		});

		if(!is_null($status)||!empty($type)){
			$devices = $devices->filter(function($device) use ($status,$type){
				if(!is_null($status) && !empty($type)){
					return $device->status == $status && $device->type == $type;
				}elseif(!is_null($status)){
					return $device->status == $status;
				}elseif(!empty($type)){
					return $device->type == $type;
				}else{

				}
			});
		}
		
		$total = $devices->count();
		$list = "desc" == $sort ? $devices->sortByDesc($sortKey)->values()->forpage($pageIndex,$pageOffset) : $devices->sortBy($sortKey)->values()->forpage($pageIndex,$pageOffset);
		return ["total" => $total,"list" => $list];
	}

	//设备统计
	public function statistics($user,$params){
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

	//重启设备
	public function restart($user,$params){
		$mac = array_get($params,"mac");
		$this->checkUserPermission($user,config("public.user.level.child_admin"));
		//校验设备
		$device = $this->getDevices($user,[
			["dev_mac",$mac]
		],[],["*"],true);
		$this->checkDevices($mac,$device,config("device.status.online"));
		$time = Carbon::now()->timestamp;
		$topic = getTopic($device->prtid,$device->cltid);
		$commID = getCommID(config("yunlot.lottype.down"),config("device.typeinfo.time_reboot"),$time);
		$command = getCommand(config("device.typeinfo.time_reboot"),["command" => ["type" => "reboot"]],$time,$commID);
		if(!sendToMqtt([$topic],$command)){
			throw new \Exception("Mqtt publish failure",config("exceptions.MQTT_PUBLISH_ERROR"));
		}
		$this->cacheRepository->setDevicesDynamic([$mac],["status" => config("device.status.offline")]);
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
				"type" => config("device.typeinfo.time_reboot"),
				"status" => 2,
				"created_at" => $time,
				"updated_at" => $time
			]
		]);
		return [];
	}

	//设备定时重启
	public function timeRestart($user,$params){
		$mac = array_get($params,"mac");
		$enable = array_get($params,"enable");
		$timeReg = array_get($params,"time");
		//账号权限校验
		$this->checkUserPermission($user,config("public.user.level.child_admin"));
		//校验设备
		$device = $this->getDevices($user,[
			["dev_mac",$mac]
		],[
			"params" => function($query){
				$query->where("type",config("device.typeinfo.time_reboot"));
			}
		],["*"],true);
		$this->checkDevices($mac,$device,config("device.status.online"));
		$command = [
			"type" => "reboot_time",
			"enable" => $enable
		];
		if($enable == 1){
			$command["time"] = $timeReg;
		}
		$time = Carbon::now()->timestamp;
		$commID = getCommID(config("yunlot.lottype.down"),config("device.typeinfo.time_reboot"),$time);
		$command = getCommand(config("device.typeinfo.time_reboot"),["command" => $command],$time,$commID);
		$topic = getTopic($device->prtid,$device->cltid);
		if(!sendToMqtt([$topic],$command)){
			throw new \Exception("Mqtt publish failure",config("exceptions.MQTT_PUBLISH_ERROR"));
		}
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
				"type" => config("device.typeinfo.time_reboot"),
				"status" => 2,
				"created_at" => $time,
				"updated_at" => $time
			]
		]);
		return [];
	}

	//批量重启设备
	public function restarts($user,$params){
		$macs = array_get($params,"macs");
		//账号权限校验
		$this->checkUserPermission($user,config("public.user.level.child_admin"));
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
			throw new \Exception("Mqtt publish failure",config("exceptions.MQTT_PUBLISH_ERROR"));
		}
		$this->cacheRepository->setDevicesDynamic($macs,["status" => config("device.status.offline")]);
		//记录命令
		$this->afterOperater($records);
		return [];
	}

	//按小时统计用户下所有设备上在线用户总数
	public function statisticsOnlineClients($user,$params){
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
		$macs = array_get($params,"macs");
		//校验账号权限
		$this->checkUserPermission($user,config("public.user.level.child_admin"),$gid);
		//校验设备
		$devices = $this->getDevices($user,[[function($query) use ($macs){
			$query->whereIn("dev_mac",$macs);
		}]]);
		$this->checkDevices($macs,$devices);
		$time = Carbon::now()->timestamp;
        DB::beginTransaction();
		$rs1 = $this->deviceRepository->save([
			"group_id" => $gid,
			"updated_at" => $time
		],[
			[function($query)use($macs){
				$query->whereIn("dev_mac",$macs);
			}]
		]);
		//清除被转组设备及其子设备的拓扑图位置信息并放入根工作组下
		$rs2 = $this->topgraphyRepository->save([
			"pid" => config("topgraphy.network.mac"),
			"point_x" => "-1",
			"point_y" => "-1",
			"group_id" => $gid,
			"updated_at" => $time
		],[
			["uid",$user->primary_id],
			[
				function($query)use($user,$macs){
					$query->whereIn("mac",$macs)->orWhere(function($query)use($macs){
						$query->whereIn("pid",$macs);
					});
				}
			]
		]);
		if($rs1 && $rs2){
			DB::commit();
			//清除所有相关工作组下拓扑图缓存信息
			$gids = $devices->map(function($device){
				return $device->group_id;
			})->toArray();
			$gids = array_flip(array_flip(array_merge($gids,[$gid])));
			$this->cacheRepository->deleteTopgraphy($user->primary_id,$gids);
			return [];
		}else{
			DB::rollBack();
			throw new \Exception("Failure",config("exceptions.MYSQL_EXEC_ERROR"));
		}
	}

	//导出设备列表
	public function exportLists($user,$params){
		$gid = array_get($params,"gid");
		$lang = array_get($params,"lang","zh-cn");
		if(!in_array($gid, $user->gids)){
			throw new \Exception("The workgroup is not exists",config("exceptions.USER_NO_WORKGROUP"));
		}
		//校验账号权限
		$this->checkUserPermission($user,config("public.user.level.child_admin"));
		$groupID = app("Hashids")->encodeHash($gid);
		$condition = [
			["group_id",$gid],
			["pid",""]
		];
		$columns = ["user_id","dev_mac","pid","dev_ip","net_ip","name","prt_type","prt_size","type","mode","version","up_time","latitude","longitude","chip","group_id","join_time","created_at"];

		$devices = $this->getDevices($user,$condition,[],$columns);
		$trans = config("public.export.list.trans");
        $cellData = $devices->map(function($device)use($user,$trans,$lang){
			return [
				$device->dev_mac,
				$device->dev_ip ? $device->dev_ip : "---",
				$device->name ? $device->name : "---",
				$device->type,
				$trans["mode"][$lang][$device->mode],
				$device->version,
				convUnixToZoneGm($device->join_time,$user->timeZone,$user->isSummerTime),
				$trans["status"][$lang][$device->status]
			];
        })->toArray();
        array_unshift($cellData, config("public.export.list.columns.name"));
        array_unshift($cellData,["用户[" . $user->username . "] 在工作组[" . $groupID . "]下的设备列表"]);
		$exportName = $user->username . "_" . $groupID . "_devices_" . Carbon::now()->format("YmdHis");

        Excel::create($exportName, function ($excel) use ($cellData) {
            $excel->setCreator("Cloudnetlot");
            $excel->setLastModifiedBy("Cloudnetlot");
            $excel->setTitle("Data from NovaiCare");
            $excel->setSubject("Office Excel");
            $excel->setDescription("Cloudnetlot - device list");
            $excel->setKeywords("Cloudnetlot - device list");
            $excel->setCategory("Cloudnetlot - device list");
            $excel->sheet("device list", function ($sheet) use ($cellData) {
            	$sheet->setWidth(config("public.export.list.columns.width"));
            	$sheet->setFontSize(16);
            	$sheet->mergeCells("A1:H1");
            	$sheet->setHeight([1 => 30]);
            	//字体加粗
            	$sheet->cell('A1:H1', function ($cell) {
                    $cell->setFontWeight('bold');
                });
                //表格背景颜色
                $sheet->cell('A2:H2',function($cell){
                	$cell->setBackground("#92d050");
                	$cell->setAlignment("center");
                });

                for($i=1;$i <= count($cellData);$i++){
                	if($i > 1){
            			$sheet->setHeight($i,18);
            		}
                	$sheet->row($i,$cellData[$i-1]);
                }
            });
        })->export('xls');
	}

	//上报信息
	public function reports($user,$params){
		$macs = array_get($params,"macs");
		$type = array_get($params,"type",[]);
		//账号权限校验
		$this->checkUserPermission($user,config("public.user.level.child_admin"));
		$devices = $this->getDevices($user,[[function($query) use ($macs){
			$query->whereIn("dev_mac",$macs);
		}]]);

		//校验设备
		$this->checkDevices($macs,$devices,config("device.status.online"));
		$dataType = [];
		if(!empty($type)){
			$dataType = array_map(function($value){
				return getCommType($value);
			},$type);
		}

		//发送命令
		$time = Carbon::now()->timestamp;
		$commID = getCommID(config("yunlot.lottype.down"),config("device.typeinfo.up"),$time);
		$command = getCommand(config("device.typeinfo.up"),["command" => ["type" => "up","datatype" => $dataType]],$time,$commID);
		$devices->each(function($device){

		});
		$records = [];
		$topics = $devices->map(function($device) use ($user,$commID,$command,$time,&$records){
			$records[] = [
				"user_id" => $user->id,
				"dev_mac" => $device->dev_mac,
				"dev_ip" => $device->dev_ip,
				"dev_name" => $device->name,
				"dev_version" => $device->version,
				"dev_type" => $device->type,
				"comm_id" => $commID,
				"content" => $command,
				"type" => config("device.typeinfo.up"),
				"status" => 2,
				"created_at" => $time,
				"updated_at" => $time
			];
			return getTopic($device->prtid,$device->cltid);
		})->toArray();
		if(!sendToMqtt($topics,$command)){
			throw new \Exception("Mqtt publish failure",config("exceptions.MQTT_PUBLISH_ERROR"));
		}
		//记录命令
		$this->afterOperater($records);
		return [];
	}

	//批量删除设备
	public function deletes($user,$params){
		$macs = array_get($params,"macs");
		//账号权限校验
		$this->checkUserPermission($user,config("public.user.level.child_admin"));
		$devices = $this->getDevices($user,[[function($query) use ($macs){
			$query->whereIn("dev_mac",$macs);
		}]]);
		//校验设备
		$this->checkDevices($macs,$devices,config("device.status.offline"));

		DB::beginTransaction();
		$rs1 = $this->deviceRepository->delete([
			["user_id",$user->primary_id],
			[function($query)use($macs){
				$query->whereIn("dev_mac",$macs);
			}]
		]);
		//删除拓扑图中设备
		$rs2 = $this->topgraphyRepository->delete([["uid",$user->primary_id]]);
		//删除拓扑关系中的设备
		$rs3 = $this->deviceRelationRepository->delete([
			["uid",$user->primary_id],
			[function($query)use($macs){
				$query->whereIn("mac",$macs);
			}]
		]);
		if($rs1 && $rs2 && $rs3){
			DB::commit();
			$registerInfo = [];
			//清除所有相关工作组下拓扑图缓存
			$gids = $devices->map(function($device)use(&$registerInfo){
				$registerInfo[$device->prtid] = $device->dev_mac;
				return $device->group_id;
			})->toArray();
			$this->cacheRepository->deleteTopgraphy($user->primary_id,$gids);
			//清除注册缓存信息
			$this->cacheRepository->delRegister($registerInfo);
			//清除设备缓存信息
			$this->cacheRepository->delDevicesDynamics($macs);
			unset($registerInfo);
			return [];
		}else{
			DB::rollBack();
			throw new \Exception("Failure",config("exceptions.MYSQL_EXEC_ERROR"));		
		}
	}

	//设置设备名称
	public function setName($user,$params){
		$mac = array_get($params,"mac");
		$name = array_get($params,"name");
		//账号权限校验
		$this->checkUserPermission($user,config("public.user.level.child_admin"));
		$device = $this->getDevices($user,[
			["dev_mac",$mac]
		],[],["*"],true);
		//校验设备
		$this->checkDevices($mac,$device,config("device.status.online"));
		$rs = $this->deviceRepository->save([
			"name" => $name,
			"updated_at" => Carbon::now()->timestamp
		],[
			"user_id" => $user->primary_id,
			"dev_mac" => $mac
		]);
		if(!$rs){
			throw new \Exception("Failure",config("exceptions.MYSQL_EXEC_ERROR"));
		}
		//清除拓扑图缓存
		$this->cacheRepository->deleteTopgraphy($user->primary_id,[$device->group_id]);
		//todo 发命令给设备
		return [];
	}

	//获取所有设备型号
	public function getTypes($user,$params){
		$gid = array_get($params,"gid");
		//账号权限校验
		$this->checkUserPermission($user,config("public.user.level.child_child"),$gid);
		$devices = $this->getDevices($user,[["group_id",$gid]]);
		return $devices->pluck("type");
	}

}
