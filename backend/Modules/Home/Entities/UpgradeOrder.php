<?php
namespace Modules\Home\Entities;

use App\Models\Model;
use App\Models\User;

class UpgradeOrder extends Model{
	protected $table = "upgrade_order";
	protected $primaryKey = "id";

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'id','orderid','user_id','package_id','package_name','package_md5','package_url','is_del','exec_time','created_at','updated_at'
    ];
	/**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['id','is_del'];

    public function user(){
        return $this->belongsTo(User::class,"user_id","id");
    }
}