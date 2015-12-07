<?php
// | Author: 枫叶 <735579768@qq.com> <http://www.zhaokeli.com>
// +----------------------------------------------------------------------

namespace Admin\Model;
use Think\Model;

/**
 * 用户模型
 * @author 寞枫 <735579768@qq.com>
 */

class ModuleModel extends BaseModel {
	protected $_validate = array(
		array('title', 'require', '名称不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
		array('title', '', '名称已经存在', self::EXISTS_VALIDATE, 'unique'),
	);
}
