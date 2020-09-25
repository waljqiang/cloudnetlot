<?php
namespace Modules\Develop\Repositories;

use App\Repositories\BaseRepository;
use Modules\Develop\Entities\Acl;

class AclRepository extends BaseRepository{
	public function model(){
		return Acl::class;
	}

}