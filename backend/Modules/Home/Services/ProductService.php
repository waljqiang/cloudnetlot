<?php
namespace Modules\Home\Services;

use App\Services\BaseService;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Home\Repositories\ProductRepository;

class ProductService extends BaseService{
	protected $productRepository;

	public function __construct(ProductRepository $productRepository){
		$this->productRepository = $productRepository;
	}

	//统计用户下各个产品下的设备数
	public function statisticsWithDevices($user,$params){
		return $this->productRepository->statisticsWithDevices($user->primary_id);
	}

}