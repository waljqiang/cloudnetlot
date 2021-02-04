<?php
namespace Modules\Admin\Services;

use App\Services\BaseService;
use App\Repositories\CacheRepository;
use Modules\Admin\Repositories\ProductRepository;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Mail;

class ProductService extends BaseService{
	protected $productRepository;
	protected $cacheRepository;

	public function __construct(ProductRepository $productRepository,CacheRepository $cacheRepository){
		$this->productRepository = $productRepository;
		$this->cacheRepository = $cacheRepository;
	}

	public function getList($admin,$params){
		$pageIndex = array_get($params,"pageIndex",1);
		$pageOffset = array_get($params,"pageOffset",10);
		$sortKey = array_get($params,"sortKey","created_at");
		$sort = array_get($params,"sort","desc");
		$keyword = array_get($params,"keyword",NULL);
		$total = $this->productRepository->getTotal($admin->id,$keyword);
		$list = [];
		if(($pageIndex-1) * $pageOffset <= $total){
			$products = $this->productRepository->getList($admin->id,$pageIndex,$pageOffset,$sortKey,$sort,$keyword);
			if(!empty($products)){
				foreach ($products as $product) {
					$list[] = [
						"prtid" => $product->prtid,
						"uid" => $product->uid,
						"username" => $product->username,
						"prtname" => $product->name,
						"type" => $product->type,
						"size" => $product->size,
						"status" => $product->aud_status,
						"created_at" => convUnixToZoneGm($product->created_at,$product->timeZone,$product->isSummerTime),
					];
				}
			}
		}
		
		return [
			"total" => $total,
			"list" => $list
		];
	}

	public function getInfo($admin,$params){
		$prtid = array_get($params,"prtid");
		$product = $this->productRepository->getProducts([["prtid",$prtid]],["user" => function($query)use($admin){
			$query->where([
				["admin_id",$admin->id],
				["status",1]
			]);
		}],["*"],true);
		if(!$product){
			throw new \Exception("The product is not exists",config("exceptions.PRT_NO"));
		}
		$user = $product->user;
		return [
			"prtid" => $prtid,
			"prtname" => $product->name,
			"type" => $product->type,
			"size" => $product->size,
			"status" => $product->aud_status,
			"describe" => $product->describe,
			"uid" => $product->uid,
			"username" => $user->username,
			"created_at" => convUnixToZoneGm($product->created_at,$user->timeZone,$user->isSummerTime)
		];
	}

	public function approve($admin,$params){
		$prtid = array_get($params,"prtid");
		$enable = array_get($params,"enable",1);
		$lang = array_get($params,"lang","zh-cn");
		$time = Carbon::now()->timestamp;

		$product = $this->productRepository->getProducts([["prtid",$prtid]],["user" => function($query) use ($admin){
			$query->where([
				["admin_id",$admin->id],
				["status",1]
			]);
		}],["*"],true);

		if(!$product){
			throw new \Exception("The product is not exists",config("exceptions.PRT_NO"));
		}

		if($product->aud_status != 2){
			throw new \Exception("The status of the product is not allowed",config("exceptions.PRT_STATUS_NO_ALLOW"));
		}

		$product->aud_status = $enable == 1 ? 4 : 3;
		$product->updated_at = $time;
		if(!$product->save()){
			throw new \Exception("Failure",config("exceptions.MYSQL_EXEC_ERROR"));
		}
		$user = $product->user;
		//清除注册信息
		$this->cacheRepository->clearRegisterByPrtid($prtid);
		//发邮件通知用户
		$email = $user->email;
		$trans = config("public.mailtodevelopwithprtpub.trans." . $lang);
		$trans["lang1"] = sprintf($trans["lang1"],$user->username);
		$trans["lang2"] = sprintf($trans["lang2"],$prtid);
		$trans["lang3"] = sprintf($trans["lang3"],$prtid);
		$subject = array_pull($trans,"subject");
		Mail::send("emails.mailtodevelopwithprtpub",[
			"flag" => (boolean)$enable,
			"username" => $user->username,
			"body" => $trans,
			"time" => convUnixToZoneGm($time,$user->timeZone,$user->isSummerTime)
		],function($message) use ($email,$subject){
			$message->to($email)->subject($subject);
		});
		return [];
	}

}