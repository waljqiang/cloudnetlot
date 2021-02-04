<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        $time = Carbon::now()->timestamp;
    	$res = DB::connection()->table("users")->where("username",config("mqtt.options.username"))->first();
    	if(empty($res)){
            //添加服务端访问消息服务器用户
            DB::beginTransaction();
	        $userID = DB::connection()->table("users")->insertGetId([
        		"username" => config("mqtt.options.username"),
        		"password" => bcrypt(config("mqtt.options.password")),
                "mq_password" => config("mqtt.options.password"),
        		"nickname" => "系统",
                "email" => config("public.default.email"),
                "timeZone" => config("public.default.timeZone"),
                "isSummerTime" => config("public.default.isSummerTime"),
                "phonecode" => config("public.default.phonecode"),
                "phone" => config("public.default.phone"),
        		"level" => 100,
                "area" => config("public.default.area"),
                "address" => config("public.default.address"),
                "latitude" => config("public.default.lat"),
                "longitude" => config("default.lng"),
        		"admin_id" => 0,
                "is_del" => 1,
        		"created_at" => $time,
        		"updated_at" => $time
        	]);
            $develop = DB::connection()->table("develop")->insertGetId([
                "user_id" => $userID,
                "aud_status" => 3,
                "created_at" => $time,
                "updated_at" => $time
            ]);
            if($userID && $develop){
                DB::commit();
            }else{
                DB::rollBack();
                throw new \Exception("Failure",config("exceptions.MYSQL_EXEC_ERROR"));
            }
	    }
    }
}
