<?php

namespace Modules\Test\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Test\Services\TestService;
use Waljqiang\Encode\Encode;
use App\Utils\HashCode;
use Illuminate\Support\Facades\Redis;
use App\Utils\LuaScripts;

class TestController extends Controller{
    private $testService;

    public function __construct(TestService $testService){
        $this->testService = $testService;
    }

    public function testMac(Request $request){
        $params = $request->all();
        echo '<pre>';
        print_r($params);
        echo '</pre>';
        $user = [
            'id' => 1
        ];
        $user = json_decode(json_encode($user));
        $device = $this->testService->getDevices($user,[],[],['*'],true);
        $device->macs = ["78D38DE18C43"];
        $devices = $this->testService->getDevices($user)->map(function($item){
            $item->macs = ["78D38DE18C43"];
            return $item;
        });
        return compact('device','devices');
        return $devices;
    }

    public function testMqtt(Request $request){
        $c = 0;
        $msg = '[{"fun":2,"comm_id":"0002002157303185193","mac":["1"],"type":"2","params":{"wlan_channel":36,"vap":[{"vap_id":4,"vap_enable":1,"vap_hide_ssid":0,"vap_psk_key":"12345678!@#@#@!","vap_encmode":"9","vap_chiper":"7","vap_ssid":"\u5bb6\u5723\u8bde\u8282\u8fd4\u56de\u952e"}],"Now":1573031851}}]';
        
        for($i=0;$i < 100;$i++){
            $topics[$i] = 'aa/' . $i . '/app2dev';
        }

        try{
            $msg = '[{"fun":2,"comm_id":"0002005157302980513","mac":["1"],"type":"5","params":{"TimeRebootEnable":1,"TimeReboot_Time":"23","Now":1573029805}}]';
            $res = [];
            sendToMqtt($topics,$msg,1,0,['onPuback' => function($mqtt,$object) use (&$res){
                $res[] = $object->getMsgID();
            }]);
            return $res;
        }catch(\Exception $e){
            throw new \Exception($e->getMessage(),$e->getCode());
        }
    }

    public function testLimit(Request $request){
        
    }

    public function testyunlot(Request $request){
        $str = '{"active":{"name":"CPE120M","chip":"RTL8197F-FR350","cpu":"1GHz","flash":"8","ram":"64","mac":"44:D1:FA:08:B9:F5","version":"CPE120M-CPE-V2.0-Build20190927134132","location":{"lat":"36.60332","lng":"109.50051"}}}';
        $encode = new Encode();
        $token = "cloudnetlot";
        $key = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG";
        $encode->init($token,Encode::AES,[
            "key" => $key
        ]);
        $encrypted = $encode->encode($str);

        $encode->setNonce($encrypted["nonce"]);
        $encode->setTimeStamp($encrypted["timestamp"]);
        $encode->setSignature($encrypted["signature"]);
        $decrypted = $encode->decode($encrypted["encrypted"]);

        $authorization = "admin#123456";
        $authhash = HashCode::encrypt($authorization,"cloudnetlot",600,6);
        $authhashdecode = HashCode::decrypt($authhash,"cloudnetlot",6);

        $res = [
            "yunlot" => [
                "str" => $str,
                "encrypted" => [
                    "signature" => $encrypted["signature"],
                    "timestamp" => $encrypted["timestamp"],
                    "nonce" => $encrypted["nonce"],
                    "encrypted" => $encrypted["encrypted"]
                ],
                "decrypted" => $decrypted
            ],
            "hash" => [
                "str" => $authorization,
                "encrypted" => $authhash,
                "decrypted" => $authhashdecode
            ]
        ];
        dd($res);
    }

    public function testOplogCache(Request $request){
        return $this->testService->testOplogCache($request->all());
    }

    public function testLog(Request $request){
        echo 11;
        echo date("Y-m-d H:i:s",time());
        logger("123");
    }

    public function testLua(Request $request){
        $a = Redis::connection()->eval(
            LuaScripts::test(), 1, "cloudnetlot:tag"
        );
        dd($a);
    }

    public function testHash(Request $request){
        return $this->testService->testHash($request->all());
    }

    public function devicesOnline(Request $request){
        return $this->testService->devicesOnline($request->all());
    }
}
