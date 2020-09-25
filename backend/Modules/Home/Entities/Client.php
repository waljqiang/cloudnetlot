<?php
namespace Modules\Home\Entities;

use App\Models\Model;
use Modules\Home\Entities\Product;

class Client extends Model{
	protected $table = "develop_client";
	protected $primaryKey = "id";

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'id','uid','prtid','mac','created_at','updated_at'
    ];
	/**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        
    ];

    public function product(){
        return $this->belongsTo(Product::class,"prtid","prtid");
    }

}