<?php
namespace Modules\Home\Repositories;

use App\Repositories\BaseRepository;
use Modules\Home\Entities\Package;
use Illuminate\Support\Facades\DB;

class PackageRepository extends BaseRepository{
	public function model(){
		return Package::class;
	}

	public function getPackages($conditions = [],$with = [],$types = "",$columns = ["*"],$unique = false){
		$packages = $this->makeModel()->with($with)->where($conditions);
		if(!empty($types)){
			$packages = $packages->whereHas("types",function($query)use($types){
				$query->whereIn("type",$types);
			});
		}
		return $unique ? $packages->first($columns) : $packages->get($columns);
	}

}