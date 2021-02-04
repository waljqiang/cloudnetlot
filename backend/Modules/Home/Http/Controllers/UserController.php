<?php
namespace Modules\Home\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Home\Http\Requests\UserRequest;
use Modules\Home\Services\UserService;
use Modules\Home\Services\OplogService;

class UserController extends Controller{
	protected $userService;

	public function __construct(UserService $userService){
		$this->userService = $userService;
	}

	//注册用户
	public function register(UserRequest $request){
		return $this->userService->register($request->all());
	}

	//获取用户信息
	public function getInfo(Request $request){
		$user = $request->user();
		return [
			"uid" => $user->id,
			"username" => $user->username,
			"nickname" => $user->nickname,
			"pid" => $user->pid,
			"is_primary" => $user->is_primary,
			"email" => $user->email,
			"phonecode" => $user->phonecode,
			"phone" => $user->phone,
			"level" => $user->level,
			"area" => $user->area,
			"address" => $user->address,
			"longitude" => $user->longitude,
			"latitude" => $user->latitude,
			"status" => $user->status,
			"admin_id" => $user->admin_id,
			"timeZone" => $user->timeZone,
			"isSummerTime" => $user->isSummerTime,
			"created_at" => convDateToZoneGm($user->created_at->toDateTimeString(),$user->timeZone,$user->isSummerTime),
			"updated_at" => convDateToZoneGm($user->updated_at->toDateTimeString(),$user->timeZone,$user->isSummerTime)
		];
	}

	//发送找回密码邮件
	public function sendPasswordMail(UserRequest $request){
		return $this->userService->sendPasswordMail($request->all());
	}

	//校验找回密码链接的有效性
	public function checkPasswordMail(UserRequest $request){
		return $this->userService->checkPasswordMail($request->all());
	}

	//重设密码
	public function resetPassword(UserRequest $request){
		return $this->userService->resetPassword($request->all());
	}

	//修改用户信息
	public function save(UserRequest $request){
		return $this->userService->save($request->user(),$request->all());
	}

	//修改用户密码
	public function savePassword(UserRequest $request){
		return $this->userService->savePassword($request->user(),$request->all());
	}

	//获取子账号列表
	public function getChild(UserRequest $request){
		return $this->userService->getChild($request->user(),$request->all());
	}

	//获取子账号信息
	public function getChildInfo(UserRequest $request){
		return $this->userService->getChildInfo($request->user(),$request->all());
	}

	//创建子账号
	public function addChild(UserRequest $request){
		return $this->userService->addChild($request->user(),$request->all());
	}

	//修改子账号信息
	public function saveChild(UserRequest $request){
		return $this->userService->saveChild($request->user(),$request->all());
	}

	//批量重置子账号密码
	public function resetPasswordForChild(UserRequest $request){
		return $this->userService->resetPasswordForChild($request->user(),$request->all());
	}

	//批量删除子账号
	public function deleteChilds(UserRequest $request){
		return $this->userService->deleteChilds($request->user(),$request->all());
	}

	//按状态统计子账号数量
	public function countChilds(Request $request){
		return $this->userService->countChilds($request->user());
	}

}