<?php
namespace Modules\Home\Entities;

use App\Models\Model;

class MessageRead extends Model{
	protected $table = "message_read";
	protected $primaryKey = "id";

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ["user_id","comm_id","created_at","updated_at"];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];
}