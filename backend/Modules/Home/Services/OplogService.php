<?php
namespace Modules\Home\Services;

use App\Services\BaseService;
use App\Repositories\CacheRepository;
use Modules\Home\Repositories\CommandRepository;
use Modules\Home\Repositories\MessageReadRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OplogService extends BaseService{
	protected $commandRepository;
	protected $messageReadRepository;
	protected $cacheRepository;

	public function __construct(CommandRepository $commandRepository,MessageReadRepository $messageReadRepository,CacheRepository $cacheRepository){
		$this->commandRepository = $commandRepository;
		$this->messageReadRepository = $messageReadRepository;
		$this->cacheRepository = $cacheRepository;
	}

	public function statisticsNotices($user,$params){
		$status = array_get($params,"status",0);
		$conditions = [];
		if($user->is_primary){//主账号
			$userIDs = $user->childs->pluck("id")->toArray();
		}
		$userIDs[] = $user->id;

		if(!empty($status)){
			array_push($conditions,["status",$status]);
		}
		array_push($conditions,[function($query)use($userIDs){
				$query->whereIn("user_id",$userIDs);
			}]);
		return $this->commandRepository->statics($conditions);
	}

	public function getList($user,$params){
		$pageIndex = array_get($params,"pageIndex",1);
		$pageOffset = array_get($params,"pageOffset",10);
		$status = array_get($params,"status",0);
		$date = array_get($params,"date","");
		$sortkey = array_get($params,"sortkey","created_at");
		$sort = array_get($params,"sort","desc");

		if($user->is_primary){//主账号
			$ids = $user->childs->pluck("id")->toArray();
			$ids[] = $user->id;
			$conditions = [
				[function($query)use($ids){
					$query->whereIn("user_id",$ids);
				}]
			];
		}else{//子账号
			$conditions = [
				["user_id",$user->id]
			];
		}

		if(!empty($status))
			array_push($conditions,["status",$status]);
		if(!empty($date)){
			$minTime = Carbon::createFromFormat("Y-m-d H:i:s",$date . " 00:00:00")->subMinutes($user->timeZone * 60)->subMinutes($user->isSummerTime * 60)->timestamp;
			$maxTime = Carbon::createFromFormat("Y-m-d H:i:s",$date . " 23:59:59")->subMinutes($user->timeZone * 60)->subMinutes($user->isSummerTime * 60)->timestamp;
			array_push($conditions,[function($query)use($minTime,$maxTime){
				$query->whereBetween("created_at",[$minTime,$maxTime]);
			}]);
		}

		$total = $this->commandRepository->makeModel()->where($conditions)->count();
    	if(($pageIndex-1) * $pageOffset > $total){
    		$list = [];
    	}else{	
    		$commands = $this->commandRepository->getInfos($conditions,["messageReads" => function($query)use($user){
    			$query->where("user_id",$user->id);
    		}],["*"],false,[$sortkey,$sort],[$pageIndex,$pageOffset]);
			$list = $commands->map(function($value)use($user){
				return [
					"id" => $value->id,
					"user_id" => $value->user_id,
					"username" => $value->user->username,
					"nickname" => $value->user->nickname,
					"dev_mac" => $value->dev_mac,
					"common_id" => $value->comm_id,
					"content" => $value->content,
					"describe" => $value->describe,
					"type" => $value->type,
					"status" => $value->status,
					"readed" => $value->messageReads->isEmpty() ? 0 : 1,
					"created_at" => convUnixToZoneGm($value->created_at,$user->timeZone,$user->isSummerTime)
				];
			});
		}
		return [
			"total" => $total,
			"list" => $list
		];
	}

	public function getInfo($user,$params){
		$id = array_get($params,"id");
		$command = $this->commandRepository->getInfos([["id",$id]],[],['*'],true);
		return [
			"id" => $command->id,
			"user_id" => $command->user_id,
			"username" => $command->user->username,
			"nickname" => $command->user->nickname,
			"common_id" => $command->comm_id,
			"content" => $command->content,
			"describe" => $command->describe,
			"type" => $command->type,
			"status" => $command->status,
			"dev_mac" => $command->dev_mac,
			"dev_ip" => !empty($command->dev_ip) ? $command->dev_ip : "",
			"dev_type" => !empty($command->dev_type) ? $command->dev_type : "",
			"dev_name" => !empty($command->dev_name) ? $command->dev_name : "",
			"version" => !empty($command->dev_version) ? $command->dev_version : "",
			"created_at" => convUnixToZoneGm($command->created_at,$user->timeZone,$user->isSummerTime)
		];
	}

	public function readedMessage($user,$params){
		$rs = false;
		$time = Carbon::now()->timestamp;
		$ids = array_get($params,"ids",[]);
		$conditions = [["user_id",$user->id]];
		if(!empty($ids)){
			array_push($conditions,[function($query)use($ids){
				$query->whereIn("id",$ids);
			}]);
		}
		$commands = $this->commandRepository->getInfos($conditions,["messageReads" => function($query)use($user){
			$query->where("user_id",$user->id);
		}]);

		if($commands->isEmpty() || (!empty($ids) && count($ids) != $commands->count())){
			throw new \Exception("Commands is not exists",config("exceptions.COMM_NO"));
		}
		$data = [];
		$commands->each(function($command)use(&$data,$time,$user){
			if($command->messageReads->isEmpty()){
				$data[] = [
					"comm_id" => $command->id,
					"user_id" => $user->id,
					"created_at" => $time,
					"updated_at" => $time
				];
			}
		});

		if(!empty($data)){
			$rs = $this->messageReadRepository->addAll($data);
			$cacheNums = $this->cacheRepository->getUserOplogNums($user->id);
			if(isset($cacheNums["unreads"]) && $cacheNums["unreads"] > 0){
				$this->cacheRepository->incrOplogNums($user->id,["reads" => 1,"unreads" => -1]);
			}
		}else{
			$rs = true;
		}
		if(!$rs){
			throw new \Exception("Failure",config("exceptions.MYSQL_EXEC_ERROR"));
		}else{
			return [];
		}
	}

}