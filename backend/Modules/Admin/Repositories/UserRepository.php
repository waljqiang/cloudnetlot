<?php
namespace Modules\Admin\Repositories;

use App\Repositories\BaseRepository;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserRepository extends BaseRepository{
	public function model(){
		return User::class;
	}


	//获取开发者列表
	public function getDevelops($conditions = [],$has = []){
		return $this->makeModel()->where($conditions)->whereHas("develop",function($query)use($has){
			$query->where($has);
		})->get();
	}

	//获取开发者信息
	public function getDevelop($conditions = []){
		return $this->makeModel()->where($conditions)->has("develop")->first();
	}

}