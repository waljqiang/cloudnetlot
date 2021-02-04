<?php
namespace App\Utils;
use Illuminate\Support\Facades\Validator;
use libphonenumber\PhoneNumberUtil;

class ValidatorExtend{
    public static function adds(){
        //验证scopes
    	Validator::extend("scopes_config",function($attribute, $value, $parameters, $validator){
                $config = array_keys(config($parameters[0]));
                $value = is_string($value) ? explode(",",$value) : $value;
                $flag = true;
                foreach ($value as $tmp) {
                    if(!in_array($tmp,$config)){
                        $flag = false;
                        break;
                    }
                }
                return $flag;
            });
        //按位数验证长度
        Validator::extend("bit_between",function($attribute,$value,$parameters,$validator){
            $size = strlen($value);
            return $size >= $parameters[0] && $size <= $parameters[1];
        });
        //验证世界各地手机号
        Validator::extend("phone",function($attribute,$value,$parameters,$validator){
            try{
                $phonecode = array_get($validator->getData(),$parameters["0"],"86");
                $fullPhone = "+" . $phonecode . $value;
                $phoneObj = PhoneNumberUtil::getInstance()->parse($fullPhone,NULL);
                return PhoneNumberUtil::getInstance()->isValidNumber($phoneObj);
            }catch(\Exception $e){
                return false;
            }
        });
        //验证文件扩展名
        Validator::extend("fileext",function($attribute,$value,$parameters,$validator){
            try{
                return in_array($value->getClientOriginalExtension(),$parameters);
            }catch(\Exception $e){
                return false;
            }
        });
        //验证产品类型
        Validator::extend("producttype",function($attribute,$value,$parameters,$validator){
            try{
                return in_array($value,config("device.producttype"));
            }catch(\Exception $e){
                return false;
            }
        });
        //小于等于某字段值
        Validator::extend("lte",function($attribute,$value,$parameters,$validator){
            if (count($parameters) < 1) {
                throw new InvalidArgumentException("Validation rule lte requires at least 1 parameters.");
            }
            $compareValue = array_get($validator->getData(),$parameters["0"]);
            return $value <= $compareValue;
        });
        //大于等于
        Validator::extend("lge",function($attribute,$value,$parameters,$validator){
            if (count($parameters) < 1) {
                throw new InvalidArgumentException("Validation rule lge requires at least 1 parameters.");
            }
            $compareValue = array_get($validator->getData(),$parameters["0"]);
            return $value >= $compareValue;
        });
    }
}