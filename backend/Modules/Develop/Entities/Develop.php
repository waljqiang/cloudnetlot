<?php
namespace Modules\Develop\Entities;

use App\Models\Model;
use App\Model\User;

class Develop extends Model{
	protected $table = "develop";
	protected $primaryKey = "id";
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	"user_id","salt","appid","secret","name","idcard","enterprise","enterprise_des","enterprisecode","is_supper","aud_status","created_at","updated_at"
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        
    ];

    public function user(){
    	return $this->belongsTo(User::class,"user_id","id");
    }

    public function getAppidAttribute($value){
        return !empty($value) ? $value : "";
    }

    public function getSecretAttribute($value){
        return !empty($value) ? $value : "";
    }
}