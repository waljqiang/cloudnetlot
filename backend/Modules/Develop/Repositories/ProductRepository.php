<?php
namespace Modules\Develop\Repositories;

use App\Repositories\BaseRepository;
use Modules\Develop\Entities\Product;

class ProductRepository extends BaseRepository{
	public function model(){
		return Product::class;
	}

}