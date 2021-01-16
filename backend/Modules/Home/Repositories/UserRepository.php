<?php
namespace Modules\Home\Repositories;

use App\Repositories\BaseRepository;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserRepository extends BaseRepository{
	public function model(){
		return User::class;
	}

	public function countChilds($uid){
		return $this->makeModel()->where("pid",$uid)->select("status",DB::raw("count(*) as total"))->groupBy("status")->get();
	}

}