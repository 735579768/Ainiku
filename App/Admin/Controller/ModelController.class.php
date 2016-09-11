<?php
// | Author: 枫叶 <735579768@qq.com> <http://www.zhaokeli.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;
/**
 * 后台用户控制器
 * @author 枫叶 <735579768@qq.com>
 */
class ModelController extends AdminController {
	public function __construct() {
		parent::__construct();
		$this->model_name = 'Model';
		//$this->primarykey='model_id';
	}
	public function index() {
		$title         = I('title');
		$map['title']  = array('like', '%' . $title . '%');
		$map['status'] = array('egt', 0);
		$this->pages(array(
			'model' => 'Model',
			'where' => $map,
			'order' => 'model_id desc',
		));
		$this->meta_title = '表单模型列表';
		$this->display();
	}
	public function add() {
		if (IS_POST) {
			$model = D('Model');
			if ($model->create()) {
				$result = $model->add();
				if ($result) {
					$this->addnewtable(I('post.table'));
					$this->success(L('_ADD_SUCCESS_'), U('Modelattr/index', array('model_id' => $result)));
				} else {
					$this->error(L('_UNKNOWN_ERROR_'));
				}
			} else {
				$this->error($model->geterror());
			}
		} else {
			$field                 = get_model_attr('model');
			$data['search_format'] = <<<eot
title:标题
eot;
			$data['list_format'] = <<<eot
article_id:编号
title:标题|<span class="el">[title]</span>
category_id|get_category_title:分类|<span class="el">[category_id]</span>
update_time|time_format:最后更新
status|[extra]:状态
views:浏览
article_id:操作:[EDIT]&id=[article_id]|编辑|btn,[DEL]&id=[article_id]|移动到回收站|btn btn-danger
eot;
			$data['recycle_format'] = <<<eot
article_id:编号
title:标题|<span class="el">[title]</span>
category_id|get_category_title:分类|<span class="el">[category_id]</span>
update_time|time_format:最后更新
status|[extra]:状态
views:浏览
article_id:操作:[HUIFU]&id=[article_id]|恢复|btn,[DELE]&id=[article_id]|彻底删除|btn btn-danger
eot;
			$this->meta_title = '添加模型';
			$this->assign('data', $data);
			$this->assign('fieldarr', $field);
			$this->display('edit');
		}

	}
	//判断有没有这个数据表没有的话添加
	private function addnewtable($table_name) {
		$tablename  = get_table($table_name);
		$table_name = get_table($table_name, false);
		$sql        = "SHOW TABLES LIKE '{$tablename}'";
		$res        = M()->execute($sql);
		if ($res <= 0) {
			$sql = <<<sql
	CREATE TABLE `{$tablename}`(
		`{$table_name}_id` int UNSIGNED NULL AUTO_INCREMENT,
		`create_time`  int(10) UNSIGNED NULL DEFAULT 0 ,
		`update_time`  int(10) UNSIGNED NULL DEFAULT 0 ,
		 PRIMARY KEY (`{$table_name}_id`)
	 )ENGINE=MyISAM DEFAULT CHARSET=utf8;;
sql;
			$res = M()->execute($sql);
		}
	}
	public function edit($model_id = null) {
		$model = D('Model');
		if (IS_POST) {
			if ($model->create()) {
				$result = $model->save();
				if (0 < $result) {
					$this->addnewtable(I('post.table'));
					$this->success(L('_UPDATE_SUCCESS_'), __PAGEURL__);
				} else {
					$this->error(L('_UNKNOWN_ERROR_'));
				}

			} else {
				$this->error($model->geterror());
			}
		} else {
			if (empty($model_id)) {
				$this->error(L('_ID_NOT_NULL_'));
			}

			$data             = D('Model')->where("model_id=$model_id")->find();
			$field            = get_model_attr('model');
			$this->meta_title = '编辑模型';
			$this->assign('fieldarr', $field);
			$this->assign('data', $data);
			$this->display();
		}

	}
	public function del($model_id = null) {
		//查询模型下面有没有字段
		$list = M('modelAttr')->where("model_id=$model_id")->select();
		if (!empty($list)) {
			$this->error('请删除模型下面的字段再删除');
		}

		$mod        = M('Model')->field('table')->find($model_id);
		$table_name = $mod['table'];

		$result = D('Model')->where("model_id=$model_id")->delete();
		if ($result > 0) {
			//删除数据表
			$tablename = get_table($table_name);
			$sql       = "DROP TABLE IF EXISTS `{$tablename}`";
			$res       = M()->execute($sql);
			$this->success(L('_DELETE_SUCCESS_'), U('index'));
		} else {
			$this->error(L('_UNKNOWN_ERROR_'));
		}

	}
}
