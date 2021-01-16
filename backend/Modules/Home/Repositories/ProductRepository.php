<?php
namespace Modules\Home\Repositories;

use App\Repositories\BaseRepository;
use Modules\Home\Entities\Product;

class ProductRepository extends BaseRepository{
	public function model(){
		return Product::class;
	}

	public function staticsWithDevices($uid){
		return $this->makeModel()->whereHas("devices",function($query)use($uid){
			$query->where("user_id",$uid);
		})->withCount(["devices" => function($query)use($uid){
			$query->where("user_id",$uid);
		}])->get();
	}

}