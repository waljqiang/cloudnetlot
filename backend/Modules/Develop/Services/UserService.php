<?php
namespace Modules\Develop\Services;

use App\Services\BaseService;
use Modules\Develop\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Mail;

class UserService extends BaseService{
	private $userRepository;

	public function __construct(UserRepository $userRepository){
		$this->userRepository = $userRepository;
	}

	public function develop($user,$params){
		$time = Carbon::now()->timestamp;
		$lang = array_get($params,"lang","zh-cn");
		$data = [
			"user_id" => $user->id,
			"name" => array_get($params,"name"),
			"idcard" => array_get($params,"idcard"),
			"enterprise" => array_get($params,"enterprise"),
			"enterprise_des" => array_get($params,"enterprise_des"),
			"enterprisecode" => array_get($params,"enterprisecode"),
			"aud_status" => 1,
			"created_at" => $time,
			"updated_at" => $time
		];
		if(empty($user->admin)){
			throw new \Exception("User data exception",config("exceptions.ERROR"));
		}
		if(!empty($user->develop)){
			if($user->develop->aud_status == 1){
				throw new \Exception("You are in developing",config("exceptions.USER_DEVELOPING"));
			}elseif($user->develop->aud_status == 3){
				throw new \Exception("You are already developed",config("exceptions.USER_DEVELOPED"));
			}else{

			}
		}

		if(!$user->develop()->updateOrCreate(["user_id" => $user->id],$data)){
			throw new \Exception("Failure",config("exceptions.MYSQL_EXEC_ERROR"));
		}

		//发邮件给管理员
		$email = $user->admin->email;
		$trans = config("public.mailtoadminwithdevelop.trans." . $lang);
		$trans["lang2"] = sprintf($trans["lang2"],$user->username);
		$subject = array_pull($trans,"subject");

		Mail::send("emails.mailtoadminwithdevelop",[
			"username" => $user->admin->username,
			"body" => $trans,
			"time" => convUnixToZoneGm($time,$user->timeZone,$user->isSummerTime)
		],function($message) use ($email,$subject){
			$message->to($email)->subject($subject);
		});
		return [];
	}

}