<?php
namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository as Repository;
use App\Traits\RepositoryTrait;
use Illuminate\Support\Facades\DB;

class BaseRepository extends Repository{
	use RepositoryTrait;
	public function model(){

	}

	/*public function getInfos($condition = [], $with = [], $columns = ['*'], $unique = false){
		if($unique)
			return $this->with($with)->where($condition)->first($columns);
		else
			return $this->with($with)->where($condition, $columns)->get($columns);
	}*/
	public function counts($conditions){
		return $this->makeModel()->where($conditions)->count();
	}

	public function getInfos($condition = [],$with = [],$columns = ['*'],$unique = false,$order = ["id","desc"],$page = [1,1000000]){
		if($unique){
			return $this->makeModel()->with($with)->where($condition)->orderBy($order[0],$order[1])->first($columns);
		}else{
			$offset = $page[0] >1 ? ($page[0]-1) * $page[1] : 0;
			$limit = $page[1]; 
			return $this->makeModel()->with($with)->where($condition,$columns)->orderBy($order[0],$order[1])->limit($limit)->offset($offset)->get($columns);
		}
	}

	public function add($data){
		return $this->makeModel()->insertGetId($data);
	}

	public function addAll($data){
		return $this->makeModel()->insert($data);
	}

	public function save($data,$condition){
		return $this->makeModel()->where($condition)->update($data);
	}

	public function delete($condition){
		return $this->makeModel()->where($condition)->delete();
	}

	public function getTable($name){
		return DB::getConfig("prefix") . $name;
	}
}