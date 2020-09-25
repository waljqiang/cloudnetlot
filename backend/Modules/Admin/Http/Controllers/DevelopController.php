<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Http\Requests\DevelopRequest;
use Modules\Admin\Services\DevelopService;

class DevelopController extends Controller{
	private $developService;

    public function __construct(DevelopService $developService){
    	$this->developService = $developService;
    }

    //获取开发者列表
    public function getList(DevelopRequest $request){
    	return $this->developService->getList($request->user(),$request->all());
    }

    //获取开发者信息
    public function getInfo(DevelopRequest $request){
    	return $this->developService->getInfo($request->user(),$request->all());
    }

    //审批开发者申请
    public function approve(DevelopRequest $request){
        return $this->developService->approve($request->user(),$request->all());
    }

}
