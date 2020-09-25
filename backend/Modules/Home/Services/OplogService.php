<?php
namespace Modules\Home\Services;

use App\Services\BaseService;
use App\Repositories\CacheRepository;
use Modules\Home\Repositories\CommandRepository;
use Modules\Home\Repositories\MessageReadRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OplogService extends BaseService{
	private $commandRepository;
	private $messageReadRepository;
	private $cacheRepository;

	public function __construct(CommandRepository $commandRepository,MessageReadRepository $messageReadRepository,CacheRepository $cacheRepository){
		$this->commandRepository = $commandRepository;
		$this->messageReadRepository = $messageReadRepository;
		$this->cacheRepository = $cacheRepository;
	}

	public function getList($user,$params){
		$pageIndex = array_get($params,"pageIndex",1);
		$pageOffset = array_get($params,"pageOffset",10);
		$status = array_get($params,"status",0);
		$date = array_get($params,"date","");
		$sortkey = array_get($params,"sortkey","created_at");
		$sort = array_get($params,"sort","desc");
		$conditions = [
			["user_id",$user->id]
		];

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
    		$hashids = app("Hashids");
			$list = $commands->map(function($value)use($user,$hashids){
				return [
					"user_id" => $hashids->encodeHash($value->user_id),
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

	public function readedMessage($user,$params){
		$rs = false;
		$time = Carbon::now()->timestamp;
		$commIds = array_get($params,"comm_id",[]);
		$conditions = [["user_id",$user->id]];
		if(!empty($commIds)){
			array_push($conditions,[function($query)use($commIds){
				$query->whereIn("comm_id",$commIds);
			}]);
		}
		$commands = $this->commandRepository->getInfos($conditions,["messageReads" => function($query)use($user){
			$query->where("user_id",$user->id);
		}]);

		if($commands->isEmpty() || (!empty($commIds) && count($commIds) != $commands->count())){
			throw new \Exception("Commands is not exists",config("exceptions.COMM_NO"));
		}
		$data = [];
		$commands->each(function($command)use(&$data,$time,$user){
			if($command->messageReads->isEmpty()){
				$data[] = [
					"comm_id" => $command->comm_id,
					"user_id" => $user->id,
					"created_at" => $time,
					"updated_at" => $time
				];
			}
		});
		if(!empty($data)){
			$rs = $this->messageReadRepository->addAll($data);
			$this->cacheRepository->incrOplogNums($user->id,["reads" => 1]);
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