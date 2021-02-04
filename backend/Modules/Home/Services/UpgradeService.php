<?php
namespace Modules\Home\Services;

use Modules\Home\Services\DeviceBaseService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Modules\Home\Repositories\PackageRepository;
use Modules\Home\Repositories\PackageTypeRepository;
use Modules\Home\Repositories\DeviceRepository;
use Modules\Home\Repositories\UpgradeOrderRepository;
use Modules\Home\Repositories\UpgradeOrderDevRepository;
use Modules\Home\Repositories\CommandRepository;
use App\Repositories\CacheRepository;

/**
 * 主账号关联了所有工作组，子账号只关联自己有关的工作组
 */

class UpgradeService extends DeviceBaseService{
	protected $packageRepository;
	protected $packageTypeRepository;
	protected $deviceRepository;
	protected $cacheRepository;
	protected $upgradeOrderRepository;
	protected $upgradeOrderDevRepository;
	protected $commandRepository;

	public function __construct(PackageRepository $packageRepository,PackageTypeRepository $packageTypeRepository,DeviceRepository $deviceRepository,CacheRepository $cacheRepository,UpgradeOrderRepository $upgradeOrderRepository,UpgradeOrderDevRepository $upgradeOrderDevRepository,CommandRepository $commandRepository){
		$this->packageRepository = $packageRepository;
		$this->packageTypeRepository = $packageTypeRepository;
		$this->deviceRepository = $deviceRepository;
		$this->cacheRepository = $cacheRepository;
		$this->upgradeOrderRepository = $upgradeOrderRepository;
		$this->upgradeOrderDevRepository = $upgradeOrderDevRepository;
		$this->commandRepository = $commandRepository;
	}

	//上传配置文件
	public function upload($user,$params){
		$file = array_get($params,"file");
		$type = array_get($params,"type");
		$version = array_get($params,"version");
		$types = explode(",",$type);
		//账号权限校验
		$this->checkUserPermission($user,config("public.user.level.child_admin"));
		if($file->isValid()){
			$realPath = $file->getRealPath();
			$fileExt = $file->getClientOriginalExtension();
			$fid = generateFid();
			$fileName = $file->getClientOriginalName();
			$time = Carbon::now()->timestamp;
			if(!in_array($fileExt,["bin","ubin","rbin"])){
				throw new \Exception("Unsupport file",config("exceptions.FILE_FILEEXT"));
			}
			$ftpService = Storage::disk("ftp");
			$dir = config("public.packagepath") . "/local/" . $user->primary_id;
			$ftpFile = $dir . "/" . $fileName;
			if(!$ftpService->exists($dir)){
				$ftpService->makeDirectory($dir);
			}else{
				if($ftpService->exists($ftpFile)){
					throw new \Exception("The packages is already exists",config("exceptions.FILE_EXISTS"));
				}
			}
			$rs1 = $ftpService->put($ftpFile,file_get_contents($realPath));
			if($rs1){
				$url = "ftp://" . config("filesystems.disks.ftp.downloaduser") . ":" . config("filesystems.disks.ftp.downloadpassword") . "@" . config("filesystems.disks.ftp.host") . "/" . $ftpFile;
				$rs2 = $this->packageRepository->add([
					"fid" => $fid,
					"name" => $fileName,
					"url" => $url,
					"version" => !empty($version) ? $version : getVersion($fileName),
					"user_id" => $user->primary_id,
					"file_md5" => md5_file($realPath,false),
					"is_local" => 1,
					"size" => $file->getClientSize(),
					"created_at" => $time,
					"updated_at" => $time
				]);
				$packageTypeAdd = array_map(function($value)use($fid,$time){
					return [
						"fid" => $fid,
						"type" => $value,
						"created_at" => $time,
						"updated_at" => $time
					];
				},$types);
				$rs3 = $this->packageTypeRepository->addAll($packageTypeAdd);
				if(!$rs2 || !$rs3){
					$ftpService->delete($ftpFile);
					throw new \Exception("Failure",config("exceptions.MYSQL_EXEC_ERROR"));
				}else{
					return ["fid" => $fid];
				}
			}else{
				throw new \Exception("Ftp Upload failure",config("exceptions.FTP_UPLOAD_FAILURE"));
			}
		}
	}

