<?php
// | Author: 枫叶 <735579768@qq.com> <http://www.zhaokeli.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;

/**
 * 后台用户控制器
 * @author 枫叶 <735579768@qq.com>
 */
class MembergroupController extends AdminController {
	function _initialize() {
		parent::_initialize();
		$this->assign('meta_title', '用户组管理');
	}
	function index() {
		$this->pages(array('model' => 'MemberGroup'));
		$this->display();
	}
	function add() {
		$model = D('MemberGroup');
		//取表单数据
		if (IS_POST) {
			if ($model->create()) {
				$result = $model->add();
				if (0 < $result) {
					F('sys_membergroup_list', null);
					$this->success(L('_ADD_SUCCESS_'), U('index'));
				} else {
					$this->error(L('_ADD_FAIL_'));
				}
			} else {
				$this->error($model->getError());
			}
		} else {
			$field = getModelAttr('memberGroup');
			$this->assign('fieldarr', $field);
			$this->assign('data', null);
			$this->assign('meta_title', '添加用户组');
			$this->display('edit');
		}
	}
	function edit() {
		$model = D('MemberGroup');
		if (IS_POST) {
			if ($model->create()) {
				$result = $model->save();
				if (0 < $result) {
					F('sys_membergroup_list', null);
					$this->success(L('_UPDATE_SUCCESS_'), U('index'));
				} else {
					$this->error(L('_UPDATE_FAIL_'));
				}
			} else {
				$this->error($model->getError());
			}
		} else {
			$data  = $model->find(I('get.member_group_id'));
			$field = getModelAttr('memberGroup');
			$this->assign('fieldarr', $field);
			$this->assign('data', $data);
			$this->assign('meta_title', '编辑用户组');
			$this->display();
		}

	}
	function del($member_group_id = null) {
		if ($member_group_id == '1' || $member_group_id == '2') {
			$this->error('系统组不能删除');
		}

		if ($gid = M('MemberGroup')->where('member_group_id=' . $member_group_id)->delete()) {
			$this->success(L('_DELETE_SUCCESS_'));
		} else {
			$this->error(L('_DELETE_FAIL_'));
		}

	}
	/**
	 * 访问授权页面
	 * @author 朱亚杰 <zhuyajie@topthink.net>
	 */
	public function auth($member_group_id = null) {
		if (empty($member_group_id)) {
			$this->error('非法访问');
		}

		$this->meta_title = '访问授权->' . getMemberGroup($member_group_id, 'title');
		if (IS_POST) {
			//处理权限数据
			$model = D('MemberGroup');
			$data  = array(
				'member_group_id' => $member_group_id,
				'auth'            => json_encode(I('post.auth')),
				'update_time'     => NOW_TIME,
			);
			if ($model->create($data)) {
				$result = $model->save();
				F('membegroupnodelist' . $member_group_id, null);
				if (0 < $result) {
					$this->success(L('_UPDATE_SUCCESS_'), U('index'));
				} else {
					$this->error(L('_UPDATE_FAIL_'));
				}
			} else {
				$this->error($model->geterror());
			}
		}

		//分组信息
		$data = getMemberGroup($member_group_id);
		if (!empty($data['auth'])) {
			$data['auth'] = json_decode($data['auth'], true);
			$data['auth'] = '[' . implode(',', $data['auth']) . ']';
		}
		$this->assign('data', $data);
		$this->assign('_list', getNodeList());
		$this->display('managergroup');
	}
}
