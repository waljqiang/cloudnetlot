<?php
namespace Modules\Admin\Services;

use App\Services\BaseService;
use Modules\Admin\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Mail;

class DevelopService extends BaseService{
	private $userRepository;

	public function __construct(UserRepository $userRepository){
		$this->userRepository = $userRepository;
	}

	public function getList($admin,$params){
		$keyword = array_get($params,"keyword",NULL);
		$sortKey = array_get($params,"sortKey","created_at");
		$sort = array_get($params,"sort","desc");
		$pageIndex = array_get($params,"pageIndex",1);
		$pageOffset = array_get($params,"pageOffset",10);
		$conditions = [
			["admin_id",$admin->id]
		];
		$has = [];
		if(!is_null($keyword)){
			array_push($conditions,["username",$keyword]);
			$has = [
				["name",$keyword],
				["enterprise","=",$keyword,"or"]
			];
		}	
		$develops = $this->userRepository->getDevelops($conditions,$has,[$sortKey,"desc"],[$pageIndex,$pageOffset]);
		$total = $develops->count();
		$list = $develops->map(function($value){
			$develop = $value->develop;
			return [
				"user_id" => app("Hashids")->encodeHash($value->id),
				"username" => $value->username,
				"nickname" => $value->nickname,
				"email" => $value->email,
				"phonecode" => $value->phonecode,
				"phone" => $value->phone,
				"name" => $develop->name,
				"enterprise" => $develop->enterprise,
				"appid" => $develop->appid,
				"secret" => $develop->secret,
				"status" => $develop->aud_status,
				"created_at" => convUnixToZoneGm($develop->created_at,$value->timeZone,$value->isSummerTime)
			];
		});
		$list = $sort == "desc" ? $list->sortByDesc($sortKey) : $list->sortBy($sortKey);
		$list = $list->values()->forPage($pageIndex,$pageOffset);
		return [
			"total" => $total,
			"list" => $list
		];
	}

	public function getInfo($admin,$params){
		$uid = array_get($params,"uid");
		if(!($info = $this->userRepository->getDevelop([["admin_id",$admin->id],["id",$uid]]))){
			throw new \Exception("The user is not exists",config("exceptions.USER_NO_EXISTS"));
		}
		$develop = $info->develop;

		return [
			"uid" => $info->id,
			"username" => $info->username,
			"nickname" => $info->nickname,
			"email" => $info->email,
			"phonecode" => $info->phonecode,
			"phone" => $info->phone,
			"timeZone" => $info->timeZone,
			"isSummerTime" => $info->isSummerTime,
			"name" => $develop->name,
			"idcard" => $develop->idcard,
			"enterprise" => $develop->enterprise,
			"enterprise_des" => $develop->enterprise_des,
			"enterprisecode" => $develop->enterprisecode,
			"appid" => $develop->appid,
			"secret" => $develop->secret,
			"status" => $develop->aud_status,
			"created_at" => convUnixToZoneGm($develop->created_at,$info->timeZone,$info->isSummerTime),
		];
	}

	public function approve($admin,$params){
		$uid = array_get($params,"uid");
		$enable = array_get($params,"enable",1);
		$lang = array_get($params,"lang","zh-cn");
		$time = Carbon::now()->timestamp;
		if(!$user = $this->userRepository->getDevelop([["admin_id",$admin->id],["id",$uid]])){
			throw new \Exception("The user is not exists",config("exceptions.USER_NO_EXISTS"));
		}
		if($user->status != config("public.user.status.enabled")){
			throw new \Exception("User is disabled",config("exceptions.USER_STATUS_NO_ALLOWED"));
		}
		$develop = $user->develop;
		if($develop->aud_status != 1){
			throw new \Exception("The user is not in the approval scope",config("exceptions.USER_NO_AGREE"));
		}
		$appid = md5($user->id . $time);
		$secret = str_random(40);
		$develop->appid = $appid;
		$develop->secret = $secret;
		$develop->updated_at = $time;
		$develop->aud_status = $enable == 1 ? 3 : 2;
		if(!$develop->save())
			throw new \Exception("Failure",config("exceptions.MYSQL_EXEC_ERROR"));
		//todo 发邮件通知用户
		$email = $user->email;
		$trans = config("public.mailtodevelop.trans." . $lang);
		$trans["lang1"] = sprintf($trans["lang1"],$user->username);
		$trans["lang2"] = sprintf($trans["lang2"],$appid,$secret);
		$subject = array_pull($trans,"subject");
		Mail::send("emails.mailtodevelopwithadmin",[
			"flag" => (boolean)$enable,
			"username" => $user->username,
			"body" => $trans,
			"time" => convUnixToZoneGm($time,$user->timeZone,$user->isSummerTime)
		],function($message) use ($email,$subject){
			$message->to($email)->subject($subject);
		});
		return $enable == 1 ? [
			"appid" => $appid,
			"secret" => $secret
		] : [];
	}

}