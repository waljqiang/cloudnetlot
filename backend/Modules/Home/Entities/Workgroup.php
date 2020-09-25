<?php
namespace Modules\Home\Entities;

use App\Models\Model;
use App\Models\User;

class Workgroup extends Model{
	protected $table = "group";
	protected $primaryKey = "id";
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ["user_id","pid","code","name","description","config_id","auto","level","is_del","created_at","updated_at"];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ["is_del"];

    public function users(){
        return $this->belongsToMany(User::class,"user_group","group_id","user_id");
    }
}