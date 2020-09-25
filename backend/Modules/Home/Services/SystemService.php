<?php
namespace Modules\Home\Services;

use App\Services\BaseService;
use Modules\Home\Repositories\DeviceRepository;
use Modules\Home\Repositories\ProductRepository;
use Modules\Home\Repositories\ClientRepository;
use Modules\Home\Repositories\AclRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SystemService extends BaseService{
	private $productRepository;
	private $clientRepository;
	private $aclRepository;
	private $deviceRepository;

	public function __construct(ProductRepository $productRepository,ClientRepository $clientRepository,AclRepository $aclRepository,DeviceRepository $deviceRepository){
		$this->productRepository = $productRepository;
		$this->clientRepository = $clientRepository;
		$this->aclRepository = $aclRepository;
		$this->deviceRepository = $deviceRepository;
	}

	public function getClient($params){
		$appid = array_get($params,"appid");
		$secret = array_get($params,"secret");
		$prtid = array_get($params,"prtid");
		$mac = array_get($params,"mac");
		$type = array_get($params,"type");
		$time = Carbon::now()->timestamp;
		$product = $this->productRepository->getInfos([["prtid",$prtid]],["user","clients" => function($query) use ($mac){
			$query->where("mac",$mac);
		}],["*"],true);
		if(!$product){
			throw new \Exception("The product is not exists",config("exceptions.PRT_NO"));
		}
		if(!$product->user || !$product->user->develop || $product->user->develop->aud_status != 3){
			throw new \Exception("No permission",config("exceptions.NO_PERMISSTION"));
		}
		if($product->user->develop->appid != $appid || $product->user->develop->secret != $secret){
			throw new \Exception("appid or secret error",config("exceptions.APPID_OR_SERCRET_ERROR"));
		}

		$cltid = generateClitid($product->uid,$product->id,$mac);
		if($product->clients->isEmpty()){//没有客户端
			DB::beginTransaction();
			$clientID = $this->clientRepository->add([
				"cltid" => $cltid,
				"uid" => $product->user->id,
				"prtid" => $product->prtid,
				"mac" => $mac,
				"created_at" => $time,
				"updated_at" => $time
			]);

			$rs = $this->aclRepository->addAll([
                [//允许用户发布设备上行主题
                    "allow" => 1,
                    "ipaddr" => NULL,
                    "username" => $product->user->username,
                    "clientid" => NULL,
                    "access" => 2,
                    "topic" => "{$prtid}/{$cltid}/dev2app",
                    "created_at" => $time,
                    "updated_at" => $time
                ],//允许用户订阅设备下行行主题
                [
                    "allow" => 1,
                    "ipaddr" => NULL,
                    "username" => $product->user->username,
                    "clientid" => NULL,
                    "access" => 1,
                    "topic" => "{$prtid}/{$cltid}/app2dev",
                    "created_at" => $time,
                    "updated_at" => $time
                ]
            ]);
            $deviceID = $this->deviceRepository->add([
            	"dev_mac" => $mac,
            	"prt_type" => $product->type,
            	"prt_size" => $product->size,
            	"type" => $type,
            	"created_at" => $time,
            	"updated_at" => $time
            ]);
            if($clientID && $rs && $deviceID){
            	DB::commit();
            }else{
            	DB::rollback();
            	throw new \Exception("Failure",config("exceptions.MYSQL_EXEC_ERROR"));
            }
		}else{
			$cltid = $product->clients->get(0)->cltid;
		}
		$encode = ["type" => config("yunlot.encodetype")];
		if($encode["type"] == 2){
			$encode = array_merge($encode,[
				"token" => config("yunlot.token"),
				"key" => config("yunlot.key")
			]);
		}
		return [
			"protocol" => config("yunlot.protocol"),
			"prtid" => $prtid,
			"cltid" => $cltid,
			"server" => config("mqtt.options.address"),
			"port" => config("mqtt.options.port"),
			"encode" => $encode
		];
	}

}