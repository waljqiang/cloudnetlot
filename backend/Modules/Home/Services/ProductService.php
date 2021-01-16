<?php
namespace Modules\Home\Services;

use App\Services\BaseService;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Home\Repositories\ProductRepository;

class ProductService extends BaseService{
	private $productRepository;

	public function __construct(ProductRepository $productRepository){
		$this->productRepository = $productRepository;
	}

	//统计用户下各个产品下的设备数
	public function staticsWithDevices($user,$params){
		$products = $this->productRepository->staticsWithDevices($user->primary_id);
		return $products->map(function($product){
			return [
				"name" => $product->name,
				"devices_count" => $product->devices_count
			];
		});
	}

}