<?php
namespace Modules\Home\Repositories;

use App\Repositories\BaseRepository;
use Modules\Home\Entities\DeviceRelation;
use Illuminate\Support\Facades\DB;

class DeviceRelationRepository extends BaseRepository{
	public function model(){
		return DeviceRelation::class;
	}

}