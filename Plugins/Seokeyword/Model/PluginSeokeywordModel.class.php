<?php
// | Author: 枫叶 <735579768@qq.com> <http://www.zhaokeli.com>
// +----------------------------------------------------------------------
namespace Plugins\Seokeyword\Model;
use Think\Model;

if (!defined("ACCESS_ROOT")) {
	die("Invalid access");
}

/**
 * 用户模型
 * @author 寞枫 <735579768@qq.com>
 */

class PluginSeokeywordModel extends Model {
	protected $_validate = array(
		array('keyword', 'require', '关键字不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
		array('keyword', '', '已经存在同样关键字', self::EXISTS_VALIDATE, 'unique'),
		array('url', 'require', '地址不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
	);

	protected $_auto = array(
		array('create_time', NOW_TIME, self::MODEL_BOTH),
		//array('status', '1', self::MODEL_INSERT),
	);
}
