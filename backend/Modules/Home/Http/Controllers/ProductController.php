<?php
namespace Modules\Home\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Home\Services\ProductService;

class ProductController extends Controller{
	private $productService;

	public function __construct(ProductService $productService){
		$this->productService = $productService;
	}

	//统计用户下各个产品下的设备数
	public function staticsWithDevices(Request $request){
		return $this->productService->staticsWithDevices($request->user(),$request->all());
	}

}