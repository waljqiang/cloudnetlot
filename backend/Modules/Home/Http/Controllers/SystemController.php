<?php
namespace Modules\Home\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Home\Services\SystemService;
use Modules\Home\Http\Requests\SystemRequest;

class SystemController extends Controller{
	protected $systemService;

	public function __construct(SystemService $systemService){
		$this->systemService = $systemService;
	}

	public function getClient(SystemRequest $request){
		return $this->systemService->getClient($request->all());
	}

}