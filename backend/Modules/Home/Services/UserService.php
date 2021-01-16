<?php
namespace Modules\Home\Services;

use App\Services\BaseService;
use App\Repositories\CacheRepository;
use Modules\Home\Repositories\UserRepository;
use Modules\Home\Repositories\WorkgroupRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Mail;


class UserService extends BaseService{
	const ENCRYPTKEY = 'cloudnetlot';
	private $userRepository;
	private $cacheRepository;
	private $workgroupRepository;

	public function __construct(UserRepository $userRepository,CacheRepository $cacheRepository,WorkgroupRepository $workgroupRepository){
		$this->userRepository = $userRepository;
		$this->cacheRepository = $cacheRepository;
		$this->workgroupRepository = $workgroupRepository;
	}

	//注册用户
	public function register($params){
		$time = Carbon::now()->timestamp;
		$data = [
			"username" => array_get($params,"username"),
			"password" => bcrypt(array_get($params,"password")),
			"mq_password" => array_get($params,"password"),
			"nickname" => array_get($params,"nickname","cnl" . getStr(12,"abcdefghijklmnopqrstuvwxyz")),
			"email" => array_get($params,"email",""),
			"phonecode" => array_get($params,"phonecode"),
			"phone" => array_get($params,"phone"),
			"level" => config("public.user.level.normal"),
			"area" => !empty(array_get($params,"area")) ? array_get($params,"area") : "0",
			"address" => array_get($params,"address",""),
			"admin_id" => "1",
			"timeZone" => config('public.default.timeZone'),
			"isSummerTime" => config('public.default.isSummerTime'),
			"created_at" => $time,
			"updated_at" => $time
		];
		$workgroup = array_merge(config("public.workgroup.default"),[
			"updated_at" => $time,
			"created_at" => $time
		]);
		DB::beginTransaction();
		$user = $this->userRepository->makeModel()->create($data);
		$workgroup["user_id"] = $user->id;
		$workgroupID = $this->workgroupRepository->add($workgroup);
		$user->workgroups()->attach($workgroupID,["created_at" => $time,"updated_at" => $time]);
		if($user && $workgroupID){
			DB::commit();
		}else{
			DB::rollback();
			throw new \Exception("Failure",config("exceptions.MYSQL_EXEC_ERROR"));
		}
		return ["uid" => $user->id];
	}

	//发送找回密码邮件
	public function sendPasswordMail($params){
		$username = array_get($params,'username');
		$email = array_get($params,'email');
		$lang = array_get($params,'lang','zh-cn');
		if(!($user = $this->userRepository->getInfos([['username',$username]],[],['*'],true))){
			throw new \Exception("The user is not exists",config('exceptions.USER_NO_EXISTS'));		
		}
		if($email != $user->email){
			throw new \Exception("The email of the user error",config('exceptions.USER_EMAIL_ERROR'));	
		}

		$time = Carbon::now();
		$urlParams = [
			'username' => $username,
			'email' => $email,
			'lang' => $lang,
			'time' => $time->copy()->addSeconds(config('public.mailtopassword.expire_in'))->timestamp
		];

		$url = config("public.mailtopassword.url") . "?text=" . $this->innerDYEncrypt(http_build_query($urlParams)) . "&lang=" . $lang;
		$trans = config('public.mailtopassword.trans.' . $lang);
		$subject = array_pull($trans,'subject');
		$trans['lang2'] = sprintf($trans['lang2'],floor(config('public.mailtopassword.expire_in')/60));

		Mail::send('emails.findpassword',[
			'username' => $username,
			'url' => $url,
			'time' => convUnixToZoneGm($time->timestamp,$user->timeZone,$user->isSummerTime),
			'body' => $trans
		],function($message) use ($email,$subject){
			$message->to($email)->subject($subject);
		});
		return [];
	}

	//校验找回密码邮件链接的有效性
	public function checkPasswordMail($params){
		$content = array_get($params,"content");
		$time = Carbon::now();
		parse_str($this->innerDYDecrypt($content),$content);
		$user = $this->_checkPasswordMail($content,$time);
		return [];
	}

	//重置密码
	public function resetPassword($params){
		$content = array_get($params,"content");
		$password = array_get($params,"password");
		$time = Carbon::now();
		parse_str($this->innerDYDecrypt($content),$content);
		$user = $this->_checkPasswordMail($content,$time);
		$user->password = bcrypt($password);
		$user->updated_at = $time->timestamp;
		$user->save();
		$this->cacheRepository->setPasswordEmailStatus($user->id . $content['time'],1,config('public.mailtopassword.expire_in'));
		return [];
	}

