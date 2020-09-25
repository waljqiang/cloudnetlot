<?php

use Illuminate\Database\Seeder;

class AreaTableSeeder extends Seeder{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        $res = DB::connection()->table("area")->first();
    	if(empty($res)){
    		$datas = require_once __DIR__ . "/AreaInit.php";
	        DB::connection()->table("area")->insert($datas);
	    }
    }
}
