<?php
namespace Modules\Home\Services;

use App\Services\BaseService;
use Carbon\Carbon;
use App\Utils\Msg;
use App\Repositories\CacheRepository;
use Modules\Home\Repositories\DeviceRepository;

class DeviceBaseService extends BaseService{

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
		if(is_string($macs)){
			if(empty($devices)){
				throw new \Exception("The device is not belong to you",config("exceptions.DEVICE_NO_USER"));
			}
			if(!is_null($status) && $devices->status != $status){
				throw new \Exception("The status of the device is not allowed",config("exceptions.DEVICE_STATUS_NO_ALLOWED"));
			}
		}else{
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
					throw new \Exception("The status of some device is not allowed",config("exceptions.DEVICE_STATUS_NO_ALLOWED"));
			}
		}
		return true;
	}

	protected function afterOperater($datas){
		$rs = $this->commandRepository->addAll($datas);
		if(!$rs){
			throw new \Exception("Failure",config("exceptions.MYSQL_EXEC_ERROR"));
		}
		return $rs;
	}
}