	//修改用户信息
	public function save($user,$params){
		$data = array_intersect_key($params,[
			"nickname" => "",
			"phonecode" => "",
			"phone" => "",
			"email" => ""
		]);

		array_walk($data, function($value,$key) use ($user){
			if(!empty($value))
				$user->{$key} = $value;
		});
		$user->updated_at = Carbon::now()->timestamp;
		$user->save();
		return [];
	}

	//修改用户密码
	public function savePassword($user,$params){
		$oldPassword = array_get($params,"old_password");
		$newPassword = array_get($params,"new_password");
		if(!Hash::check($oldPassword,$user->password)){
			throw new \Exception('The old password error',config('exceptions.USER_PASSWORD_ERROR'));
		}
		if(!$user->update(['password' => bcrypt($newPassword),'updated_at' => Carbon::now()->timestamp])){
			throw new \Exception('Failure',config('exceptions.MYSQL_EXEC_ERROR'));
		}
		auth('cloudnetlot')->invalidate();
		return [];
	}

	/*发送邮件中连接地址的加密函数*/
    public function innerDYEncrypt($encryptstr){
        return  urlencode($this->innerDYEncryptSubfun($encryptstr));
    }

    private function innerDYEncryptSubfun($encryptstr){
        srand((double)microtime()*1000000);
        $encrypt_key = md5(rand(0,32000));
        $ctr=0; $tmpstr = "";
        for ($i=0;$i<strlen($encryptstr);$i++){
            if ($ctr==strlen($encrypt_key)) $ctr=0;
            $tmpstr.= substr($encrypt_key,$ctr,1) .
            (substr($encryptstr,$i,1) ^ substr($encrypt_key,$ctr,1));
            $ctr++;
        }
        $returninfo = base64_encode(self::keyED($tmpstr,self::ENCRYPTKEY));
        if (strrpos($returninfo,"/") or strrpos($returninfo,'') or strrpos($returninfo,'+'))
            return $this->innerDYEncryptSubfun($encryptstr);
        return $returninfo;

    }

    /*发送邮件中连接地址的解密函数*/
    public function innerDYDecrypt($decryptstr){
        $decryptstr = urldecode($decryptstr);
        $decryptstr = $this->keyED(base64_decode($decryptstr),self::ENCRYPTKEY);
        $tmpstr = "";
        for ($i=0;$i<strlen($decryptstr);$i++){
            $md5 = substr($decryptstr,$i,1);
            $i++;
            $tmpstr.= (substr($decryptstr,$i,1) ^ $md5);
        }
        return  $tmpstr;
    }

    /*加密函数内部调用函数*/
    private function keyED($txt,$encrypt_key) {
        $encrypt_key = md5($encrypt_key);
        $ctr=0;
        $tmp = "";
        for ($i=0;$i<strlen($txt);$i++) {
        	if ($ctr==strlen($encrypt_key)) $ctr=0;
        	$tmp.= substr($txt,$i,1) ^ substr($encrypt_key,$ctr,1);
        	$ctr++;
        }
        return $tmp;
    }

    private function _checkPasswordMail($content,$time){
    	if(!isset($content["username"]) || !isset($content["email"]) || !isset($content["time"]) || !is_numeric($content["time"])){
			throw new \Exception("Param is invalid",config("exceptions.PARAMS_INVALID"));
		}

		if(Carbon::createFromTimestamp($content["time"])->lt($time)){
			throw new \Exception("Url expire",config("exceptions.URL_EXPIRE"));
		}

		$user = $this->userRepository->getInfos([["username",$content["username"]]],[],["*"],true);
		if(!$user){
			throw new \Exception("The user is not exists",config("exceptions.USER_NO_EXISTS"));		
		}
		if($user->email != $content["email"]){
			throw new \Exception("The email of the user is error",config("exceptions.USER_EMAIL_ERROR"));
		}
		if($this->cacheRepository->getPasswordStatus($user->id . $content["time"])){
			throw new \Exception("Url invalid",config("exceptions.URL_INVALID"));
		}
		return $user;
    }

