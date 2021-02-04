<?php
namespace Modules\Home\Repositories;

use App\Repositories\BaseRepository;
use Modules\Home\Entities\UpgradeOrder;
use Illuminate\Support\Facades\DB;

class UpgradeOrderRepository extends BaseRepository{
	public function model(){
		return UpgradeOrder::class;
	}

}