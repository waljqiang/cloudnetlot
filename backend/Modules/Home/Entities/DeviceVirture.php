<?php
namespace Modules\Home\Entities;

use App\Models\Model;

class DeviceVirture extends Model{
	protected $table = "device_virture";
	protected $primaryKey = "id";

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'id','uid','mac','name','mode','status','group_id','created_at','updated_at'
    ];
	/**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        
    ];

}