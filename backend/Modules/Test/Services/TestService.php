<?php
namespace Modules\Test\Services;

use App\Services\BaseService;
use Modules\Test\Repositories\DeviceRepository;
use App\Repositories\CacheRepository;

class TestService extends BaseService{
	private $deviceRepository;
	private $cacheRepository;

	public function __construct(DeviceRepository $deviceRepository,CacheRepository $cacheRepository){
		$this->deviceRepository = $deviceRepository;
		$this->cacheRepository = $cacheRepository;
	}

	public function getDevices($user,$condition = [], $with = [], $columns = ['*'], $unique = false){
		array_push($condition,['user_id',$user->id]);
		return $this->deviceRepository->getInfos($condition,$with,$columns,$unique);
	}

	public function testOplogCache($params){
		$uid = array_get($params,"uid",2);
		$rs = $this->cacheRepository->incrOplogNums($uid,[
			"total" => 100,
			"reads" => 50
		]);
		$rs = $this->cacheRepository->getUserOplogNums($uid);
		var_dump($rs);
		$rs = $this->cacheRepository->setUserOplogNums($uid,["total" => 10,"reads" => 5]);
		var_dump($rs);
		$rs = $this->cacheRepository->getUserOplogNums($uid);
		var_dump($rs);
		$rs = $this->cacheRepository->incrOplogNums($uid,[
			"total" => 2,
			"reads" => 1
		]);
		var_dump($rs);
		$rs = $this->cacheRepository->getUserOplogNums($uid);
		dd($rs);
	}
}