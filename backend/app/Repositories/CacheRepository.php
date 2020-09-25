<?php
namespace App\Repositories;

use Illuminate\Support\Facades\Redis;
use App\Utils\LuaScripts;

class CacheRepository{
	const DEVICE_QUEQUE_LOCATION = "device:queue:location";//设备定位队列
	const DEVICE_QUEQUE_DATA = "device:queue:data";//设备消息队列
	const DEVICE_DYNAMIC = "h:device:dynamic:";//设备信息缓存
	const REGISTER = "register:";//注册信息缓存,该缓存有服务层解析设备上报信息的时候写入
	const MSG_MID = "msg:";//消息去重缓存

	const SYSTEM_CAPTCHA = 'system:captcha:';//验证码存储地址
	const USER_EMAIL_STATUS = 'user:password:email:status:';//用户找回密码邮件链接是否使用
	const OPLOG_NUM = "h:user:oplog:num:";//用户操作日志统计

	public function setRegister($prtid,$mac,$data,$ttl=0){
		return !empty($ttl) ? Redis::setex(self::REGISTER . $prtid . ":" . $mac,$ttl,json_encode($data)) :Redis::set(self::REGISTER . $prtid . ":" . $mac,json_encode($data));
	}

	public function getRegister($prtid,$mac){
		$rs = Redis::get(self::REGISTER . $prtid . ":" . $mac);
		return $rs ? json_decode($rs,true) : [];
	}

	public function clearRegisterByPrtid($prtid){
        return Redis::connection()->eval(
            LuaScripts::vagueDel(), 1, self::REGISTER . $prtid . ":*"
        );
	}

	public function setCaptcha($code,$ttl = null){
		return !empty($ttl) ? Redis::setex(self::SYSTEM_CAPTCHA . $code['code'],$ttl,serialize($code)) : Redis::set(self::SYSTEM_CAPTCHA . $code['code'],serialize($code));
	}

	public function getCaptcha($code){
		return unserialize(Redis::get(self::SYSTEM_CAPTCHA . $code));
	}

	public function deleteCaptcha($key){
		return Redis::del(self::SYSTEM_CAPTCHA . $key);
	}

	public function setPasswordEmailStatus($uid,$value,$ttl = 120){
		return !empty($ttl) ? Redis::setex(self::USER_EMAIL_STATUS . $uid,$ttl,serialize($value)) : Redis::set(self::USER_EMAIL_STATUS . $uid,serialize($value));
	}

	public function getPasswordStatus($uid){
		return unserialize(Redis::get(self::USER_EMAIL_STATUS . $uid));
	}

	public function setUserOplogNums($uid,$data){
		return Redis::hmset(self::OPLOG_NUM . $uid,$data);
	}

	public function getUserOplogNums($uid){
		return Redis::hgetall(self::OPLOG_NUM . $uid);
	}

	public function incrOplogNums($uid,$data){
		if(!empty($data)){
			return Redis::pipeline(function($pipe)use($uid,$data){
				foreach($data as $key => $value){
					$pipe->hincrby(self::OPLOG_NUM . $uid,$key,$value);
				}
			});
		}
		return true;
	}

	public function setDeviceDynamic($mac,$data){
		return Redis::hmset(self::DEVICE_DYNAMIC . $mac,$data);
	}

	public function getDeviceDynamic($mac){
		$data = Redis::hgetall(self::DEVICE_DYNAMIC . $mac);
		return !empty($data) && $data["status"] == config("device.status.online") ? $data : ["cpu_use" => "0","memory_use" => "0","runtime" => "0","status" => "0"];
	}

	public function getDevicesDynamic($macs){
		$res = [];
		$macs = $macs instanceof \Illuminate\Support\Collection ? $macs->toArray() : $macs;
		if(!empty($macs)){
			$results = Redis::pipeline(function($pipe)use($macs){
				foreach ($macs as $mac) {
					$pipe->hgetall(self::DEVICE_DYNAMIC . $mac);
				}
			});
			foreach ($macs as $key => $mac) {
				$res[$mac] = !empty($results[$key]) && $results[$key]["status"] == config("device.status.online") ? $results[$key] : ["cpu_use" => "0","memory_use" => "0","runtime" => "0","status" => "0"];
			}
		}
		return $res;
	}

}