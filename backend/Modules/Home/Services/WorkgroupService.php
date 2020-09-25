<?php
namespace Modules\Home\Services;

use App\Services\BaseService;
use Modules\Home\Repositories\WorkgroupRepository;
use Modules\Home\Repositories\DeviceRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class WorkgroupService extends BaseService{
	private $workgroupRepository;

	public function __construct(WorkgroupRepository $workgroupRepository, DeviceRepository $deviceRepository){
		$this->workgroupRepository = $workgroupRepository;
		$this->deviceRepository = $deviceRepository;
	}

	//获取子工作组列表
	public function getAll($user,$params){
		$tree = array_get($params,"tree",1);
		$hashids = app("Hashids");
		$workgroups = $user->workgroups->map(function($value) use ($hashids){
			return [
				"gid" => $hashids->encodeHash($value->id),
				"user_id" => $hashids->encodeHash($value->user_id),
				"name" => $value->name,
				"pid" => $hashids->encodeHash($value->pid)
			];
		});

		return $tree ? rankSort($workgroups->toArray(),"gid","pid","child","gid") : [
			"total" => $workgroups->count(),
			"list" => $workgroups
		];
	}

	//获取工作组信息
	public function getInfo($user,$params){
		$gid = array_get($params,"gid");
		$info = $user->workgroups()->where("group.id",$gid)->first();
		if($info){
			$info->gid = $info->id;
			$info->created_at = convUnixToZoneGm($info->created_at,$user->timeZone,$user->isSummerTime);
			$info->updated_at = convUnixToZoneGm($info->updated_at,$user->timeZone,$user->isSummerTime);
			return $info->makeHidden("id")->makeHidden("pivot");
		}else{
			throw new \Exception("You don't have this workgoup",config("exceptions.USER_NO_WORKGROUP"));
		}
	}

	//上传配置文件
	public function uploadConfig($user,$params){
		$file = array_get($params,"file");
		if($file->isValid()){
			$realPath = $file->getRealPath();
			$fid = getStr(10) . Carbon::now()->timestamp;
			$filename = $fid . "." . $file->getClientOriginalExtension();
			$ftpService = Storage::disk('ftp');
			$dir = "deviceconfig/" . $user->id;
			if(!$ftpService->exists($dir)){
				$ftpService->makeDirectory($dir);
			}
			$rs = $ftpService->put($dir . "/" . $filename,file_get_contents($realPath));
			if($rs){
				//$url = "ftp://" . config("filesystems.disks.ftp.downloaduser") . ":" . config("filesystems.disks.ftp.downloadpassword") . "@" . config("filesystems.disks.ftp.host") . "/" . $dir . "/" . $filename;
				return ["fid" => $fid];
			}else{
				throw new \Exception("Ftp Upload failure",config("exceptions.FTP_UPLOAD_FAILURE"));
			}
		}
	}
	//添加工作组
	public function addWorkgroup($user,$params){
		$gid = array_get($params,"gid");
		$name = array_get($params,"name");
		$description = array_get($params,"description","");
		$configID = array_get($params,"config_id","");
		$auto = array_get($params,"auto",0);
		$time = Carbon::now()->timestamp;
		if(!$user->is_primary){
			throw new \Exception("No permission",config("exceptions.NO_PERMISSTION"));
		}
		$groupInfo = $this->workgroupRepository->getInfos([["user_id",$user->id],["id",$gid]],[],['*'],true);
		if(!$groupInfo){
			throw new \Exception("You don't have this workgoup",config("exceptions.USER_NO_WORKGROUP"));
		}
		if($groupInfo->level +1 >= config("public.workgroup.level")){
			throw new Exception("Maximum depth reached",config("exceptions.MAX_DEPTH"));		
		}
		$code = $this->workgroupRepository->generateChildCode($groupInfo->user_id,$groupInfo->id);
		$groupID = $this->workgroupRepository->add([
			"user_id" => $user->id,
			"pid" => $gid,
			"code" => $code,
			"name" => $name,
			"description" => $description,
			"config_id" => $configID,
			"auto" => $auto,
			"level" => $groupInfo->level + 1,
			"created_at" => $time,
			"updated_at" => $time
		]);
		//绑定关系
		$user->workgroups()->attach($groupID,["created_at" => $time,"updated_at" => $time]);

		return ["gid" => $groupID];
	}

	//修改工作组
	public function saveWorkgroup($user,$params){
		$gid = array_get($params,"gid");
		
		if(!$user->is_primary){
			throw new \Exception("No permission",config("exceptions.NO_PERMISSTION"));
		}
		$groupInfo = $this->workgroupRepository->getInfos([["user_id",$user->id],["id",$gid]],[],['*'],true);
		if(!$groupInfo){
			throw new \Exception("You don't have this workgoup",config("exceptions.USER_NO_WORKGROUP"));
		}
		if(empty($groupInfo->pid)){
			throw new \Exception("The work group of the system can't be modify",config("exceptions.WORKGROUP_ROOT_NO"));
		}
		$data = array_intersect_key($params,[
			"name" => "",
			"description" => "",
			"config_id" => "",
			"auto" => 0
		]);
		if(empty($data)){
			throw new \Exception("Params is invalid",config("exceptions.PARAMS_INVALID"));
		}
		$data["updated_at"] = Carbon::now()->timestamp;
		$rs = $this->workgroupRepository->save($data,[
			"id" => $gid,
			"user_id" => $user->primary_id
		]);
		//todo自动应用配置
		if($rs){
			return [];
		}else{
			throw new \Exception("Failure",config("exceptions.MYSQL_EXEC_ERROR"));
		}
	}

	//删除工作组
	public function deleteWorkgroup($user,$params){
		$gid = array_get($params,"gid");
		//只有主账号才能操作
		if(!$user->is_primary){
			throw new \Exception("No permission",config("exceptions.NO_PERMISSTION"));
		}
		//工作组必须属于操作者
		$workgroup = $user->workgroups()->where("group.id",$gid)->first();
		if(!$workgroup){
			throw new \Exception("You don't have this workgoup",config("exceptions.USER_NO_WORKGROUP"));
		}
		if(empty($workgroup->pid)){
			throw new \Exception("The work group of the system can't be deleted",config("exceptions.WORKGROUP_ROOT_NO"));
		}
		//工作组不能有子工作组
		if($this->workgroupRepository->getInfos([["user_id",$user->primary_id],["pid",$gid]],[],['*'],true)){
			throw new \Exception("Workgroups cannot have child workgroups",config("exceptions.WORKGROUP_HAS_CHILD"));
		}
		//工作组不能属于任何一个子账号
		if($workgroup->users()->where("user_id","!=",$user->primary_id)->first()){
			throw new \Exception("A sub account owns this workgroup",config("exceptions.WORKGROUP_BELONGS_CHILD"));
		}
		//工作组中不能有设备
		if($this->deviceRepository->getInfos([["group_id",$gid]],[],["*"],true)){
			throw new \Exception("No devices in workgroup",config("exceptions.WORKGROUP_HAS_DEVICE"));
		}
		DB::transaction(function() use ($workgroup,$user){
			$workgroup->delete();
			$user->workgroups()->detach($workgroup->id);
		});
		
		return [];
	}

}