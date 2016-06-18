<?php
// | Author: 枫叶 <735579768@qq.com> <http://www.zhaokeli.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;
defined("ACCESS_ROOT") || die("Invalid access");
/**
 * 后台用户控制器
 * @author 枫叶 <735579768@qq.com>
 */
class ModuleController extends AdminController {
	public function index() {
		$moduleposlist = F('sys_modulepos_tree');
		if (empty($menulist)) {
			$moduleposlist = F_getmoduleposList();
			F('sys_modulepos_tree', $moduleposlist);
		}
		$moduleposlist[0] = '全部位置';
		$field            = array(
			array(
				'field'   => 'modulepos_id',
				'name'    => 'modulepos_id',
				'type'    => 'select',
				'title'   => '模块信息位置置',
				'note'    => '',
				'extra'   => $moduleposlist,
				'is_show' => 3,
				'value'   => I('modulepos_id'),
			),
		);
		$this->assign('fieldarr', $field);
		$this->assign('data', null);

		$modulepos_id = I('modulepos_id');
		$title        = I('title');
		//$modulepos_id=I('modulepos_id');
		//if(!empty($cat_id))$map['modulepos_id']=$cat_id;
		if (!empty($modulepos_id)) {
			$map[__DB_PREFIX__ . 'module.modulepos_id'] = $modulepos_id;
			$this->meta_title                           = '模块信息列表>模块信息位置>' . getmoduleposTitle($modulepos_id);
		} else {
			$this->meta_title = '模块信息列表';
		}
		$map[__DB_PREFIX__ . 'module.title'] = array('like', '%' . $title . '%');
		//$map[__DB_PREFIX__.'module.status']=1;
		$this->pages(array(
			'model' => 'Module',
			'field' => '*,' . __DB_PREFIX__ . 'module.module_id as module_id,' . __DB_PREFIX__ . 'module.sort as sort,' . __DB_PREFIX__ . 'module.title as title,b.title as postitle,' . __DB_PREFIX__ . 'module.status as status',
			'join'  => __DB_PREFIX__ . 'modulepos as b on b.modulepos_id=' . __DB_PREFIX__ . 'module.modulepos_id',
			'where' => $map,
			'order' => __DB_PREFIX__ . 'module.modulepos_id,status asc,' . __DB_PREFIX__ . 'module.sort asc,' . __DB_PREFIX__ . 'module.module_id desc',
		));

		$this->display();
	}
	public function add() {
		if (IS_POST) {
			$model = D('Module');
			if ($model->create()) {
				$result = $model->add();
				if (0 < $result) {
					$this->success(L('_ADD_SUCCESS_'), U('index', array('modulepos_id' => I('modulepos_id'))));
				} else {
					$this->error(L('_UNKNOWN_ERROR_'));
				}
			} else {
				$this->error($model->geterror());
			}
		} else {
			//$field=Api('Model/AdModel');
			$field            = get_model_attr('Module');
			$this->meta_title = '添加模块信息';
			$this->assign('fieldarr', $field);
			$this->assign('data', null);
			$this->display('edit');
		}

	}
	public function edit($module_id = null) {
		$model = D('Module');
		if (IS_POST) {
			if ($model->create()) {
				$result = $model->save();
				if ($result !== false) {
					$this->success(L('_UPDATE_SUCCESS_'), U('Module/index', array('modulepos_id' => I('modulepos_id'))));
				} else {
					$this->error(L('_UNKNOWN_ERROR_'));
				}

			} else {
				$this->error($model->geterror());
			}
		} else {
			if (empty($module_id)) {
				$this->error(L('_ID_NOT_NULL_'));
			}

			$data = M('Module')->where("module_id=$module_id")->find();

			//$field=Api('Model/AdModel');
			$field            = get_model_attr('Module');
			$this->meta_title = '编辑模块信息';
			$this->assign('fieldarr', $field);
			$this->assign('data', $data);
			$this->display();
		}

	}
	public function del($module_id) {
		$result = M('Module')->where("module_id=$module_id")->delete();
		if ($result !== false) {
			$this->success(L('_DELETE_SUCCESS_'), U('Module/index'));
		} else {
			$this->error(L('_UNKNOWN_ERROR_'));
		}

	}
}
