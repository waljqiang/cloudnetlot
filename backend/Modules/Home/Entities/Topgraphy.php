<?php
namespace Modules\Home\Entities;

use App\Models\Model;

class Topgraphy extends Model{
	protected $table = "topgraphy";
	protected $primaryKey = "id";

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'id','uid','mac','pid','content','is_edit','is_virture','point_x','point_y','group_id','created_at','updated_at'
    ];
	/**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        
    ];

}