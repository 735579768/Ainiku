<?php
namespace Admin\Controller;
defined("ACCESS_ROOT") || die("Invalid access");
class MenuController extends AdminController {
	public function __construct() {
		parent::__construct();
		$this->model_name = 'Menu';
		//$this->primarykey='id';
	}
	/**
	 * 配置管理
	 * @author 枫叶 <735579768@qq.com>
	 */
	public function index() {
		//$list=M('menu')->where("hide=0 and pid=0")->select();
		$this->assign('meta_title', '菜单列表');
		$tree = $this->getTree(0, 'id,title,url,hide,sort,pid,group,is_dev,status');
		$this->assign('_TREE_', $tree);
		//trace(APP_DEBUG);
		$this->display();
	}
	/**
	 * 获取分类树，指定分类则返回指定分类极其子分类，不指定则返回所有分类树
	 * @param  integer $id    分类ID
	 * @param  boolean $field 查询字段
	 * @return array          分类树
	 * @author 枫叶 <735579768@qq.com>
	 */
	private function getTree($pid = 0, $field = true, $allrows = false) {
		/* 获取所有分类 */
		$map  = array('status' => array('gt', -1), 'pid' => $pid);
		$list = array();
		if ($allrows) {
			$list = M('Menu')->field($field)->where($map)->order('is_dev desc,hide asc,sort asc,`group` desc')->select();
		} else {
			$list = $this->pages(array(
				'model'  => 'Menu',
				'where'  => $map,
				'order'  => 'is_dev desc,hide asc,sort asc,`group` desc',
				'$field' => $field,
				'rows'   => 10,
			));
		}
		return $list;
	}
	/**
	 * 显示分类树，仅支持内部调
	 * @param  array $tree 分类树
	 * @author 枫叶 <735579768@qq.com>
	 */
	public function tree($tree = null) {
		if (is_string($tree)) {
			$tree = $this->getTree($tree, 'id,title,url,hide,sort,pid,group,is_dev,status', true);
		}
		$this->assign('_TREE_', $tree);
		$this->display('tree');
	}
	function sorts() {
		$map['pid']  = 0;
		$map['hide'] = 0;
		$list        = M('Menu')->where($map)->field('id,title,pid,sort')->select();
		foreach ($list as $key => $val) {
			$map['pid']          = $val['id'];
			$list[$key]['child'] = M('Menu')->where($map)->field('id,title,pid,sort,`group`')->order('`group` asc,sort asc')->select();
		}
		$this->assign('_list', $list);
		$this->display();
	}
	function add($pid = '') {
		if (IS_POST) {
			F('sys_menu_tree', null);
			$model = D('Menu');
			$data  = $model->create();
			if ($data) {
				if ($model->add()) {
					$this->success(L('_ADD_SUCCESS_'), U('index'));
				} else {
					$this->error(L('_ADD_FAIL_'));
				}
			} else {
				$this->error($model->getError());
			}
		} else {
			//$field=Api('Model/menuModel');
			$field = get_model_attr('menu');
			$this->assign('fieldarr', $field);
			$data = array();
			if (!empty($pid)) {
				$data['pid'] = $pid;
			}

			$this->assign('data', $data);
			$this->assign('meta_title', '添加菜单');
			$this->display('edit');
		}
		F("sys_menu_tree", null);
	}
	/**
	 * 编辑配置
	 * @author yangweijie <yangweijiester@gmail.com>
	 */
	public function edit($id = 0) {
		if (IS_POST) {
			F('sys_menu_tree', null);
			$Menu = D('Menu');
			$data = $Menu->create();
			if ($data) {
				if ($Menu->save() !== false) {
					$this->success(L('_UPDATE_SUCCESS_'));
				} else {
					$this->error(L('_UPDATE_FAIL_'));
				}
			} else {
				$this->error($Menu->getError());
			}
		} else {
			$info = array();
			/* 获取数据 */
			$data  = M('Menu')->find($id);
			$field = get_model_attr('menu');
			$this->assign('fieldarr', $field);
			$this->assign('data', $data);
			$this->meta_title = '编辑菜单';
			$this->display();
		}
	}
	/**
	 * 删除后台菜单
	 * @author yangweijie <yangweijiester@gmail.com>
	 */
	public function del($id = null) {
		F('sys_menu_tree', null);
		$tem = M('menu')->where('pid=' . $id)->find();
		if (!empty($tem)) {
			$this->error('请删除子菜单后再操作');
		} else {
			$result = M('Menu')->where("id=$id")->delete();
			if ($result) {
				$this->success(L('_DELETE_SUCCESS_'));
			} else {
				$this->error(L('_DELETE_FAIL_'));
			}
		}
	}
}