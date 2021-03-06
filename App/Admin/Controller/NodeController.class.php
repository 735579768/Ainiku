<?php
namespace Admin\Controller;
defined("ACCESS_ROOT") || die("Invalid access");
class NodeController extends AdminController {
	public function __construct() {
		parent::__construct();
		$this->model_name = 'Node';
		//$this->primarykey='node_id';
	}
	/**
	 * 配置管理
	 * @author 枫叶 <735579768@qq.com>
	 */
	public function index() {
		//$list=M('Node')->where("hide=0 and pid=0")->select();
		$this->assign('meta_title', '节点列表');
		$tree = $this->getTree(0, 'node_id,is_all,sort,title,name,pid,status');
		$this->assign('_TREE_', $tree);
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
			$list = M('Node')->field($field)->where($map)->order('sort asc,node_id desc')->select();
		} else {
			$list = $this->pages(array(
				'model'  => 'Node',
				'where'  => $map,
				'order'  => 'sort asc,node_id desc',
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
			$tree = $this->getTree($tree, 'node_id,is_all,sort,title,name,pid,status', true);
		}
		$this->assign('_TREE_', $tree);
		$this->display('tree');
	}
	function add($pid = '') {
		if (IS_POST) {
			F('sys_Node_tree', null);
			$model = D('Node');
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
			//$field=Api('Model/NodeModel');
			$field = get_model_attr('Node');
			$this->assign('fieldarr', $field);
			$data = array();
			if (!empty($pid)) {
				$data['pid'] = $pid;
			}

			$this->assign('data', $data);
			$this->assign('meta_title', '添加节点');
			$this->display('edit');
		}
		F("sys_Node_tree", null);
	}
	/**
	 * 编辑配置
	 * @author yangweijie <yangweijiester@gmail.com>
	 */
	public function edit($node_id = 0) {
		if (IS_POST) {
			F('sys_Node_tree', null);
			$Node = D('Node');
			$data = $Node->create();
			if ($data) {
				if ($Node->save() !== false) {
					$this->success(L('_UPDATE_SUCCESS_'), U('index'));
				} else {
					$this->error(L('_UPDATE_FAIL_'));
				}
			} else {
				$this->error($Node->getError());
			}
		} else {
			$info = array();
			/* 获取数据 */
			$data  = M('Node')->find($node_id);
			$field = get_model_attr('Node');
			$this->assign('fieldarr', $field);
			$this->assign('data', $data);
			$this->meta_title = '编辑节点';
			$this->display();
		}
	}
	/**
	 * 删除后台节点
	 * @author yangweijie <yangweijiester@gmail.com>
	 */
	public function del($node_id = null) {
		F('sys_Node_tree', null);
		$tem = M('Node')->where('pid=' . $node_id)->find();
		if (!empty($tem)) {
			$this->error('请删除子节点后再操作');
		} else {
			$result = M('Node')->where("node_id=$node_id")->delete();
			if ($result) {
				$this->success(L('_DELETE_SUCCESS_'));
			} else {
				$this->error(L('_DELETE_FAIL_'));
			}
		}
	}
}