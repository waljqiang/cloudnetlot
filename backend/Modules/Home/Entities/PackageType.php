<?php
namespace Modules\Home\Entities;

use App\Models\Model;

class PackageType extends Model{
	protected $table = "package_type";
	protected $primaryKey = "id";

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'id','fid','type','created_at','updated_at'
    ];
	/**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['id'];

}