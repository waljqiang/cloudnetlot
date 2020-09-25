<?php
namespace Modules\Home\Repositories;

use App\Repositories\BaseRepository;
use Modules\Home\Entities\MessageRead;

class MessageReadRepository extends BaseRepository{
	public function model(){
		return MessageRead::class;
	}

}