<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AdminTableSeeder extends Seeder{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
    	$time = Carbon::now()->timestamp;
    	$res = DB::connection()->table("admin")->first();
    	if(empty($res)){
	        DB::connection()->table("admin")->insert([
	        	[
	        		"username" => config("public.default.admin"),
	        		"password" => bcrypt(config("public.default.password")),
                    "email" => config("public.default.email"),
                    "phonecode" => config("public.default.phonecode"),
                    "phone" => config("public.default.phone"),
                    "status" => 0,
	        		"created_at" => $time,
	        		"updated_at" => $time
	        	]
	        ]);
	    }
    }
}
