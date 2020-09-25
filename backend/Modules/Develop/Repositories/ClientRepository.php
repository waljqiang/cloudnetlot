<?php
namespace Modules\Develop\Repositories;

use App\Repositories\BaseRepository;
use Modules\Develop\Entities\Client;

class ClientRepository extends BaseRepository{
	public function model(){
		return Client::class;
	}

}