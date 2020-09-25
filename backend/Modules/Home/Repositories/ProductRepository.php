<?php
namespace Modules\Home\Repositories;

use App\Repositories\BaseRepository;
use Modules\Home\Entities\Product;

class ProductRepository extends BaseRepository{
	public function model(){
		return Product::class;
	}

}