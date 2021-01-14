<?php
namespace Modules\Home\Repositories;

use App\Repositories\BaseRepository;
use Modules\Home\Entities\Command;
use Illuminate\Support\Facades\DB;

class CommandRepository extends BaseRepository{
	public function model(){
		return Command::class;
	}

	public function statics($conditions){
		$all = $this->counts($conditions);
		$reads = $this->makeModel()->where($conditions)->whereHas("messageReads")->count();
		return [
			"all" => $all,
			"reads" => $reads,
			"unreads" => $all-$reads 
		];
	}

}