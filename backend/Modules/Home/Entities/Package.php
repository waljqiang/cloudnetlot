<?php
namespace Modules\Home\Entities;

use App\Models\Model;

class Package extends Model{
	protected $table = "package";
	protected $primaryKey = "id";

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'id','fid','name','url','version','user_id','file_md5','is_local','size','downloads','created_at','updated_at'
    ];
	/**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id','is_local'
    ];

    public function types(){
        return $this->hasMany(PackageType::class,"fid","fid");
    }

    public function getSupportTypesAttribute(){
        return $this->types->pluck("type");
    }

}