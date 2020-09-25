<?php
namespace Modules\Develop\Repositories;

use App\Repositories\BaseRepository;
use App\Models\User;

class UserRepository extends BaseRepository{
	public function model(){
		return User::class;
	}

}