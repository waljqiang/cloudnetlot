<?php
namespace Modules\Home\Repositories;

use App\Repositories\BaseRepository;
use Modules\Home\Entities\PackageType;
use Illuminate\Support\Facades\DB;

class PackageTypeRepository extends BaseRepository{
	public function model(){
		return PackageType::class;
	}

}