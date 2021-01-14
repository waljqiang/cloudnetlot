<?php
namespace Modules\Home\Repositories;

use App\Repositories\BaseRepository;
use Modules\Home\Entities\DeviceClientsNums;

class DeviceClientsNumsRepository extends BaseRepository{
	public function model(){
		return DeviceClientsNums::class;
	}

}