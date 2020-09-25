<?php
namespace Modules\Develop\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Develop\Http\Requests\ProductRequest;
use Modules\Develop\Services\ProductService;

class ProductController extends Controller{
	private $productService;

	public function __construct(ProductService $productService){
		$this->productService = $productService;
	}

	//注册产品
	public function register(ProductRequest $request){
		return $this->productService->register($request->user(),$request->all());
	}

	//获取产品列表
	public function getList(ProductRequest $request){
		return $this->productService->getList($request->user(),$request->all());
	}

	//获取产品详情
	public function getInfo(ProductRequest $request){
		return $this->productService->getInfo($request->user(),$request->all());
	}

	//编辑产品
	public function save(ProductRequest $request){
		return $this->productService->save($request->user(),$request->all());
	}

	//删除产品
	public function delete(ProductRequest $request){
		return $this->productService->delete($request->user(),$request->all());
	}

	//发布产品
	public function publish(ProductRequest $request){
		return $this->productService->publish($request->user(),$request->all());
	}

}