<?php
namespace Modules\Home\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Home\Http\Requests\UpgradeRequest;
use Modules\Home\Services\UpgradeService;

class UpgradeController extends Controller{
	protected $upgradeService;

	public function __construct(UpgradeService $upgradeService){
		$this->upgradeService = $upgradeService;
	}

	//上传配置文件
	public function upload(UpgradeRequest $request){
		return $this->upgradeService->upload($request->user(),$request->all());
	}

	//获取本地升级包列表
	public function getLocalPackages(UpgradeRequest $request){
		return $this->upgradeService->getLocalPackages($request->user(),$request->all());
	}

	//删除本地升级包
	public function deleteLocalPackages(UpgradeRequest $request){
		return $this->upgradeService->deleteLocalPackages($request->user(),$request->all());
	}

	//升级
	public function upgrades(UpgradeRequest $request){
		return $this->upgradeService->upgrades($request->user(),$request->all());
	}

	//获取升级单列表
	public function getOrders(UpgradeRequest $request){
		return $this->upgradeService->getOrders($request->user(),$request->all());
	}

}