<?php
namespace Modules\Admin\Repositories;

use App\Repositories\BaseRepository;
use Modules\Admin\Entities\Product;
use Illuminate\Support\Facades\DB;

class ProductRepository extends BaseRepository{
	public function model(){
		return Product::class;
	}

	public function getProducts($conditions = [],$has = [],$columns = ["*"],$unique = false){
		$product = $this->makeModel()->where($conditions);
		if(!empty($has)){
			foreach ($has as $key => $value) {
				$product = $product->whereHas($key,$value);
			}
		}
		return $unique ? $product->first($columns) : $product->get($columns);
	}

	//获取产品总数
	public function getTotal($adminID,$keyword = NULL){
		$data = [$adminID,1];
		$sql = "SELECT COUNT(m.`id`) as counts FROM " . $this->getTable("develop_product") . " m," . $this->getTable("users") . " n WHERE m.`uid`=n.`id` AND n.`admin_id`=? AND n.`status`=?";
		if(!is_null($keyword)){
			$sql .= " AND m.`id`=?";
			$data[] = $keyword;
		}
		$total = DB::select($sql,$data);
		return intval($total["0"]->counts);
	}

	public function getList($adminID,$pageIndex = 1,$pageOffset = 10,$sortKey = "created_at",$sort = "desc",$keyword = NULL){
		$start = $pageIndex > 1 ? ($pageIndex-1)*$pageOffset : 0;
		$end = $pageOffset;
		$data = [$adminID,1];
		$sql = "SELECT m.`id`,m.`prtid`,m.`uid`,m.`name`,m.`type`,m.`size`,m.`aud_status`,m.`created_at`,n.`username`,n.`timeZone`,n.`isSummerTime` FROM " . $this->getTable("develop_product") . " m," . $this->getTable("users") . " n WHERE m.`uid`=n.`id` AND n.`admin_id`=? AND n.`status`=?";
		if(!is_null($keyword)){
			$sql .= " AND m.`prtid`=?";
			$data[] = $keyword;
		}
		$sql .= " ORDER BY {$sortKey} {$sort} LIMIT {$start},{$end}";
		return DB::select($sql,$data);
	}

}