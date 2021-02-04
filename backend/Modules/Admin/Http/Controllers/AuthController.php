<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Http\Requests\AccessTokenRequest;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Carbon\Carbon;

class AuthController extends Controller{
    public function __construct(){

    }

    /**
     * 功能描述：获取token
     * @author waljqiang
     * @date   2019-12-27
     * @param  Request    $request 
     * @return
     */
    public function getToken(AccessTokenRequest $request){
        $credentials = request(["username","password"]);
        $credentials["status"] = 1;
        $token = auth("cloudnetlotadmin")->attempt($credentials);
        return $this->responseWithToken($token,$this->getRefreshToken($token));
    }

    /**
     * 功能描述：刷新token
     * @author waljqiang
     * @date   2019-12-31
     * @param  AccessTokenRequest $request [description]
     * @return array
     */
    public function refreshToken(AccessTokenRequest $request){
    	$params = $request->all();
        $refreshToken = array_get($params,"refresh_token");
        $token = auth("cloudnetlotadmin")->getToken();
        $refreshTokenDecode = base64_decode($refreshToken);
        $timeLen = 25 + strlen(config("hashids.id.header"));
        $refreshTokenBody = substr($refreshTokenDecode,0,-$timeLen);
        $refreshTokenExpire = app("Hashids")->decodeHash(substr($refreshTokenDecode,-$timeLen));

        if(!isset($refreshTokenExpire[0]) || empty($refreshTokenExpire[0])){
            throw new \Exception("Refresh token invalid",config("exceptions.REFRESH_TOKEN_INVALID"));        
        }

        if(Carbon::now()->gt(Carbon::createFromTimestamp($refreshTokenExpire[0]))){
            throw new \Exception("Refresh token is expired",config("exceptions.REFRESH_TOKEN_EXPIRES"));
        }

        return $this->responseWithToken(auth("cloudnetlotadmin")->refresh(),$refreshToken);
    }

    public function destroyToken(Request $request){
        auth("cloudnetlotadmin")->invalidate();
        return [];
    }

    /**
     * 功能描述：返回访问token处理
     * @author waljqiang
     * @date   2019-12-27
     * @param  string     $token 请求token
     * @param  string     $refreshToken 刷新token
     * @return array
     */
    private function responseWithToken($token,$refreshToken){
    	return [
    		"access_token" => $token,
    		"token_type" => "Bearer",
    		"expires_in" => auth("cloudnetlotadmin")->factory()->getTTL() * 60,
    		"refresh_token" => $refreshToken,
    		"refresh_expires_in" => config("jwt.refresh_ttl")
    	];
    }

    /**
     * 功能描述：获取refresh_token
     * @author waljqiang
     * @date   2019-12-27
     * @param  string     $token access_token
     * @return string
     */
    private function getRefreshToken($token){
    	$refreshExpiresIn = config("jwt.refresh_ttl");
    	$time = Carbon::now()->addMinutes($refreshExpiresIn)->timestamp;
    	return base64_encode($token . app("Hashids")->encodeHash($time));
    }

}
