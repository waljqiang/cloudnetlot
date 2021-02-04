<?php
namespace Modules\Home\Entities;

use App\Models\Model;

class UpgradeOrderDev extends Model{
	protected $table = "upgrade_order_dev";
	protected $primaryKey = "id";

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'id','orderid','dev_mac','dev_name','type','status','is_del','created_at','updated_at'
    ];
	/**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['id','is_del'];

}