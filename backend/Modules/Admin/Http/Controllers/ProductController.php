<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Http\Requests\ProductRequest;
use Modules\Admin\Services\ProductService;

class ProductController extends Controller{
	private $productService;

    public function __construct(ProductService $productService){
    	$this->productService = $productService;
    }

    //获取产品列表
    public function getList(ProductRequest $request){
    	return $this->productService->getList($request->user(),$request->all());
    }

    //获取产品详情
    public function getInfo(ProductRequest $request){
    	return $this->productService->getInfo($request->user(),$request->all());
    }

    //审核产品发布申请
    public function approve(ProductRequest $request){
        return $this->productService->approve($request->user(),$request->all());
    }

}
