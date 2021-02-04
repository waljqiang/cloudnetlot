<?php
namespace Modules\Home\Services;

use Modules\Home\Services\DeviceBaseService;
use App\Repositories\CacheRepository;
use Modules\Home\Repositories\DeviceRepository;
use Modules\Home\Repositories\TopgraphyRepository;
use Modules\Home\Repositories\DeviceVirtureRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TopgraphyService extends DeviceBaseService{
	protected $deviceRepository;
	protected $cacheRepository;
	protected $topgraphyRepository;
	protected $deviceVirtureRepository;

	public function __construct(CacheRepository $cacheRepository,DeviceRepository $deviceRepository,TopgraphyRepository $topgraphyRepository,DeviceVirtureRepository $deviceVirtureRepository){
		$this->cacheRepository = $cacheRepository;
		$this->deviceRepository = $deviceRepository;
		$this->topgraphyRepository = $topgraphyRepository;
		$this->deviceVirtureRepository = $deviceVirtureRepository;
	}

	public function init($user,$params){
		$gid = array_get($params,"gid","");
		if(!empty($gid)){
			$rs = $this->topgraphyRepository->init($user->primary_id,$gid);
		}else{
			$rs = $this->topgraphyRepository->init($user->primary_id);
		}
		if($rs > 0){//清除拓扑图缓存
			if(!empty($gid)){
				$this->cacheRepository->deleteTopgraphy($user->primary_id,[$gid]);
			}else{
				$this->cacheRepository->deleteTopgraphy($user->primary_id,$user->gids);
			}
		}
		return [];
	}

	public function getInfo($user,$params){
		$gid = array_get($params,"gid");
		$devices = $this->getDevices($user,[["group_id",$gid]]);
		$macs = $devices->pluck("dev_mac")->toArray();
		$topDatas = $this->cacheRepository->getTopgraphy($user->primary_id,$gid);
		if(empty($topDatas)){
			$topDatas = $this->topgraphyRepository->getTopgraphy($user->primary_id,$macs,$gid);
			$this->cacheRepository->setTopgraphy($user->primary_id,$gid,$topDatas,config("topgraphy.expire"));
		}
		$topMacs = array_column($topDatas,"mac");
		$topParentMacs = array_column($topDatas,"pid");
		$dynamics = $this->cacheRepository->getDevicesDynamic($topMacs);
		//根节点
		$root = config("topgraphy.network");
		//如果找不到父则放到根下
		$topDatas = array_map(function($value)use($topMacs,$root){
			if($value["mac"] != $root["mac"] && !in_array($value["pid"],$topMacs)){
				$value["pid"] = $root["mac"];
			}
			return $value;
		},$topDatas);
		//添加动态信息
		$topDatas = $this->parseDynamics($topDatas,$dynamics,true);
		if(!empty($topDatas)){
			$datas = array_filter($topDatas,function($value)use($root){
				return $value['mac'] != $root["mac"];
			});
			$root = array_filter($topDatas,function($value)use($root){
				return $value['mac'] == $root["mac"];
			});
			$res = rankSort($datas,'mac','pid','children','mac',SORT_ASC,$root[0],true);
			$res = isset($res[0]) ? $res[0] : [];
		}else{
			$res = [];
		}
		return $res;
	}

	//从新生成拓扑图
	public function rebuild($user,$params){
		$gid = array_get($params,"gid");
		//校验用户权限
		$this->checkUserPermission($user,config("public.user.level.child_admin"),$gid);
		$devices = $this->getDevices($user,[["group_id",$gid]]);
		$macs = $devices->pluck("dev_mac")->toArray();
		$rootMac = config("topgraphy.network.mac");
		$time = Carbon::now()->timestamp;
		DB::beginTransaction();
		//删除拓扑图设备
		$rs1 = $this->topgraphyRepository->delete([
			["uid",$user->primary_id],
			["mac","!=",$rootMac],
			[function($query)use($macs){
				$query->whereIn("mac",$macs);
			}]
		]);
		//删除虚拟设备
		$rs2 = $this->deviceVirtureRepository->delete([
			["uid",$user->primary_id],
			["group_id",$gid]
		]);
		//恢复拓扑根信息
		$rs3 = $this->topgraphyRepository->save([
			"point_x" => config("topgraphy.network.point_x"),
			"point_y" => config("topgraphy.network.point_y"),
			"updated_at" => $time
		],[
			"uid" => $user->primary_id,
			"mac" => config("topgraphy.network.mac"),
			"group_id" => $gid
		]);
		if($rs1 && $rs2 && $rs3){
			DB::commit();
			//清除拓扑图缓存
			$this->cacheRepository->deleteTopgraphy($user->primary_id,[$gid]);
		}else{
			DB::rollBack();
			throw new \Exception("Failure",config("exceptions.MQTT_PUBLISH_ERROR"));
		}
		$this->init($user,$params);
		return [];
	}

	//生成虚拟设备MAC
	public function generateVirtureDeviceMac($user,$params){
		//校验用户权限
		$this->checkUserPermission($user,config("public.user.level.child_admin"));
		$time = Carbon::now();
		return $time->copy()->timestamp * 1000 + intval($time->copy()->micro/1000) . getStr(17,"0123456789ABCDEFHIJKLMNOPQRSTUVWXYZ");
	}

	//保存拓扑图
	public function save($user,$params){
		$gid = array_get($params,"gid");
		$data = array_get($params,"data");
		//校验用户权限
		$this->checkUserPermission($user,config("public.user.level.child_admin"),$gid);
		$tree = rankSort($data,"mac","pid","children","mac",SORT_ASC);
		if(count($tree) != 1){
			throw new \Exception("The data of topgraphy is not a valid tree",config("exceptions.TOPGRAPHY_DATA_NO_TREE"));
		}
		//根节点
		$root = array_filter($data,function($value){
			return $value["mac"] == config("topgraphy.network.mac");
		});
		if(empty($root)){
			throw new \Exception("The root of the data is invalid",config("exceptions.TOPGRAPHY_DATA_ROOT_INVALID"));
		}
		//移除根节点
		$data = array_filter($data,function($value){
			return $value["mac"] != config("topgraphy.network.mac");
		});
		$time = Carbon::now()->timestamp;
		//处理数据
		list($virtureData,$topgraphyData,$macs,$deviceNames,$dynamics) = $this->parseTopgraphyData($user,$gid,$data,$time);
		//校验真实设备
		$devices = $this->getDevices($user,[[function($query) use ($macs){
			$query->whereIn("dev_mac",$macs);
		}]]);
		$this->checkDevices($macs,$devices);
		DB::beginTransaction();
		// 清除之前老数据
		$rs1 = $this->topgraphyRepository->delete([
			["uid",$user->primary_id],
			["group_id",$gid],
			["mac","!=",$root[0]["mac"]]
		]);
		$rs2 = $this->deviceVirtureRepository->delete([
			["uid",$user->primary_id],
			["group_id",$gid]
		]);
		if(!empty($virtureData))
			$rs3 = $this->deviceVirtureRepository->addAll($virtureData);
		else
			$rs3 = true;
		if(!empty($topgraphyData))
			$rs4 = $this->topgraphyRepository->addAll($topgraphyData);
		else
			$rs4 = true;
		if(!empty($deviceNames))
			$rs5 = $this->deviceRepository->saveDevicesName($deviceNames);
		else
			$rs5 = true;
		$rs6 = $this->topgraphyRepository->save([
			"point_x" => $root[0]["point_x"],
			"point_y" => $root[0]["point_y"],
			"updated_at" => $time
		],[
			["uid",$user->primary_id],
			["group_id",$gid],
			["mac",$root[0]["mac"]]
		]);
		if($rs1 && $rs2 && $rs3 && $rs4 && $rs5 && $rs6){
			DB::commit();
			if(!empty($dynamics))
				$this->cacheRepository->setDevicesDynamics($dynamics);
			$this->cacheRepository->deleteTopgraphy($user->primary_id,[$gid]);
			return [];
		}else{
			DB::rollBack();
			throw new \Exception("Failure",config("exceptions.MQTT_PUBLISH_ERROR"));
		}
	}

	public function getDynamics($user,$params){
		$gid = array_get($params,"gid");
		//校验用户权限
		$this->checkUserPermission($user,config("public.user.level.child_guest"),$gid);
		$topDatas = $this->cacheRepository->getTopgraphy($user->primary_id,$gid);
		if(empty($topDatas)){
			$devices = $this->getDevices($user,[["group_id",$gid]]);
			$macs = $devices->pluck("dev_mac")->toArray();
			$topDatas = $this->topgraphyRepository->getRealDevices($user->primary_id,$macs,$gid);
		}else{
			$topDatas = array_filter($topDatas,function($topData){
				return $topData["is_virture"] == 0;
			});
		}
		$topMacs = array_column($topDatas,"mac");
		$dynamics = $this->cacheRepository->getDevicesDynamic($topMacs);
		$res = $this->parseDynamics($topDatas,$dynamics);
		return array_values($res);
	}

	//获取未编辑过的设备MAC
	public function getUnedits($user,$params){
		$gid = array_get($params,"gid");
		//校验用户权限
		$this->checkUserPermission($user,config("public.user.level.child_guest"),$gid);
		$devices = $this->getDevices($user,[["group_id",$gid]]);
		$macs = $devices->pluck("dev_mac")->toArray();
		$datas = $this->topgraphyRepository->getInfos([
			["uid",$user->primary_id],
			[function($query)use($macs){
				$query->whereIn("mac",$macs);
			}]
		]);
		return $datas->pluck("mac");
	}

	private function parseDynamics($topDatas,$dynamics,$encode = false){
		if(!empty($topDatas)){
			$root = config("topgraphy.network");
			$encode && $hashids = app("Hashids");
			$topDatas = array_combine(array_column($topDatas,"mac"),$topDatas);
			foreach ($topDatas as &$topData) {
				$topData["uid"] = $encode ? $hashids->encodeHash($topData["uid"]) : $topData["uid"];
				if($topData["is_virture"] == 1){//虚拟设备
					$topData["status"] = config("device.status.online");
					$topData["runtime"] = "-1";
					if($topData["mac"] == $root["mac"]){//根节点设备
						$topData["name"] = $root["name"];
						$topData["mode"] = $root["mode"];
						$topData["type"] = "-1";
						$topData["link"] = "-1";
						$topData["rssi"] = "-1";
					}elseif($topData["pid"] == $root["mac"]){//父节点是根节点
						$topData["link"] = "1";
						$topData["rssi"] = "1";
					}else{
						if(!isset($topDatas[$topData["pid"]]) || $topDatas[$topData["pid"]]["is_virture"] == 1){//父节点为虚拟设备
							$topData["link"] = "-1";
							$topData["rssi"] = "100";
						}else{
							if($dynamics[$topData["pid"]] == config("device.status.offline")){//父节点离线，连线则为离线
								$topData["link"] = "0";
								$topData["rssi"] = "100";
							}else{
								$topData["link"] = "1";
								$topData["rssi"] = "1";
							}
						}
					}
				}else{//真实设备
					$topData["status"] = $dynamics[$topData["mac"]]["status"];
					$topData["runtime"] = $dynamics[$topData["mac"]]["runtime"];
					if($topData["pid"] == $root["mac"]){//父节点为根节点
						$topData["link"] = $dynamics[$topData["mac"]] == config("device.status.offline") ? "0" : "1";
						$topData["rssi"] = $dynamics[$topData["mac"]] == config("device.status.offline") ? "100" : "1";
					}elseif(!isset($topDatas[$topData["pid"]]) || $topDatas[$topData["pid"]]["is_virture"] == 1){//父节点为虚拟设备
						$topData["link"] = $dynamics[$topData["mac"]] == config("device.status.offline") ? "0" : "1";
						$topData["rssi"] = $dynamics[$topData["mac"]] == config("device.status.offline") ? "100" : "1";
					}else{
						if($dynamics[$topData["mac"]] == config("device.status.offline") || $dynamics[$topData["pid"]] == config("device.status.offline")){//只要有一个离线，则连线为离线
							$topData["link"] = "0";
							$topData["rssi"] = "100";
						}else{
							$topData["link"] = $dynamics[$topData["mac"]]["link"];
							$topData["rssi"] = $dynamics[$topData["mac"]]["rssi"];
						}
					}
				}
			}
		}
		unset($topData);
		return $topDatas;
	}

	private function parseTopgraphyData($user,$gid,$data,$time){
		$virtureData = [];//虚拟设备数据
		$topgraphyData = [];//拓扑图数据
		$macs = [];//真实设备mac
		$deviceNames = [];//真实设备的名字
		$dynamics = [];//动态信息
		if(!empty($data)){
			foreach ($data as $value) {
				if(!empty($value["mac"])){
					if($value["is_virture"] == 1){//虚拟设备
						$virtureData[] = [
							"mac" => $value["mac"],
							"uid" => $user->primary_id,
							"name" => $value["name"],
							"status" => $value["status"],
							"mode" => $value["mode"],
							"group_id" => $gid,
							"created_at" => $time,
							"updated_at" => $time
						];
					}else{
						$macs[] = $value["mac"];
						$deviceNames[$value["mac"]] = $value["name"];
					}
					$topgraphyData[] = [
						"uid" => $user->primary_id,
						"mac" => $value["mac"],
						"pid" => $value["pid"],
						"is_edit" => 1,
						"is_virture" => $value["is_virture"],
						"point_x" => isset($value["point_x"]) ? intval($value["point_x"]) : -1,
						"point_y" => isset($value["point_y"]) ? intval($value["point_y"]) : -1,
						"group_id" => $gid
					];
					$dynamics[$value["mac"]] = [
						"link" => isset($value["link"]) ? $value["link"] : "-1",
						"rssi" => isset($value["rssi"]) ? $value["rssi"] : "-1"
					];
				}
			}
		}
		return [$virtureData,$topgraphyData,$macs,$deviceNames,$dynamics];
	}

}