<?php
namespace Modules\Develop\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Develop\Http\Requests\UserRequest;
use Modules\Develop\Services\UserService;

class UserController extends Controller{
	private $userService;

	public function __construct(UserService $userService){
		$this->userService = $userService;
	}

	//获取用户信息
	public function getInfo(Request $request){
		$user = $request->user();
		$develop = $user->develop;
		return [
			"uid" => $user->id,
			"username" => $user->username,
			"nickname" => $user->nickname,
			"pid" => $user->pid,
			"email" => $user->email,
			"phonecode" => $user->phonecode,
			"phone" => $user->phone,
			"level" => $user->level,
			"area" => $user->area,
			"address" => $user->address,
			"longitude" => $user->longitude,
			"latitude" => $user->latitude,
			"status" => $develop ? $develop->aud_status : 0,
			"admin_id" => $user->admin_id,
			"timeZone" => $user->timeZone,
			"isSummerTime" => $user->isSummerTime,
			"created_at" => convDateToZoneGm($user->created_at->toDateTimeString(),$user->timeZone,$user->isSummerTime),
			"updated_at" => convDateToZoneGm($user->updated_at->toDateTimeString(),$user->timeZone,$user->isSummerTime),
			"develop" => $develop && $develop->aud_status == 3 && !empty($develop) ? [
				"appid" => $develop->appid,
				"secret" => $develop->secret,
				"name" => $develop->name,
				"idcard" => $develop->idcard,
				"enterprise" => $develop->enterprise,
				"enterprise_des" => $develop->enterprise_des,
				"enterprisecode" => $develop->enterprisecode
			] : []
		];
	}

	//申请成为开发者
	public function develop(UserRequest $request){
		return $this->userService->develop($request->user(),$request->all());
	}

}