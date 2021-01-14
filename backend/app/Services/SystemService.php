<?php
namespace App\Services;

use App\Services\BaseService;
use App\Repositories\CacheRepository;
use App\Repositories\CountryCodeRepository;
use Illuminate\Support\Facades\DB;

class SystemService extends BaseService{
	private $cacheRepository;
	private $countryCodeRepository;

	public function __construct(CacheRepository $cacheRepository,CountryCodeRepository $countryCodeRepository){
		$this->cacheRepository = $cacheRepository;
		$this->countryCodeRepository = $countryCodeRepository;
	}

	public function getCaptcha(){
		$captcha = app("Captcha");
		$code = $captcha->entry();
		$this->cacheRepository->setCaptcha($code,config("captcha.expire"));
		return $captcha->getImage();
	}

	public function checkCaptcha($params){
		$code = array_get($params,"code");
		$captcha = app("Captcha");
		$key = $captcha->authCode($code);
		$sysCode = $this->cacheRepository->getCaptcha($key);
		if(!$captcha->check($sysCode,$code)){
			throw new \Exception("The captcha is invalid",config("exceptions.CAPTCHA_INVALID"));
		}
		$this->cacheRepository->deleteCaptcha($key);
		return [];
	}

	public function getCountryCode($params){
		$lang = array_get($params,"lang","zh-cn");
		$nameKey = "name_" . str_replace("-","_",$lang);
		$datas = $this->countryCodeRepository->getInfos([],[],["*"],false,["id","asc"])->map(function($data)use($nameKey){
			return [
				"name" => $data[$nameKey],
				"short2" => $data["short2"],
				"short3" => $data["short3"],
				"num" => $data["num"],
				"phonecode" => $data["phonecode"]
			];
		});
		return [
			"total" => $datas->count(),
			"list" => $datas
		];
	}

}