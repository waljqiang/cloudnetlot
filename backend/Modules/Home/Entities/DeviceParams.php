<?php
namespace Modules\Home\Entities;

use App\Models\Model;

class DeviceParams extends Model{
	protected $table = 'device_params';
	protected $primaryKey = 'id';

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ["dev_mac","params","type","is_del","created_at","updated_at"];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    public function device(){
    	return $this->belongsTo(Device::class,"dev_mac","dev_mac");
    }

}