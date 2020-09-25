<?php
namespace App\Utils\Passport;

use App\Models\Model;

class Scope extends Model{
	protected $table = 'client_scopes';
	protected $primaryKey = 'id';

	protected $fillable = [
        'client_id','scopes','create_time','update_time'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        
    ];

    public function getScopesAttribute($value){
        return !empty($value) ? json_decode($value,true) : [];
    }   
}