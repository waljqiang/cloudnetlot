<?php
namespace Modules\Home\Entities;

use App\Models\Model;

class Device extends Model{
	protected $table = 'device';
	protected $primaryKey = 'id';

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ["user_id","dev_mac","dev_ip","net_ip","heartbeat","name","prt_type","prt_size","type","mode","version","up_time","pid","area","country","province","city","address","latitude","longtitude","chip","sn","notes","group_id","is_ip_location","is_del","join_time","created_at","updated_at"];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    public function params(){
        return $this->hasMany(DeviceParams::class,"dev_mac","dev_mac");
    }

	public function commands(){
		return $this->hasMany(Command::class,"dev_mac","dev_mac");
	}

    public function getParametersAttribute(){
        return $this->params->pluck("params","type")->map(function($value){
            return json_decode($value,true);
        });
    }

    public function getSystemAttribute(){
        return $this->parameters->get(config("device.typeinfo.system"));
    }

    public function getNetworkAttribute(){
        return $this->parameters->get(config("device.typeinfo.network"));
    }

    public function getWifiAttribute(){
        return $this->parameters->get(config("device.typeinfo.wifi"));
    }

    public function getClientAttribute(){
        return $this->parameters->get(config("device.typeinfo.user"));
    }

    public function getTimereBootAttribute(){
        return $this->parameters->get(config("device.typeinfo.time_reboot"));
    }

}