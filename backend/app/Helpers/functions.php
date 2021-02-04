<?php
use Carbon\Carbon;

//生成随机字符串
function getStr($len = 6,$source = '0123456789'){
    $str = '';
    for($i = 0; $i < $len; $i++){
    	$str .= iconv_substr($source,floor(mt_rand(0,mb_strlen($source,'utf-8')-1)),1,'utf-8');
    }
    return $str;
}

//根据时间戳、时区、夏令时将时间转换为相应的时区时间gmdate
function convUnixToZoneGm($timestamp,$timeZone,$summerTime){
	return Carbon::createFromFormat("Y-m-d H:i:s",date('Y-m-d H:i:s',$timestamp))->addMinutes($timeZone * 60)->addMinutes
	($summerTime * 60)->toDateTimeString();
}

//根据时间、时区、夏令时将时间转换为相应的时区时间gmdate
function convDateToZoneGm($date,$timeZone,$summerTime){
	return Carbon::createFromFormat("Y-m-d H:i:s",$date)->addMinutes($timeZone * 60)->addMinutes($summerTime * 60)->toDateTimeString();
}

//获取客户端真是ip
function getClientIp(){
    if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
        $ip = getenv('HTTP_CLIENT_IP');
    } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
        $ip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
        $ip = getenv('REMOTE_ADDR');
    } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    $res =  preg_match ( '/[\d\.]{7,15}/', $ip, $matches ) ? $matches [0] : '';
    return $res;
}

//mac地址添加冒号
function setMac($mac){
    return substr($mac, 0, 2) . ':' . substr($mac, 2, 2) . ':' . substr($mac, 4, 2) . ':' . substr($mac, 6, 2) . ':' . substr($mac, 8, 2) . ':' . substr($mac, 10, 2);
}

//mac地址去除冒号
function parseMac($mac){
    return str_replace(':', '', $mac);
}

/**
 * tree
 *
 * @param  array $items 数据
 * @param  string $id    数据标识key
 * @param  string $pid   数据父ID标识key
 * @param  string $sub   子节点名称
 * @param  string $order 排序字段
 * @param  [type] $sort  排序方法
 * @param  array  $root  根节点，如果有值，则所有节点全部放在根节点下
 * @param  boolean $modifyPid 如果节点中pid与跟节点id不一致时，是否修改节点pid为根节点id
 * @return array
 */
function rankSort($items,$id='id',$pid='pid',$sub='sub',$order='id',$sort=SORT_ASC,$root=[],$modifyPid=false){
    
    $items = array_combine(array_column($items,$id),$items);

    $tree = [];
    if(!empty($items)){
        foreach($items as $item){
            if(!isset($items[$item[$id]][$sub]))
                $items[$item[$id]][$sub] = [];
            if(isset($items[$item[$pid]])){
                $items[$item[$pid]][$sub][] = &$items[$item[$id]];
            }else{
                $tree[] = &$items[$item[$id]];
            }
        }
    }

    $res = $tree;
    unset($items);
    unset($tree);

    if(!empty($root)){
        if(!empty($res)){
            foreach ($res as $node) {
                if($modifyPid){
                    $node[$pid] = $root[$id];
                }
                $root[$sub][] = $node;
            }
        }
        $res = [$root];
    }

    array_multisort(array_column($res,$order),$sort,$res);
    return $res;
}

