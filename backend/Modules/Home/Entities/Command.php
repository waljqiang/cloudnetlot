<?php
namespace Modules\Home\Entities;

use App\Models\Model;
use App\Models\User;
use Modules\Home\Entities\MessageRead;

class Command extends Model{
	protected $table = "command";
	protected $primaryKey = "id";

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ["user_id","dev_mac","comm_id","content","describe","type","status","is_del","created_at","updated_at"];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

	public function user(){
		return $this->belongsTo(User::class,"user_id","id");
	}

	public function messageReads(){
		return $this->hasMany(MessageRead::class,"comm_id","comm_id");
	}

	public function getContentAttribute($value){
		return json_decode($value,true);
	}

	public function device(){
		return $this->belongsTo(Device::class,"dev_mac","dev_mac");
	}

}