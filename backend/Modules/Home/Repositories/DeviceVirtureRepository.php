<?php
namespace Modules\Home\Repositories;

use App\Repositories\BaseRepository;
use Modules\Home\Entities\DeviceVirture;
use Illuminate\Support\Facades\DB;

class DeviceVirtureRepository extends BaseRepository{
	public function model(){
		return DeviceVirture::class;
	}

}