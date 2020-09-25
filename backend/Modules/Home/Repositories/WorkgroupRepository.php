<?php
namespace Modules\Home\Repositories;

use App\Repositories\BaseRepository;
use Modules\Home\Entities\Workgroup;
use App\Models\User;

class WorkgroupRepository extends BaseRepository{
	public function model(){
		return Workgroup::class;
	}

	public function generateChildCode($uid,$gid){
		$codes = User::from("group as m")->select("m.code as parentcode","n.code as siblingcode")->leftJoin("group as n",function($join){
			$join->on("m.user_id","=","n.user_id")->on("m.id","=","n.pid");
		})->where("m.user_id",$uid)->where("m.id",$gid)->orderBy("n.id","desc")->first();
		$preCode = !empty($codes->parentcode) ? $codes->parentcode : "";
		if(empty($codes->siblingcode)){
			$code = $preCode . "001";
		}else{
			$codeArr = str_split($codes->siblingcode,3);
			$code = $preCode . sprintf("%03s",intval($codeArr[count($codeArr)-1])+1);
		}
		return trim($code);
	}

}