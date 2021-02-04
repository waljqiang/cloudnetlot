<?php
namespace Modules\Home\Repositories;

use App\Repositories\BaseRepository;
use Modules\Home\Entities\Product;
use Illuminate\Support\Facades\DB;

class ProductRepository extends BaseRepository{
	public function model(){
		return Product::class;
	}

	public function statisticsWithDevices($uid){
		$products = $this->makeModel()->whereHas("devices",function($query)use($uid){
			$query->where("user_id",$uid);
		})->with(["devices" => function($query)use($uid){
			$query->where("user_id",$uid);
		}])->get();
		return $products->map(function($product){
			$devices = $product->devices->groupBy("type");
			$devicesStatics = $devices->map(function($device){
				return $device->count();
			});
			return [
				"name" => $product->name,
				"devices_total" => $product->devices->count(),
				"devices_statics" => $devicesStatics
			];
		});
	}

}