    //获取子账号列表
    public function getChild($user,$params){
    	$pageIndex = array_get($params,"pageIndex",1);
    	$pageOffset = array_get($params,"pageOffset",10);
    	$keyword = array_get($params,"keyword",NULL);
    	$sortkey = array_get($params,"sortkey","created_at");
    	$sort = array_get($params,"sort","desc");
    	$status = array_get($params,"status",2);
    	$role = array_get($params,"role","");

    	if(!$user->is_primary){
			throw new \Exception("No permission",config("exceptions.NO_PERMISSTION"));
		}

		$condition = [
			["pid",$user->id]
		];

		if(in_array($status,[0,1])){
			array_push($condition,["status",$status]);
		}

		if(!empty($role)){
			array_push($condition,["level",$role]);
		}
    	
    	if(!is_null($keyword)){
    		$keyword = '%' . $keyword . '%';
    		array_push($condition,[function($query)use($keyword){
    			$query->where([
    				["username","like",$keyword],
    				["nickname","like",$keyword,"or"],
    				["email","like",$keyword,"or"]
    			]);
    		}]);
    	}
    	$sortkey = $sortkey == "role" ? "level" : $sortkey;
    	$order = [$sortkey,$sort];
    	$page = [$pageIndex,$pageOffset];

    	$total = $this->userRepository->makeModel()->where($condition)->count();
    	if(($pageIndex-1) * $pageOffset > $total){
    		$list = [];
    	}else{
    		$list = $this->userRepository->getInfos($condition,[],['*'],false,$order,$page)->map(function($value){
				return [
					"uid" => $value->id,
					"username" => $value->username,
        			"nickname" => $value->nickname,
        			"pid" => $value->pid,
        			"email" => $value->email,
        			"phonecode" => $value->phonecode,
        			"phone" => $value->phone,
        			"level" => $value->level,
        			"status" => $value->status,
        			"created_at" => convDateToZoneGm($value->created_at->toDateTimeString(),$value->timeZone,$value->isSummerTime),
        			"updated_at" => convDateToZoneGm($value->updated_at->toDateTimeString(),$value->timeZone,$value->isSummerTime)
				];
			});
    	}
    	return [
    		"total" => $total,
    		"list" => $list
    	];
    }

    //创建子账号
    public function addChild($user,$params){
    	if(!$user->is_primary){
			throw new \Exception("No permission",config("exceptions.NO_PERMISSTION"));
		}
		$gids = array_get($params,"gids");
		//检测工作组树
		$allWorkgroup = $this->workgroupRepository->getInfos([["user_id",$user->id]]);
		$allWorkgroupTree = rankSort($allWorkgroup->toArray(),"id","pid","child","id");
		$this->checkTree($allWorkgroupTree,$gids);
		$time = Carbon::now()->timestamp;
		$data = [
			"username" => array_get($params,"username"),
			"password" => bcrypt(array_get($params,"password")),
			"nickname" => array_get($params,"nickname","cnl" . getStr(12,"abcdefghijklmnopqrstuvwxyz")),
			"pid" => $user->id,
			"email" => array_get($params,"email"),
			"phonecode" => array_get($params,"phonecode"),
			"phone" => array_get($params,"phone"),
			"level" => array_get($params,"role") == 1 ? config("public.user.level.child_admin") : config("public.user.level.child_guest"), 
			"area" => !empty(array_get($params,"area")) ? array_get($params,"area") : "0",
			"address" => array_get($params,"address",""),
			"status" => array_get($params,"enable") == 1 ? config("public.user.status.enabled") : config("public.user.status.disabled"),
			"admin_id" => "1",
			"timeZone" => config('public.default.timeZone'),
			"isSummerTime" => config('public.default.isSummerTime'),
			"created_at" => $time,
			"updated_at" => $time
		];
		$attaches = array_combine($gids,array_fill(0,count($gids),["created_at" => $time,"updated_at" => $time]));
		try{
			DB::beginTransaction();
			$user = $this->userRepository->makeModel()->create($data);
			$user->workgroups()->attach($attaches);
			DB::commit();
			return ["uid" => $user->id];
		}catch(\Exception $e){
			DB::rollback();
			throw new \Exception($e->getMessage(),config("exceptions.MYSQL_EXEC_ERROR"));
		}
    }

    private function checkTree($trees,$datas){
    	$res = false;
    	sort($datas);
    	$node = array_shift($datas);
    	foreach($trees as $tree){
    		if($node == $tree["id"]){
    			$res = true;
    			$trees = $tree["child"];
    			break;
    		}
    	}
    	if(!$res){
    		throw new \Exception("Group node if not a tree",config("exceptions.WORKGROUP_NODE_NO_TREE"));
    	}
    	if(!empty($datas)){
    		$this->checkTree($trees,$datas);
    	}
    }

