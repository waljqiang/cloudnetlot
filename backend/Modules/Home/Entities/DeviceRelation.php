<?php
namespace Modules\Home\Entities;

use App\Models\Model;

class DeviceRelation extends Model{
	protected $table = "device_relation";
	protected $primaryKey = "id";

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'id','uid','mac','pid','content','created_at','updated_at'
    ];
	/**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        
    ];

}