<?php
namespace Modules\Admin\Entities;

use App\Models\Model;
use App\Models\User;
use Modules\Admin\Entities\Client;

class Product extends Model{
	protected $table = "develop_product";
	protected $primaryKey = "id";
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ["uid","name","describe","type","size","aud_status","created_at","updated_at"];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        
    ];

    public function user(){
        return $this->belongsTo(User::class,"uid","id");
    }

    public function clients(){
        return $this->hasMany(Client::class,"prtid","prtid");
    }

}