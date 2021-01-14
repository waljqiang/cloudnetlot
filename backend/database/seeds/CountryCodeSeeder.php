<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CountryCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $time = Carbon::now()->timestamp;
    	$res = DB::connection()->table("country_code")->first();
    	if(empty($res)){
    		$inits = require_once __DIR__ . "/CountrycodeInit.php";
            $datas = [];
            $first = [];
            $defaultShort2 = config("public.default.country");
            foreach($inits as $value){
                if($value[2] == $defaultShort2){
                    $first = $value;
                }else{
                    $datas[] = $value;
                }
            }
            array_unshift($datas,$first);
    		$key = ["name_en_us","name_zh_cn","short2","short3","num","phonecode","created_at","updated_at"];
    		$insertDatas = [];
    		foreach ($datas as $data) {
    			$data = array_merge($data,[$time,$time]);
    			$insertDatas[] = array_combine($key,$data);
    		}

	        DB::connection()->table("country_code")->insert($insertDatas);
	    }
    }
}
