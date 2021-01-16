<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use App\User as UserBase;
use Laravel\Passport\HasApiTokens;
use Modules\Home\Entities\Workgroup;
use Modules\Home\Entities\Command;
use Modules\Develop\Entities\Develop;
use App\Models\Admin;
use Modules\Develop\Entities\Product;
use Modules\Develop\Entities\Acl;

class User extends UserBase implements JWTSubject{
    use HasApiTokens;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username','password','mq_password','salt','nickname','pid','email','timeZone','isSummerTime','phonecode','phone','level','area','address','latitude','longitude','status','admin_id','is_del','created_at','updated_at'
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password','mq_password','salt','remember_token','is_del'
    ];

    protected $dateFormat = 'U';

    /** @var attribute trans */
    protected $casts = [
        
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier(){
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims(){
        return [];
    }

    public function workgroups(){
        return $this->belongsToMany(Workgroup::class,"user_group","user_id","group_id");
    }

    public function commands(){
        return $this->hasMany(Command::class,"user_id","id");
    }

    public function develop(){
        return $this->hasOne(Develop::class,"user_id","id");
    }

    public function products(){
        return $this->hasMany(Product::class,"uid","id");
    }

    public function acls(){
        return $this->hasMany(Acl::class,'username','username');
    }

    public function admin(){
        return $this->belongsTo(Admin::class,"admin_id","id");
    }

    public function childs(){
        return $this->hasMany(User::class,"pid","id");
    }

    public function getNicknameAttribute($value){
        return !empty($value) ? $value : '';
    }

    public function getEmailAttribute($value){
        return !empty($value) ? $value : '';
    }

    public function getPhoneAttribute($value){
        return !empty($value) ? $value : '';
    }

    public function getAddressAttribute($value){
        return !empty($value) ? $value : '';
    }

    public function getLongitudeAttribute($value){
        return !empty($value) ? $value : '';
    }

    public function getLatitudeAttribute($value){
        return !empty($value) ? $value : '';
    }

    public function getIsPrimaryAttribute($value){
        return empty($this->pid);
    }

    public function getPrimaryIdAttribute($value){
        return $this->is_primary ? $this->id : $this->pid;
    }

    public function getGidsAttribute($value){
        return $this->workgroups->pluck("id")->toArray();
    }

}
