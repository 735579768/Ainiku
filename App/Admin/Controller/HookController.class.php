<?php
namespace Admin\Controller;
class HookController extends AdminController {
	public function index() {
		$this->meta_title = '钩子列表';
		$map['name']      = array('like', '%' . I('title') . '%');
		$this->pages(array('model' => 'Hooks', 'where' => $map, 'order' => 'status asc ,id asc'));
		$this->display();
	}
	public function add() {
		if (IS_POST) {
			$model = D('Hooks');
			if ($model->create()) {
				$model->add();
				$this->success(L('_ADD_SUCCESS_'), U('Hook/index'));
			} else {
				$this->error($model->getError());
			}
		} else {
			$this->display();
		}

	}

	/*
		     * 禁用插件
	*/
	public function jinyong($id = null) {
		if (D('Hooks')->jinyong($id)) {
			$this->success('禁用成功', U('index'));
		} else {
			$this->error(L('_CAOZUO_FAIL_'));
		}
	}
	/*
		     * 启用插件
	*/
	public function qiyong($id = null) {
		if (D('Hooks')->qiyong($id)) {
			$this->success('启用成功', U('index'));
		} else {
			$this->error(L('_CAOZUO_FAIL_'));
		}
	}
	/*
		     * 管理插件
	*/
	public function manage($id = null) {
		if (IS_POST) {
			$model = D('Hooks');
			if ($model->create()) {
				$model->save();
				$this->success('保存成功');
				exit();
			} else {
				$this->error('保存失败');
				exit();
			}
		} else {
			$data = M('Hooks')->where("id=$id")->find();
			$this->assign('data', $data);
		}
		//查询已挂载插件列表
		$model = M('Hooks')->where("id=$id")->field('pluginid')->find();
		//$arr=explode('c', $model['pluginid']);
		$pluginlisted = array();
		//if(!empty($arr)){
		$map      = array();
		$pluginid = $model['pluginid'];
		if (!empty($pluginid)) {
			$map['id']    = array('in', $pluginid);
			$pluginlisted = M('Addons')->where($map)->select();
		}
		//查询还没有挂载的插件列表
		$map           = array();
		$map['status'] = 1;
		$pluginlist    = M('Addons')->where($map)->select();
		$temarr        = array();
		foreach ($pluginlist as $val) {
			$str = stripos($model['pluginid'], $val['id']);
			if ($str === false) {
				$temarr[] = $val;
			}
		}
		$this->assign('pluginlist', $temarr);
		$this->assign('pluginlisted', $pluginlisted);
		$this->display();
	}
	/*
		     * 保存插件列表字符串
	*/
	public function savepluginlist($id = null) {
		$result = M('Hooks')->where("id=$id")->save(array('pluginid' => I('post.val')));
// echo M('Hooks')->getlastsql();
		if (!$result) {
			$this->error('保存数据失败');
		}
	}
	public function del($id = null) {
		$model = D('Hooks');
		$rows  = $model->where("id=$id")->find();
		if (empty($rows['pluginid'])) {
			$model->where("id=$id")->delete();
			$this->success(L('_DELETE_SUCCESS_'));
		} else {
			$this->error('钩子上面有插件挂载不能删除', U('Hook/manage?id=' . $id));
		}
	}
}