	public function getLocalPackages($user,$params){
		$macs = array_get($params,"macs");
		//账号权限校验
		$this->checkUserPermission($user,config("public.user.level.child_admin"));
		$devices = $this->getDevices($user,[[function($query) use ($macs){
			$query->whereIn("dev_mac",$macs);
		}]]);
		//校验设备
		$this->checkDevices($macs,$devices,config("device.status.online"));
		$infos = $this->packageRepository->getPackages([
			["user_id",$user->primary_id],
			["is_local",1],
		],["types"],$devices->pluck("type")->toArray());
		$dir = config("public.packagepath") . "/local/" . $user->primary_id;
		$ftpFiles = Storage::disk("ftp")->files($dir);
		list($exists,$noexists) = $infos->partition(function($info)use($dir,$ftpFiles){
			return in_array($dir . "/" . $info->name,$ftpFiles);
		});
		if(!$noexists->isEmpty()){// 清除不存在的包记录
			$fids = $noexists->pluck("fid");
			DB::beginTransaction();
			$rs1 = $this->packageRepository->delete([
				["user_id",$user->primary_id],
				["is_local",1],
				[function($query)use($fids){
					$query->whereIn("fid",$fids);
				}]
			]);
			$rs2 = $this->packageTypeRepository->delete([
				[function($query)use($fids){
					$query->whereIn("fid",$fids);
				}]
			]);
			if($rs1 && $rs2){
				DB::commit();
			}else{
				DB::rollBack();
			}
		}
		return $exists->map(function($value){
			return [
				"fid" => $value->fid,
				"name" => $value->name,
				"version" => $value->version,
				"types" => $value->support_types,
				"size" => $value->size,
				"downloads" => $value->downloads
			];
		});
	}

	public function deleteLocalPackages($user,$params){
		$fids = array_get($params,"fids");
		//账号权限校验
		$this->checkUserPermission($user,config("public.user.level.child_admin"));
		$packages = $this->packageRepository->getInfos([
			["user_id",$user->primary_id],
			["is_local",1],
			[function($query)use($fids){
				$query->whereIn("fid",$fids);
			}]
		]);
		if(count($fids) != $packages->count()){
			throw new \Exception("The packages is not exists",config("exceptions.FILE_NO"));
		}
		DB::beginTransaction();
		$rs1 = $this->packageRepository->delete([
			["user_id",$user->primary_id],
			["is_local",1],
			[function($query)use($fids){
				$query->whereIn("fid",$fids);
			}]
		]);
		$rs2 = $this->packageTypeRepository->delete([
			[function($query)use($fids){
				$query->whereIn("fid",$fids);
			}]
		]);
		if($rs1 && $rs2){
			DB::commit();
			$ftpService = Storage::disk("ftp");
			$dir = config("public.packagepath") . "/local/" . $user->primary_id;
			$rmFiles = $packages->map(function($package)use($dir){
				return $dir . "/" . $package->name;
			})->toArray();
			$ftpService->delete($rmFiles);
		}else{
			DB::rollBack();
			throw new \Exception("Failure",config("exceptions.MYSQL_EXEC_ERROR"));
		}
		return [];
	}