    //修改子账号信息
    public function saveChild($user,$params){
    	if(!$user->is_primary){
			throw new \Exception("No permission",config("exceptions.NO_PERMISSTION"));
		}
		$uid = array_get($params,"uid");
		$gids = array_get($params,"gids",[]);
		unset($params["uid"]);
		if(empty($params)){
			throw new \Exception("Params is invalid",config("exceptions.PARAMS_INVALID"));
		}

		$child = $this->userRepository->getInfos([["id",$uid],["pid",$user->id]],[],["*"],true);
		if(!$child){
			throw new \Exception("The user isn't exists",config("exceptions.USER_NO_EXISTS"));
		}

		foreach($params as $key => $param){
			if(in_array($key,["nickname","email","phonecode","phone"])){
				$child->$key = $param;
			}elseif($key == "role"){
				$child->level = $param == 1 ? config("public.user.level.child_admin") : config("public.user.level.child_guest");
			}elseif($key == "enable"){
				$child->status = $param == 1 ? config("public.user.status.enabled") : config("public.user.status.disabled");
			}else{

			}
		}

		$time = Carbon::now()->timestamp;
		$child->updated_at = $time;
		try{
			DB::beginTransaction();
			$child->save();
			if(!empty($gids)){
				//检测工作组树
				$allWorkgroup = $this->workgroupRepository->getInfos([["user_id",$user->id]]);
				$allWorkgroupTree = rankSort($allWorkgroup->toArray(),"id","pid","child","id");
				$this->checkTree($allWorkgroupTree,$gids);
				$attaches = array_combine($gids,array_fill(0,count($gids),["updated_at" => $time]));
				$child->workgroups()->sync($attaches);
			}
			DB::commit();
			return [];
		}catch(\Exception $e){
			DB::rollback();
			throw new \Exception($e->getMessage(),$e->getCode());
		}	
    }

    //批量重置子账号密码
    public function resetPasswordForChild($user,$params){
    	if(!$user->is_primary){
			throw new \Exception("No permission",config("exceptions.NO_PERMISSTION"));
		}
		$uids = array_get($params,"uids");
		$password = array_get($params,"password",config("public.default.password"));
		$users = $this->userRepository->getInfos([["pid",$user->id],[function($query)use($uids){
			$query->whereIn("id",$uids);
		}]]);
		if(count($uids) != $users->count()){
			throw new \Exception("There is an account that doesn't belong to you",config("exceptions.USER_NO_EXISTS"));	
		}
		$time = Carbon::now()->timestamp;
		$rs = $this->userRepository->save([
			"password" => bcrypt($password),
			"updated_at" => $time
		],[["pid",$user->id],[function($query)use($uids){
			$query->whereIn("id",$uids);
		}]]);
		if($rs){
			return [];
		}else{
			throw new \Exception("Failure",config("exceptions.MYSQL_EXEC_ERROR"));
		}
    }

    //批量删除子账号
    public function deleteChilds($user,$params){
    	if(!$user->is_primary){
			throw new \Exception("No permission",config("exceptions.NO_PERMISSTION"));
		}
		$uids = array_get($params,"uids");
		$users = $this->userRepository->getInfos([["pid",$user->id],[function($query)use($uids){
			$query->whereIn("id",$uids);
		}]]);
		if(count($uids) != $users->count()){
			throw new \Exception("There is an account that doesn't belong to you",config("exceptions.USER_NO_EXISTS"));	
		}
		try{
			DB::beginTransaction();
			$this->userRepository->delete([["pid",$user->id],[function($query)use($uids){
				$query->whereIn("id",$uids);
			}]]);
			$users->each(function($user){
				$user->workgroups()->detach();
			});
			DB::commit();
			return [];
		}catch(\Exception $e){
			DB::rollback();
			throw new \Exception("Failure",config("exceptions.600102100"));
		}
    }

    public function countChilds($user){
    	if(!$user->is_primary){
			throw new \Exception("No permission",config("exceptions.NO_PERMISSTION"));
		}
		$counts = $this->userRepository->countChilds($user->id);
					
		$total = [
			"all" => 0,
			"enabled" => 0,
			"disabled" => 0
		];

		$counts->each(function($value)use(&$total){
			if($value->status == config("public.user.status.enabled")){
				$total["enabled"] = $value->total;
			}
			if($value->status == config("public.user.status.disabled")){
				$total["disabled"] = $value->total;
			}
		});
		$total["all"] = $total["enabled"] + $total["disabled"];
		return $total;
    }

}