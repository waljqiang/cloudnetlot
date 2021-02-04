<?php
namespace Modules\Home\Repositories;

use App\Repositories\BaseRepository;
use Modules\Home\Entities\UpgradeOrderDev;
use Illuminate\Support\Facades\DB;

class UpgradeOrderDevRepository extends BaseRepository{
	public function model(){
		return UpgradeOrderDev::class;
	}

}