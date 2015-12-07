<?php
// | Author: 枫叶 <735579768@qq.com> <http://www.zhaokeli.com>
// +----------------------------------------------------------------------
namespace Home\Model;
use Think\Model\RelationModel;

defined("ACCESS_ROOT") || die("Invalid access");

/**
 * 用户模型
 * @author 寞枫 <735579768@qq.com>
 */

class GoodsModel extends RelationModel {
	protected $_link = array(
		'GoodsAttribute' => array(
			'mapping_type'   => self::HAS_MANY,
			'class_name'     => 'GoodsAttribute',
			'mapping_name'   => 'Goodsattribute',
			'parent_key'     => 'goods_id',
			'foreign_key'    => 'goods_id',
			'mapping_order'  => 'goods_attribute_id desc',
			'mapping_fields' => 'name,value',
		),
	);
}
