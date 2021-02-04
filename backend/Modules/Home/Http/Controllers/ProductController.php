<?php
namespace Modules\Home\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Home\Services\ProductService;

class ProductController extends Controller{
	protected $productService;

	public function __construct(ProductService $productService){
		$this->productService = $productService;
	}

	//统计用户下各个产品下的设备数
	public function statisticsWithDevices(Request $request){
		return $this->productService->statisticsWithDevices($request->user(),$request->all());
	}

}