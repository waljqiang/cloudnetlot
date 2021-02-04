<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class HashidsEncode{
    private $hashids;

    public function __construct(){
        $this->hashids = app("Hashids");
    }

    /**
     * Handle an incoming request.
     *
     * @param  Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next,$keys = 'id'){
      $response = $next($request);
        if($response instanceof Response){
            $content = $response->getOriginalContent();
            $keys = explode("#",$keys);
            if(!empty($keys)){
                foreach ($keys as $key) {
                    $this->encode($content,$key);
                }
            }
            $response->setContent($content);
        }
        return $response;
    }

    private function encode(&$array,$key){
        $keys = explode(".",$key);
        while(count($keys) > 1){
            $key = array_shift($keys);
            if("*" == $key){
                if($array instanceof Collection){
                    $array = $array->map(function($value) use ($keys){
                        return $this->encode($value,implode(".",$keys));
                    });
                }else{
                    $array = array_map(function($value) use ($keys){
                        return $this->encode($value,implode(".",$keys));
                    },$array);
                }
            }elseif(isset($array[$key])){
                $array = &$array[$key];
            }else{
                break;
            }
        }
        $currentKey = array_shift($keys);
        //最后为数字索引数组
        if("*" == $currentKey){
            $currentArr = $array;
            if($currentArr instanceof Collection){
                $array = $currentArr->map(function($value){
                    return $this->hashids->encodeHash($value);
                });
            }else{
                $array = array_map(function($value){
                    return $this->hashids->encodeHash($value);
                },$currentArr);
            }
        }
        if(isset($array[$currentKey])){
            $array[$currentKey] = $this->hashids->encodeHash($array[$currentKey]);
        }
        return $array;
    }

}
