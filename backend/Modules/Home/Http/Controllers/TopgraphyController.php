<?php
namespace Modules\Home\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Home\Services\TopgraphyService;
use Modules\Home\Http\Requests\TopgraphyRequest;

class TopgraphyController extends Controller{
	protected $topgraphyService;

	public function __construct(TopgraphyService $topgraphyService){
		$this->topgraphyService = $topgraphyService;
	}

	//初始化拓扑图数据
	public function init(TopgraphyRequest $request){
		return $this->topgraphyService->init($request->user(),$request->all());
	}

	//获取拓扑图
	public function getInfo(TopgraphyRequest $request){
		return $this->topgraphyService->getInfo($request->user(),$request->all());
	}

	//从新生成拓扑图
	public function rebuild(TopgraphyRequest $request){
		return $this->topgraphyService->rebuild($request->user(),$request->all());
	}

	//生成虚拟设备MAC
	public function generateVirtureDeviceMac(TopgraphyRequest $request){
		return $this->topgraphyService->generateVirtureDeviceMac($request->user(),$request->all());
	}

	//保存拓扑图
	public function save(TopgraphyRequest $request){
		return $this->topgraphyService->save($request->user(),$request->all());
	}

	//获取拓扑图动态信息
	public function getDynamics(TopgraphyRequest $request){
		return $this->topgraphyService->getDynamics($request->user(),$request->all());
	}

	//获取未编辑过的设备
	public function getUnedits(TopgraphyRequest $request){
		return $this->topgraphyService->getUnedits($request->user(),$request->all());
	}

}