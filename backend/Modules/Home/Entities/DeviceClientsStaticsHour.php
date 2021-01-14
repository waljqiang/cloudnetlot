<?php
namespace Modules\Home\Entities;

use App\Models\Model;

class DeviceClientsStaticsHour extends Model{
	protected $table = "device_clients_statics_hour";
	protected $primaryKey = "id";

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ["mac","onlines","hours","created_at","updated_at"];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    public function device(){
        return $this->belongsTo(Device::class,"mac","dev_mac");
    }
}