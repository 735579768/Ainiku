<?php
namespace Tool\Controller;
use Common\Controller\CommonController;

class IndexController extends CommonController {
	public function index() {
		die();
	}
	/**
	 * 取数据表里的所有字段
	 * @param  [type] $table_name [description]
	 * @return [type]             [description]
	 */
	private function getAllField($table_name) {
		$con_yc    = 'mysql://root:123456@localhost:3306/yc_oxisi';
		$fieldlist = M()->db(1, $con_yc)->query("SHOW COLUMNS FROM $table_name ");
		$flist     = [];
		foreach ($fieldlist as $k => $v) {
			$flist[] = $v['field'];
		}
		return $flist;
	}

	/**
	 * 移动指定数据表的数据
	 * @param  [type] $table_name [description]
	 * @return [type]             [description]
	 */
	private function moveTableData($table_name) {
		$con_lc = 'mysql://root:123456@localhost:3306/oxisi';
		$con_yc = 'mysql://root:123456@localhost:3306/yc_oxisi';
		//转移用户表数据
		$ctable = strtolower(preg_replace('/([A-Z])/', "_$1", $table_name));
		// echo ($ctable);
		$flist    = $this->getAllField('oasis' . $ctable);
		$datalist = M($table_name, 'oasis_', $con_yc)->select();

		//要添加的数据数组
		$adddata = null;
		foreach ($datalist as $key => $value) {
			$tem = null;
			foreach ($flist as $k => $v) {
				$tem[$v] = empty($value[$v]) ? '' : $value[$v];
			}
			$adddata[] = $tem;
		}
		$result = M($table_name, 'oasis_', $con_lc)->addAll($adddata);
		return $result;
	}
	/**
	 * 从旧数据库导入数据到新数据库
	 * 转移地址http://oasis.loc/kefu.php?m=Admin&c=Ostool&a=moveData
	 */
	public function moveData() {
		$con_lc = 'mysql://root:123456@localhost:3306/oxisi';
		$con_yc = 'mysql://root:123456@localhost:3306/yc_oxisi';
		//先清空本地数据表
		$deltable = ['Userinfo', 'Userfb', 'Fangan', 'Fanganmx', 'Order', 'Adminlogs', 'Cart', 'Loginlogs', 'Logs', 'Logsid', 'Msg', 'Msgdemo', 'Notepad', 'Notice', 'Seo', 'Sms', 'Tuidan', 'Userlog', 'Usertpl', 'Orderinfo', 'Userid', 'KfActionlog', 'KfFile', 'KfMemberLog', 'KfPicture', 'KfFile'];
		foreach ($deltable as $key => $value) {
			$result = M($value, 'oasis_', $con_lc)->where('1=1')->delete();
		}
		//需要转移数据的用户表
		$movetable = ['Userid', 'Order', 'Orderinfo', 'Tuidan', 'Userinfo', 'Usertpl', 'Userfb', 'KfPicture', 'Fangan', 'Fanganmx'];
		foreach ($movetable as $key => $value) {
			//清除本地数据
			M($value, 'oasis_', $con_lc)->where('1=1')->delete();
			var_dump($value . ' : ' . $this->moveTableData($value));
		}

	}
}