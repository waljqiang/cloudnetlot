<?php
namespace Modules\Home\Repositories;

use App\Repositories\BaseRepository;
use Modules\Home\Entities\Topgraphy;
use Illuminate\Support\Facades\DB;

class TopgraphyRepository extends BaseRepository{
	public function model(){
		return Topgraphy::class;
	}

	//初始化拓扑图
	//返回受影响的行数
	public function init($uid,$gid=""){
		//relation存在topgraphy不存在，直接插入
		$insertSql = "INSERT INTO " . $this->getTable("topgraphy") . "(`uid`,`mac`,`pid`,`group_id`,`created_at`,`updated_at`) SELECT m.`uid`,m.`mac`,m.`pid`,n.`group_id`,m.`created_at`,m.`updated_at` FROM " . $this->getTable("device_relation") . " m," .$this->getTable("device") . " n WHERE m.`uid`= ? AND m.`mac` NOT IN (SELECT p.`mac` FROM " . $this->getTable("topgraphy") . " p WHERE p.`uid`= ?) AND m.`uid`=n.`user_id` AND m.`mac`=n.`dev_mac`";
		$insertArr = [$uid,$uid];
		//更新未编辑过且父设备变化的设备
		$updateSql = "UPDATE " . $this->getTable("topgraphy") . " m INNER JOIN " . $this->getTable("device_relation") . " n SET m.`pid`=n.`pid`,m.`updated_at`=n.`updated_at` WHERE m.`uid`=? AND m.`is_edit`=0 AND m.`uid`=n.`uid` AND m.`mac`=n.`mac` AND m.`pid` != n.`pid`";
		$updateArr = [$uid];
		//删除topgraphy中多余设备,除根设备外
		$deleteSql = "DELETE FROM " . $this->getTable("topgraphy") . " WHERE `uid`=? AND `is_edit`=0 AND `mac` != '0' AND `mac` NOT IN(SELECT `mac` FROM " . $this->getTable("device_relation") . " WHERE `uid`=?)";
		$deleteArr = [$uid,$uid];
		if(!empty($gid)){
			$insertSql .= " AND n.`group_id` = ?";
			$insertArr[] = $gid;
			$updateSql .= " AND m.`group_id` = ?";
			$updateArr[] = $gid;
			$deleteSql .= " AND `group_id` = ?";
			$deleteArr[] = $gid;
		}
		DB::beginTransaction();
		$rs1 = DB::affectingStatement($insertSql,$insertArr);
		$rs2 = DB::update($updateSql,$updateArr);
		$rs3 = DB::delete($deleteSql,$deleteArr);
		$rs2 = 0;
		$rs3 = 0;
		if($rs1 !== false && $rs2 !== false && $rs3 !== false){
			DB::commit();
			return $rs1 + $rs2 + $rs3;
		}else{
			DB::rollBack();
			throw new \Exception("Failure",config("exceptions.MYSQL_EXEC_ERROR"));
		}
	}

	public function getTopgraphy($uid,$macs,$gid){
		$sql = 'SELECT m.`uid`,m.`mac`,m.`pid`,m.`is_edit`,m.`is_virture`,m.`point_x`,m.`point_y`,CASE WHEN m.`is_virture`=0 THEN n.`name` else p.`name` END AS name,CASE WHEN m.`is_virture`=0 THEN n.`type` ELSE "" END AS type,CASE WHEN `is_virture`=0 THEN n.`mode` else p.`mode` END AS mode FROM ' . $this->getTable('topgraphy') . ' m LEFT JOIN ' . $this->getTable('device') . ' n ON m.`mac`=n.`dev_mac` LEFT JOIN ' . $this->getTable('device_virture') . ' p ON m.`mac`=p.`mac` AND m.`uid`=p.`uid` AND p.`group_id`= ? WHERE m.`uid`= ? AND m.`group_id` = ? AND(m.`mac`="0" OR m.`mac` IN (SELECT `mac` FROM ' . $this->getTable('device_virture') . ' WHERE `uid`= ? AND `group_id`= ?)';
		if(empty($macs)){
			$sql .= ')';
			$binds = [$gid,$uid,$gid,$uid,$gid];
		}else{
			$sql .= ' OR m.`mac` IN (' . implode(',',array_fill(0,count($macs),'?')) . '))';
			$binds = array_merge([$gid,$uid,$gid,$uid,$gid],$macs);
		}
		$datas = DB::select($sql,$binds);
		if(empty($datas)){
			return [];
		}else{
			foreach ($datas as $data) {
				$res[] = [
					"uid" => $data->uid,
					"mac" => $data->mac,
					"pid" => $data->pid,
					"is_edit" => $data->is_edit,
					"is_virture" => $data->is_virture,
					"point_x" => $data->point_x,
					"point_y" => $data->point_y,
					"name" => is_null($data->name) ? "" : $data->name,
					"mode" => is_null($data->mode) ? "" : $data->mode,
					"type" => $data->type
				];
			}
			return $res;
		}
	}

	public function getRealDevices($uid,$macs,$gid){
		$sql = "SELECT m.`uid`,m.`mac`,m.`pid`,m.`is_edit`,m.`is_virture`,m.`point_x`,m.`point_y`,n.`name`,n.`mode`,n.`type` FROM " . $this->getTable("topgraphy") . " m LEFT JOIN " . $this->getTable("device") . " n ON m.`mac`=n.`dev_mac` AND m.`uid`=n.`user_id` AND m.`group_id`=n.`group_id` WHERE m.`uid`=? AND m.`mac` != '0' AND m.`group_id`=?";
		$binds = [$uid,$gid];
		if(!empty($macs)){
			$sql .= " AND m.`mac` IN (" . implode(',',array_fill(0,count($macs),'?')) . ")";
			$binds = array_merge($binds,$macs);
		}
		$datas = DB::select($sql,$binds);
		if(empty($datas)){
			return [];
		}else{
			foreach ($datas as $data) {
				$res[] = [
					"uid" => $data->uid,
					"mac" => $data->mac,
					"pid" => $data->pid,
					"is_edit" => $data->is_edit,
					"is_virture" => $data->is_virture,
					"point_x" => $data->point_x,
					"point_y" => $data->point_y,
					"name" => is_null($data->name) ? "" : $data->name,
					"mode" => is_null($data->mode) ? "" : $data->mode,
					"type" => $data->type
				];
			}
			return $res;
		}
	}

}