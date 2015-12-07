<?php
// | Author: 枫叶 <735579768@qq.com> <http://www.zhaokeli.com>
// +----------------------------------------------------------------------

namespace Admin\Model;
use Think\Model;
defined("ACCESS_ROOT") || die("Invalid access");
/**
 * 分类模型
 * @author 枫叶 <735579768@qq.com>
 */
class GoodsTypeAttributeModel extends BaseModel{

    protected $_validate = array(
        array('title', 'require', '名称不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
		array('name', 'require', '标识不能为空', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
        array('name', '', '标识已经存在', self::VALUE_VALIDATE, 'unique', self::MODEL_BOTH),
		array('goods_type_id', 'require', '类型id不能为空', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH)
    );

    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_BOTH)
    );
    /**
     * 获取分类详细信息
     * @param  milit   $id 分类ID或标识
     * @param  boolean $field 查询字段
     * @return array     分类信息
     * @author 枫叶 <735579768@qq.com>
     */
    public function info($id, $field = true){
        /* 获取分类信息 */
        $map['goods_type_attribute_id'] = $id;
        return $this->field($field)->where($map)->find();
    }
}
