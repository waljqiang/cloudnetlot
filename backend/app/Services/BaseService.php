<?php
namespace App\Services;

use Carbon\Carbon;
use App\Utils\Msg;

abstract class BaseService{
	protected function checkUserPermission($user,$level,$gids=""){
		//子账号权限校验
		if(!$user->is_primary && $user->level <= $level){
			throw new \Exception("No permission",config("exceptions.NO_PERMISSTION"));
		}
		//工作组校验
		if(!empty($gids)){;
			if(is_array($gids)){
				if(!empty(array_diff($gids,$user->gids))){
					throw new \Exception("There some workgroup is not belongs to you",config("exceptions.USER_NO_WORKGROUP"));
				}
			}else{
				if(!in_array($gids,$user->gids)){
					throw new \Exception("The workgroup is not belongs to you",config("exceptions.USER_NO_WORKGROUP"));
				}
			}
		}
		return true;
	}

}