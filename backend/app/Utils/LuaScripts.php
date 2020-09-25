<?php
namespace App\Utils;

class LuaScripts{
	public static function vagueDel(){
		return <<<'LUA'
local keyArr = redis.call('keys',KEYS[1])
if(next(keyArr) ~= nil) then
	return redis.call('del',unpack(keyArr))
else
	return 0
end
LUA;
	}
}