	//升级
	public function upgrades($user,$params){
		$macs = array_get($params,"macs");
		$fid = array_get($params,"fid");
		$time = array_get($params,"time");
		$orderID = generateOrderid();
		$now = Carbon::now();
		if(!empty($time)){
			$time = Carbon::createFromFormat("Y-m-d H:i:s",$time)->subMinutes($user->timeZone * 60)->subMinutes($user->summerTime * 60);
			if($time->lte($now)){
				throw new \Exception("The time must be after now",config("exceptions.DATE_AFTER_NOW"));
			}
			$execTime = (string)$time->addSeconds(config("public.upgradedelay"))->timestamp;
		}else{
			$execTime = (string)$now->copy()->addSeconds(config("public.upgradedelay"))->timestamp;
		}
		//账号权限校验
		$this->checkUserPermission($user,config("public.user.level.child_admin"));
		$devices = $this->getDevices($user,[[function($query) use ($macs){
			$query->whereIn("dev_mac",$macs);
		}]]);
		//校验设备
		$this->checkDevices($macs,$devices,config("device.status.online"));
		$package = $this->packageRepository->getInfos([
			["fid",$fid]
		],["types"],["*"],true);
		if(empty($package)){
			throw new \Exception("The package is not exists",config("exceptions.FILE_NO"));
		}
		$ftpService = Storage::disk("ftp");
		$file = config("public.packagepath") . "/local/" . $user->primary_id . "/" . $package->name;
		if(!$ftpService->exists($file)){
			throw new \Exception("The package is not exists",config("exceptions.FILE_NO"));
		}
		$packageTypes = $package->support_types;
		$orderDevs = [];
		$topics = [];
		$commID = getCommID(config("yunlot.lottype.down"),config("device.typeinfo.upgrade"),$now->timestamp);
		$command = getCommand(config("device.typeinfo.upgrade"),["command" => ["type" => "upgrade","url" => $package->url,"wait" => $execTime-$now->timestamp,"file" => $package->file_md5]],$now->timestamp,$commID);
		$afterDatas = [];
		$support = $devices->every(function($device)use($user,$packageTypes,$orderID,$now,$commID,$command,&$orderDevs,&$topics,&$afterDatas){
			$orderDevs[] = [
				"orderid" => $orderID,
				"dev_mac" => $device->dev_mac,
				"dev_name" => $device->name,
				"type" => $device->type,
				"created_at" => $now->timestamp,
				"updated_at" => $now->timestamp
			];
			$topics[] = getTopic($device->prtid,$device->cltid);
			$afterDatas[] = [
				"user_id" => $user->id,
				"dev_mac" => $device->dev_mac,
				"dev_ip" => $device->dev_ip,
				"dev_name" => $device->name,
				"dev_version" => $device->version,
				"dev_type" => $device->type,
				"comm_id" => $commID,
				"content" => $command,
				"type" => config("device.typeinfo.upgrade"),
				"status" => 2,
				"created_at" => $now->timestamp,
				"updated_at" => $now->timestamp
			];
			return $packageTypes->contains($device->type);
		});
		if(!$support){
			throw new \Exception("Unsupport the device",config("exceptions.DEV_TYPE_UNSUPPORT"));
		}
		$order = [
			"orderid" => $orderID,
			"user_id" => $user->id,
			"package_id" => $package->fid,
			"package_name" => $package->name,
			"package_md5" => $package->file_md5,
			"package_url" => $package->url,
			"version" => $package->version,
			"exec_time" => $execTime,
			"created_at" => $now->timestamp,
			"updated_at" => $now->timestamp
		];
		DB::beginTransaction();
		$rs1 = $this->upgradeOrderRepository->add($order);
		$rs2 = $this->upgradeOrderDevRepository->addAll($orderDevs);
		if(!$rs1 || !$rs2){
			DB::rollBack();
			throw new \Exception("Failure",config("exceptions.MYSQL_EXEC_ERROR"));
		}else{
			DB::commit();
		}
		//发命令	
		if(!sendToMqtt($topics,$command)){
			throw new \Exception($e->getMessage(),config("exceptions.MQTT_PUBLISH_ERROR"));
		}
		$this->afterOperater($afterDatas);
		return [];
	}

	//获取升级单列表
	public function getOrders($user,$params){
		$status = array_get($params,"status",4);
		$start = array_get($params,"start","");
		$end = array_get($params,"end","");
		$userIDs[] = $user->primary_id;
		if(!$user->is_primary){
			$userIDs = $user->child_ids;
		}
		$conditions = [
			[function($query)use($userIDs){
				$query->whereIn("user_id",$userIDs);
			}]
		];
		if(in_array($status,[0,1,2,3])){
			array_push($conditions,["status",$status]);
		}
		if(!empty($start) && !empty($end)){
			$start = Carbon::createFromFormat("Y-m-d H:i:s",$start)->subMinutes($user->timeZone * 60)->subMinutes($user->summerTime * 60)->timestamp;
			$end = Carbon::createFromFormat("Y-m-d H:i:s",$end)->subMinutes($user->timeZone * 60)->subMinutes($user->summerTime * 60)->timestamp;
			array_push($conditions,[function($query)use($start,$end){
				$query->whereBetween("created_at",[$start,$end]);
			}]);
		}
		$orders = $this->upgradeOrderRepository->getInfos($conditions,["user"]);
		return $orders->map(function($order)use($user){
			return [
				"orderid" => $order->orderid,
				"user_id" => $order->user_id,
				"username" => $order->user->username,
				"package_id" => $order->package_id,
				"package_name" => $order->package_name,
				"version" => $order->version,
				"status" => $order->status,
				"exec_time" => convUnixToZoneGm($order->exec_time,$user->timeZone,$user->summerTime),
				"created_at" => convUnixToZoneGm($order->created_at,$user->timeZone,$user->summerTime)
			];
		});
	}

}