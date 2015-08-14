<?php
// | Author: 枫叶 <735579768@qq.com> <http://www.zhaokeli.com>
// +----------------------------------------------------------------------

namespace Admin\Model;
use Think\Model;

/**
 * 用户模型
 * @author 寞枫 <735579768@qq.com>
 */

class ModuleposModel extends BaseModel {
	    protected $_validate = array(
        array('name', 'require', '模块标识不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
		array('name', '','模块标识已经存在', self::EXISTS_VALIDATE, 'unique'),
        array('title', 'require', '模块名称不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
		array('title', '','模块名称已经存在', self::EXISTS_VALIDATE, 'unique')
    );
}