//生成协议类型
function getLotType($value){
    return strtoupper(str_pad(dechex($value),4,"0",STR_PAD_LEFT));
}
//生成信息类型
function getCommType($value){
    return strtoupper(str_pad(dechex($value),4,"0",STR_PAD_LEFT));
}
//生成命令ID
function getCommID($lotType,$commType,$time = ""){
    return !empty($time) ? getLotType($lotType) . getCommType($commType) . $time . getStr(4) : getLotType($lotType) . getCommType($commType) . time() . getStr(4);
}
//生成命令
function getCommand($commType,$body,$timestamp = "",$commID = ""){
    $timestamp = empty($timestamp) ? (string) time() : (string) $timestamp;
    if(empty($commID)){
        $body = array_merge(["comm_id" => getCommID(config("yunlot.lottype.down"),$commType,$timestamp)],$body);
    }else{
        $body = array_merge(["comm_id" => $commID],$body);
    }
    return app("yunlot")->init()->setHeader(["type" => config("yunlot.lottype.down")])->setBody($body)->setNow($timestamp)->out();
}
//生成产品ID
function generatePrtid($userID){
    $time = time();
    $userIDLength = str_pad(strlen($userID),2,0,STR_PAD_LEFT);
    $str = $userID . $time . getStr(4) . $userIDLength;
    return app("Hashprtids")->encodeHash($str);
}
//解析产品ID
function decodePrtID($encrypt){
    $prtIDArr = app("Hashprtids")->decodeHash($encrypt);
    if(count($prtIDArr) != 1){
        throw new \Exception("The prtid is error",config("exceptions.PRTID_ERROR"));
    }
    $userIDLength = intval(substr($prtIDArr[0],-2));
    return [substr($prtIDArr[0],0,$userIDLength)];
}
//生成客户端ID
function generateClitid($userID,$productID,$mac,$time = ""){
    $time = empty($time) ? time() : $time;
    $macdec = hexdec(parseMac($mac));
    $userIDLength = str_pad(strlen($userID),2,0,STR_PAD_LEFT);
    $productIDLength = str_pad(strlen($productID),2,0,STR_PAD_LEFT);
    $macdecLength = str_pad(strlen($macdec),2,0,STR_PAD_LEFT);
    $str = $userID . $productID . $macdec . $time . $userIDLength . $productIDLength . $macdecLength;
    return app("Hashcltids")->encodeHash($str);
}
//解析客户端ID
function decodeCltID($encrypt){
    $cltIDArr = app("Hashcltids")->decodeHash($encrypt);
    if(count($cltIDArr) != 1){
        throw new \Exception("The cltid is error",config("exceptions.CLT_ERROR"));
    }
    $macdecLength = intval(substr($cltIDArr[0],-2));
    $productIDLength = intval(substr($cltIDArr[0],-4,-2));
    $userIDLength = intval(substr($cltIDArr[0],-6,-4));
    return [
        substr($cltIDArr[0],0,$userIDLength),//用户ID
        substr($cltIDArr[0],$userIDLength,$productIDLength),//产品ID
        strtoupper(setMac(dechex(substr($cltIDArr[0],($userIDLength+$productIDLength),$macdecLength))))//mac地址
    ];
}
//生成绑定码
function getBindCode($userID,$mac,$gid){
    $time = time();
    $result = "";
    $mac = parseMac($mac);
    $str = strtolower($userID . "#" . $mac . "#" . $gid . "#" . $time);
    $keyLen = strlen($mac);
    $strLen = strlen($str);
    for($i=0;$i<$strLen;$i++){
        $k = $i % $keyLen;
        $result .= $str[$i] ^ $mac[$k];
    }
    return base64_encode($result);
}
//解析绑定码
function parseBindCode($bind,$key){
    try{
        $result = "";
        $bind = base64_decode($bind);
        $keyLen = strlen($key);
        for($i=0;$i<strlen($bind);$i++){
            $k = $i % $keyLen;
            $result .= $bind[$i] ^ $key[$k];
        }
        $arr = explode("#", $result);
        return [
            $arr[0],//toUid
            strtoupper(setMac($arr[1])),//mac
            $arr[2],//gid
            $arr[3]//created_at
        ];
    }catch(\Exception $e){
        throw new \Exception("The bind code is error",config("exceptions.BINDCODE_ERROR"));
    }
}
//生成topic
function getTopic($prtid,$cltid){
    return sprintf(str_replace("+","%s",config("mqtt.topic.devicedown")),$prtid,$cltid);
}
//发送mqtt消息
function sendToMqtt($topics,$message,$qos=0,$retain=0,$callBack = NULL,$isAsync = true,&$msgID = Null){
    try{
        $isAsync ? app("mqtt")->publishAsync($topics,$message,$qos,$retain,$callBack,$msgID) : app("mqtt")->publishSync($topics,$message,$qos,$retain,$msgID);
        return true;
    }catch(\Exception $e){
        app('log')->error($e->getMessage());
        return false;
    }
}

function getVersion($name){
    $arr = explode("-", $name);
    $str = $arr[count($arr) - 2];
    $str = substr($str, 5);
    return $str;
}

function generateFid(){
    return Carbon::now()->timestamp . getStr(10);
}

function generateOrderid(){
    return "po" . Carbon::now()->timestamp . getStr(8);
}