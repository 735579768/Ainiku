<?php
namespace Admin\Model;
use Think\Model;

defined("ACCESS_ROOT") || die("Invalid access");
class GoodsModel extends BaseModel {
	protected $_validate = array(
		array('title', 'require', '产品标题不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
		array('title', '', '已经存在同样标题的产品', self::EXISTS_VALIDATE, 'unique'),
		array('price', 'require', '产品价格不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
		array('price', '/^(\d+?)\.?(\d*?)$/', '产品价格格式不正确', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
		array('stock', 'require', '产品库存不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
		array('stock', '/^\d{1,5}$/', '产品库存不合法', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
		array('meta_title', '1,50', '网页标题不能超过50个字符', self::VALUE_VALIDATE, 'length', self::MODEL_BOTH),
		array('keywords', '1,255', '网页关键字不能超过255个字符', self::VALUE_VALIDATE, 'length', self::MODEL_BOTH),
		array('meta_title', '1,255', '网页描述不能超过255个字符', self::VALUE_VALIDATE, 'length', self::MODEL_BOTH),
	);

	protected $_auto = array(
		array('create_time', NOW_TIME, self::MODEL_INSERT),
		array('update_time', NOW_TIME, self::MODEL_BOTH),
		//array('status', '1', self::MODEL_INSERT),
	);
}