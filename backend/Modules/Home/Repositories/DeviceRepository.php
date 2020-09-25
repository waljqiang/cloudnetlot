<?php
namespace Modules\Home\Repositories;

use App\Repositories\BaseRepository;
use Modules\Home\Entities\Device;
use Illuminate\Support\Facades\DB;

class DeviceRepository extends BaseRepository{
	public function model(){
		return Device::class;
	}

	public function getRegister($prtid,$mac){
		$sql = "SELECT m.`uid` AS developUid,m.`prtid`,m.`type`,m.`size`,m.`aud_status`,n.`cltid`,n.`mac`,p.`user_id` AS bindUid,p.`bind` FROM " . $this->getTable("develop_product") . " m LEFT JOIN " . $this->getTable("develop_client") . " n ON m.`prtid`=n.`prtid` AND n.`mac`=? LEFT JOIN " . $this->getTable("device") . " p ON n.`mac`=p.`dev_mac` WHERE m.`prtid`=?";
		$rs = DB::select($sql,[$mac,$prtid]);
		return isset($rs[0]) ? (array)$rs[0] : [];
	}

}