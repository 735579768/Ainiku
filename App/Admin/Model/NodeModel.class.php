<?php
// | Author: 枫叶 <735579768@qq.com> <http://www.zhaokeli.com>
// +----------------------------------------------------------------------

namespace Admin\Model;
use Think\Model;

defined("ACCESS_ROOT") || die("Invalid access");
/**
 * 用户模型
 * @author 寞枫 <735579768@qq.com>
 */

class NodeModel extends BaseModel {
	protected $_validate = array(
		array('name', 'require', '操作名字必须填写'), //默认情况下用正则进行验证
		array('title', 'require', '标题必须填写'), //默认情况下用正则进行验证
		array('title', '', '菜单标题已经被占用', self::EXISTS_VALIDATE, 'unique'),
	);
	/**
	 * 获取分类详细信息
	 * @param  milit   $id 分类ID或标识
	 * @param  boolean $field 查询字段
	 * @return array     分类信息
	 * @author 枫叶 <735579768@qq.com>
	 */
	public function info($id, $field = true) {
		/* 获取分类信息 */
		$map = array();
		if (is_numeric($id)) {
			//通过ID查询
			$map['id'] = $id;
		} else {
			//通过标识查询
			$map['title'] = $id;
		}
		return $this->field($field)->where($map)->find();
	}

}
