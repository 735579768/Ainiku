<?php
namespace Admin\Model;
use Think\Model;

/**
 * 用户模型
 * @author 寞枫 <735579768@qq.com>
 */

class GoodsAttributeModel extends Model {

//更新用户信息
	//@param  $field 一个用户字段数组格式为$field[0],$field[1],...$field['attr_id']
	//@param  $Goods_id 用户id
	public function updateGoodsInfo($Goodsid = null) {
		$msg = array(
			'info' => '操作成功',
			'status' => 1,
		);

		//附加用户信息转为数组
		$data = I('post.');
		$fields = array();
		//取带_的键值说明是附加属性
		foreach ($data as $key => $value) {
			$pos = strpos($key, '____');
			if ($pos !== false) {
				$fields[$key] = $value;
			}
		}
		//更新字段状态
		$fieldbool = true;
		foreach ($fields as $key => $val) {
			//查找有没有这个表单值
			$tem = explode('____', $key);
			//$data2[]=$value;
			$map = array();
			$map['goods_id'] = $Goodsid;
			$map['name'] = $tem[0];
			$map['goods_type_attribute_id'] = $tem[1];
			$temrow = $this->where($map)->select();
			var_dump($this->select());
			var_dump($this->getlastsql());
			var_dump($temrow);
			var_dump($map);
			$map['update_time'] = NOW_TIME;
			$map['value'] = $val;

			if (count($temrow) == 0) {
				//die('run');
				//添加
				$result = $this->add($map);
				if (0 < $result) {
					$msg['info'] = '产品添加成功';
					$msg['status'] = 1;
				} else {
					$msg['info'] = '添加字段时出错';
					$msg['status'] = 0;
				}
			} else {
				die('edit');
				//更新
				$whe['goods_attribute_id'] = $temrow['goods_attribute_id'];
				$result = $this->where($whe)->save($map);
				if (0 < $result) {
					$msg['info'] = '产品更新成功';
				} else {
					$msg['info'] = '更新附加属性时出错';
					$msg['status'] = 0;
				}
			}
		}
		return $msg;
	}
	/**
	 *把post过来的附加用户信息转换成数组
	 */
	private function goodsinfotoarray() {
		//取post值
		$data = I('post.');

		$data2 = array();
		//取带_的键值说明是附加属性
		foreach ($data as $key => $value) {
			$pos = strpos($key, '____');
			if ($pos !== false) {
				$data2[$key] = $value;
			}
		}
		return $data2;
	}
}
