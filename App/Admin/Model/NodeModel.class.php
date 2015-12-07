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
	/**
	 * 获取分类树，指定分类则返回指定分类极其子分类，不指定则返回所有分类树
	 * @param  integer $id    分类ID
	 * @param  boolean $field 查询字段
	 * @return array          分类树
	 * @author 枫叶 <735579768@qq.com>
	 */
	public function getTree($id = 0, $field = true) {
		/* 获取当前分类信息 */
		if ($id) {
			$info = $this->info($id);
			$id = $info['id'];
		}

		/* 获取所有分类 */
		$map = array('status' => array('gt', -1));
		$list = $this->field($field)->where($map)->order('sort asc,node_id desc')->select();
		$list = list_to_tree($list, $pk = 'node_id', $pid = 'pid', $child = '_', $root = $id);

		/* 获取返回数据 */
		if (isset($info)) {
			//指定分类则返回当前分类极其子分类
			$info['_'] = $list;
		} else {
			//否则返回所有分类
			$info = $list;
		}

		return $info;
	}
}
