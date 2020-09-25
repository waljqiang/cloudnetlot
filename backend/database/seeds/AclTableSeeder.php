<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AclTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $time = Carbon::now()->timestamp;
    	$res = DB::connection()->table("develop_acl")->where("username",config("mqtt.options.username"))->first();
    	if(empty($res)){
            //添加服务端访问消息服务器规则
	        DB::connection()->table("develop_acl")->insert([
                [//禁止所有用户订阅系统主题
                    "id" => 1,
                    "allow" => 0,
                    "ipaddr" => NULL,
                    "username" => '$all',
                    "clientid" => NULL,
                    "access" => 3,
                    "topic" => '$SYS/#',
                    "created_at" => $time,
                    "updated_at" => $time
                ],
                [//允许admin用户发布订阅设备上行主题
                    "id" => 2,
                    "allow" => 1,
                    "ipaddr" => NULL,
                    "username" => config("mqtt.options.username"),
                    "clientid" => NULL,
                    "access" => 3,
                    "topic" => config("mqtt.topic.deviceup"),
                    "created_at" => $time,
                    "updated_at" => $time
                ],
                [//允许admin用户发布订阅设备下行主题
                    "id" => 3,
                    "allow" => 1,
                    "ipaddr" => NULL,
                    "username" => config("mqtt.options.username"),
                    "clientid" => NULL,
                    "access" => 3,
                    "topic" => config("mqtt.topic.devicedown"),
                    "created_at" => $time,
                    "updated_at" => $time
                ],
                [//允许admin用户订阅设备上线主题
                    "id" => 4,
                    "allow" => 1,
                    "ipaddr" => NULL,
                    "username" => config("mqtt.options.username"),
                    "clientid" => NULL,
                    "access" => 1,
                    "topic" => config("mqtt.topic.online"),
                    "created_at" => $time,
                    "updated_at" => $time
                ],
                [//允许admin用户订阅设备下线主题
                    "id" => 5,
                    "allow" => 1,
                    "ipaddr" => NULL,
                    "username" => config("mqtt.options.username"),
                    "clientid" => NULL,
                    "access" => 1,
                    "topic" => config("mqtt.topic.offline"),
                    "created_at" => $time,
                    "updated_at" => $time
                ]
        	]);
	    }
    }
}
