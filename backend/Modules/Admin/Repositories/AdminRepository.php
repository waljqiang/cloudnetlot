<?php
namespace Modules\Admin\Repositories;

use App\Repositories\BaseRepository;
use App\Models\Admin;

class AdminRepository extends BaseRepository{
	public function model(){
		return Admin::class;
	}

}