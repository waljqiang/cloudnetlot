<?php
namespace Modules\Develop\Services;

use App\Services\BaseService;
use App\Repositories\CacheRepository;
use Modules\Develop\Repositories\ProductRepository;
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

	public function register($user,$params){
		if(!$user->develop || $user->develop->aud_status != 3){
			throw new \Exception("You are not the developer",config("exceptions.NO_PERMISSTION"));
		}
		$productName = array_get($params,"productname");
		$type = array_get($params,"type");
		$size = array_get($params,"size");
		$productDes = array_get($params,"productdes");
		$time = Carbon::now()->timestamp;
		$product = $this->productRepository->getInfos([["uid",$user->id],["type",$type],["size",$size]],[],["*"],true);
		if($product){
			throw new \Exception("The product is already register",config("exceptions.PRT_REGISTERED"));
		}
		$prtid = generatePrtid($user->id);
		$productID = $this->productRepository->add([
			"prtid" => $prtid,
			"uid" => $user->id,
			"name" => $productName,
			"type" => $type,
			"size" => $size,
			"aud_status" => 1,
			"describe" => $productDes,
			"created_at" => $time,
			"updated_at" => $time
		]);
		if(!$productID){
			throw new \Exception("Failure",config("exceptions.MYSQL_EXEC_ERROR"));
		}
		return ["prtid" => $prtid];
	}

	public function getList($user,$params){
		if(!$user->develop || $user->develop->aud_status != 3){
			throw new \Exception("You are not the developer",config("exceptions.NO_PERMISSTION"));
		}
		$pageIndex = array_get($params,"pageIndex",1);
		$pageOffset = array_get($params,"pageOffset",10);
		$keyword = array_get($params,"keyword",NULL);
		$sortKey = array_get($params,"sortKey","created_at");
		$sort = array_get($params,"sort","desc");
		$conditions = [
			["uid",$user->id]
		];
		if(!is_null($keyword)){
			$keyword = "%" . $keyword . "%";
			array_push($conditions,[function($query)use($keyword){
				$query->where([
					["name","like",$keyword],
					["type","like",$keyword,"or"],
					["size","like",$keyword,"or"]
				]);
			}]);
		}
		$total = $this->productRepository->counts($conditions);
		if(($pageIndex-1) * $pageOffset > $total){
			$list = [];
		}else{
			$list = $this->productRepository->getInfos($conditions,[],["*"],false,[$sortKey,$sort],[$pageIndex,$pageOffset])->map(function($product) use ($user){
				return [
					"prtid" => $product->prtid,
					"uid" => $product->uid,
					"name" => $product->name,
					"type" => $product->type,
					"size" => $product->size,
					"status" => $product->aud_status,
					"created_at" => convUnixToZoneGm($product->created_at,$user->timeZone,$user->isSummerTime)
				];
			});
		}
		return [
			"total" => $total,
			"list" => $list
		];
	}

	public function getInfo($user,$params){
		if(!$user->develop || $user->develop->aud_status != 3){
			throw new \Exception("You are not the developer",config("exceptions.NO_PERMISSTION"));
		}
		$prtid = array_get($params,"prtid");
		$product = $this->productRepository->getInfos([
			["prtid",$prtid],
			["uid",$user->id]
		],[],["*"],true);
		if(!$product){
			throw new \Exception("Product is not exists",config("exceptions.PRT_NO"));
		}
		return [
			"prtid" => $product->prtid,
			"uid" => $product->uid,
			"username" => $user->username,
			"name" => $product->name,
			"type" => $product->type,
			"size" => $product->size,
			"describe" => $product->describe,
			"status" => $product->aud_status,
			"created_at" => convUnixToZoneGm($product->created_at,$user->timeZone,$user->isSummerTime)
		];
	}

	public function save($user,$params){
		if(!$user->develop || $user->develop->aud_status != 3){
			throw new \Exception("You are not the developer",config("exceptions.NO_PERMISSTION"));
		}
		$prtid = array_get($params,"prtid");
		$productname = array_get($params,"productname","");
		$productdes = array_get($params,"productdes","");
		$product = $this->productRepository->getInfos([
			["prtid",$prtid],
			["uid",$user->id]
		],[],["*"],true);
		if(!$product){
			throw new \Exception("Product is not exists",config("exceptions.PRT_NO"));
		}
		if($product->aud_status == 2){
			throw new \Exception("The status of the product is not allowed to modify",config("exceptions.PRT_STATUS_NO_ALLOW"));
		}
		if(!empty($productname)){
			$product->name = $productname;
		}
		if(!empty($productdes)){
			$product->describe = $productdes;
		}
		$product->aud_status = 1;
		$product->updated_at = Carbon::now()->timestamp;
		if(!$product->save()){
			throw new \Exception("Failure",config("exceptions.MYSQL_EXEC_ERROR"));
		}
		//清除注册信息
		$this->cacheRepository->clearRegisterByPrtid($prtid);
		return [];
	}

	public function delete($user,$params){
		if(!$user->develop || $user->develop->aud_status != 3){
			throw new \Exception("You are not the developer",config("exceptions.NO_PERMISSTION"));
		}
		$prtid = array_get($params,"prtid");
		$product = $this->productRepository->getInfos([
			["prtid",$prtid],
			["uid",$user->id]
		],[],["*"],true);
		if(!$product){
			throw new \Exception("Product is not exists",config("exceptions.PRT_NO"));
		}
		if(!$product->delete()){
			throw new \Exception("Failure",config("exceptions.MYSQL_EXEC_ERROR"));
		}
		//清除注册信息
		$this->cacheRepository->clearRegisterByPrtid($prtid);
		return [];
	}

	public function publish($user,$params){
		if(!$user->develop || $user->develop->aud_status != 3){
			throw new \Exception("You are not the developer",config("exceptions.NO_PERMISSTION"));
		}
		$prtid = array_get($params,"prtid");
		$lang = array_get($params,"lang","zh-cn");
		$time = Carbon::now()->timestamp;
		$product = $this->productRepository->getInfos([
			["prtid",$prtid],
			["uid",$user->id]
		],[],["*"],true);
		if(!$product){
			throw new \Exception("Product is not exists",config("exceptions.PRT_NO"));
		}
		if($product->aud_status != 1){
			throw new \Exception("The status of the product is not allowed to modify",config("exceptions.PRT_STATUS_NO_ALLOW"));
		}
		$product->aud_status = 2;
		$product->updated_at = $time;
		if(!$product->save()){
			throw new \Exception("Failure",config("exceptions.MYSQL_EXEC_ERROR"));
		}
		//清除注册信息
		$this->cacheRepository->clearRegisterByPrtid($prtid);
		//发邮件给管理员
		$email = $user->admin->email;
		$trans = config("public.mailtoadminwithprtpub.trans." . $lang);
		$trans["lang2"] = sprintf($trans["lang2"],$user->username,$prtid);
		$subject = array_pull($trans,"subject");

		Mail::send("emails.mailtoadminwithprtpub",[
			"username" => $user->admin->username,
			"body" => $trans,
			"time" => convUnixToZoneGm($time,$user->timeZone,$user->isSummerTime)
		],function($message) use ($email,$subject){
			$message->to($email)->subject($subject);
		});
		//清除注册信息
		$this->cacheRepository->clearRegisterByPrtid($prtid);
		return [];
	}

}