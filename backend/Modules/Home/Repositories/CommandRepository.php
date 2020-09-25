<?php
namespace Modules\Home\Repositories;

use App\Repositories\BaseRepository;
use Modules\Home\Entities\Command;

class CommandRepository extends BaseRepository{
	public function model(){
		return Command::class;
	}

}