<?php
namespace Modules\Home\Services;

use App\Services\BaseService;
use App\Repositories\CacheRepository;
use Modules\Home\Repositories\WorkgroupRepository;
use Modules\Home\Repositories\DeviceRepository;
use Modules\Home\Repositories\TopgraphyRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

/**
 * 主账号关联了所有工作组，子账号只关联自己有关的工作组
 */

class WorkgroupService extends BaseService{
	protected $workgroupRepository;
	protected $deviceRepository;
	protected $cacheRepository;
	protected $topgraphyRepository;

	public function __construct(WorkgroupRepository $workgroupRepository, DeviceRepository $deviceRepository,CacheRepository $cacheRepository,TopgraphyRepository $topgraphyRepository){
		$this->workgroupRepository = $workgroupRepository;
		$this->deviceRepository = $deviceRepository;
		$this->cacheRepository = $cacheRepository;
		$this->topgraphyRepository = $topgraphyRepository;
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
				"pid" => !empty($value->pid) ? $hashids->encodeHash($value->pid) : 0
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
		$info = $user->workgroups()->with("parent")->where("group.id",$gid)->first();
		$hashids = app("Hashids");
		if($info){
			$info->gid = $info->id;
			$info->pid = !empty($info->pid) ? $hashids->encodeHash($info->pid) : 0;
			$info->pname = !empty($info->parent) ? $info->parent->name : "";
			$info->created_at = convUnixToZoneGm($info->created_at,$user->timeZone,$user->isSummerTime);
			$info->updated_at = convUnixToZoneGm($info->updated_at,$user->timeZone,$user->isSummerTime);
			return $info->makeHidden(["id","pivot","parent"]);
		}else{
			throw new \Exception("You don't have this workgoup",config("exceptions.USER_NO_WORKGROUP"));
		}
	}

	//模板文件下载
	public function downloadConfig($user,$params){
		return response()->download(storage_path("files") . '/configtpl.txt', 'configtpl.txt');
	}

	//上传配置文件
	public function uploadConfig($user,$params){
		$file = array_get($params,"file");
		$this->checkUserPermission($user,config("public.user.level.child_admin"));
		if(!$user->is_primary){
			throw new \Exception("No permission",config("exceptions.NO_PERMISSTION"));
		}
		if($file->isValid()){
			$realPath = $file->getRealPath();
			$fid = generateFid();
			$filename = $fid . "." . $file->getClientOriginalExtension();
			$ftpService = Storage::disk('ftp');
			$dir = "deviceconfig/" . $user->primary_id;
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
		$this->checkUserPermission($user,config("public.user.level.child_admin"),$gid);
		$groupInfo = $this->workgroupRepository->getInfos([["user_id",$user->id],["id",$gid]],[],['*'],true);
		if(!$groupInfo){
			throw new \Exception("You don't have this workgoup",config("exceptions.USER_NO_WORKGROUP"));
		}
		if($groupInfo->level +1 >= config("public.workgroup.level")){
			throw new Exception("Maximum depth reached",config("exceptions.MAX_DEPTH"));		
		}
		$ftpService = Storage::disk('ftp');
		$ftpDir = "deviceconfig/" . $user->primary_id;
		if(!empty($configID) && !$ftpService->exists($ftpDir . "/" . $configID . ".txt")){
			throw new \Exception("The config file is not exists",config("exceptions.FILE_NO"));
		}
		$code = $this->workgroupRepository->generateChildCode($groupInfo->user_id,$groupInfo->id);
		DB::beginTransaction();
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
		//创建拓扑根节点信息
		$topRoot = config("topgraphy.network");
		$rs1 = $user->topgraphy()->firstOrCreate([
			"uid" => $user->primary_id,
			"mac" => $topRoot["mac"],
			"pid" => $topRoot["pid"],
			"content" => $topRoot["content"],
			"is_edit" => $topRoot["is_edit"],
			"is_virture" => $topRoot["is_virture"],
			"point_x" => $topRoot["point_x"],
			"point_y" => $topRoot["point_y"],
			"group_id" => $groupID,
			"created_at" => $time,
			"updated_at" => $time
		]);
		//绑定关系
		$rs2 = $user->workgroups()->attach($groupID,["created_at" => $time,"updated_at" => $time]);
		if($groupID && $rs1 && $rs2 !== false){
			DB::commit();
			//清除无效配置文件
			if(!empty($configID)){
				$files = $ftpService->files($ftpDir);
				if(!empty($files)){
					$configIDs = array_merge([$configID],$user->config_ids);
					foreach ($files as $file) {
						$pathArr = explode("/",$file);
						$fileArr = explode(".",$pathArr[count($pathArr)-1]);
						if(!in_array($fileArr[0],$configIDs)){
							$rmFileNames[] = $file;
						}
					}
					if(!empty($rmFileNames)){
						$ftpService->delete($rmFileNames);
					}
				}
			}
			//todo发命令应用配置
			return ["gid" => $groupID];
		}else{
			DB::rollBack();
			throw new \Exception("Failure",config("exceptions.MYSQL_EXEC_ERROR"));
		}
	}

	//修改工作组
	public function saveWorkgroup($user,$params){
		$gid = array_get($params,"gid");
		$configID = array_get($params,"config_id","");
		$this->checkUserPermission($user,config("public.user.level.child_admin"),$gid);
		$groupInfo = $this->workgroupRepository->getInfos([["user_id",$user->id],["id",$gid]],[],['*'],true);
		if(!$groupInfo){
			throw new \Exception("You don't have this workgoup",config("exceptions.USER_NO_WORKGROUP"));
		}
		$ftpService = Storage::disk('ftp');
		$ftpDir = "deviceconfig/" . $user->primary_id;
		if(!empty($configID) && !$ftpService->exists($ftpDir . "/" . $configID . ".txt")){
			throw new \Exception("The config file is not exists",config("exceptions.FILE_NO"));
		}
		if(empty($groupInfo->pid)){
			$data = array_intersect_key($params,[
				"config_id" => "",
				"auto" => 0
			]);
		}else{
			$data = array_intersect_key($params,[
				"name" => "",
				"description" => "",
				"config_id" => "",
				"auto" => 0
			]);
		}
		if(empty($data)){
			throw new \Exception("Params is invalid",config("exceptions.PARAMS_INVALID"));
		}
		$data["updated_at"] = Carbon::now()->timestamp;
		$rs = $this->workgroupRepository->save($data,[
			"id" => $gid,
			"user_id" => $user->primary_id
		]);
		
		if($rs){
			//清除无效配置文件
			if(!empty($configID)){
				$configIDs = array_merge([$configID],$user->config_ids);
				$files = $ftpService->files($ftpDir);
				if(!empty($files)){
					foreach ($files as $file) {
						$pathArr = explode("/",$file);
						$fileArr = explode(".",$pathArr[count($pathArr)-1]);
						if(!in_array($fileArr[0],$configIDs)){
							$rmFileNames[] = $file;
						}
					}
					if(!empty($rmFileNames)){
						$ftpService->delete($rmFileNames);
					}
				}
			}
			//todo发命令应用配置
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
		
		DB::transaction(function() use ($workgroup,$user,$gid){
			$workgroup->delete();
			$user->workgroups()->detach($workgroup->id);
			//删除工作组下拓扑图信息
			$this->topgraphyRepository->delete([
				["uid",$user->primary_id],
				["group_id",$gid]
			]);
			//清除相关节点下拓扑图缓存信息
			$this->cacheRepository->deleteTopgraphy($user->primary_id,[$gid]);
		});
		
		return [];
	}

}