<?php
//Author: 枫叶 <735579768@qq.com> <http://www.zhaokeli.com>
namespace Admin\Controller;
class ConfigController extends AdminController {
	public function __construct() {
		parent::__construct();
		$this->model_name = 'Config';
		//$this->primarykey='id';
	}
	/**
	 * 配置管理
	 * @author 枫叶 <735579768@qq.com>
	 */
	public function index() {
		/* 查询条件初始化 */
		$grouplist = extra_to_array(C('CONFIG_GROUP'));
		$this->assign('group', $grouplist);

		$group = I('group', 0);
		$title = I('title', '');
		if ($group !== 0) {
			$map['group'] = $group;
		}

		$map['title'] = array('like', '%' . $title . '%');
		$list         = $this->pages(array(
			'model' => 'Config',
			'where' => $map,
			'order' => 'sort asc,config_id desc',
		));

		// $this->assign('group',extra_to_array(C('CONFIG_GROUP')));
		// $this->assign('group_id',I('get.group',0));
		$this->meta_title = '配置列表';
		$this->display();
	}
	function add() {
		$this->meta_title = '添加配置';
		if (IS_POST) {
			$Config = D('Config');
			$data   = $Config->create();
			if ($data) {
				if ($Config->add()) {
					F('DB_CONFIG_DATA', null);

					$this->success(L('_ADD_SUCCESS_'), U('index', array('group' => I('group'))));
				} else {
					$this->error(L('_ADD_FAIL_'));
				}
			} else {
				$this->error($Config->getError());
			}
		} else {
			$field = get_model_attr('config');
			$this->assign('fieldarr', $field);
			$this->assign('data', $data);
			$this->display('edit');
		}
	}
	/**
	 * 编辑配置
	 * @author 枫叶 <735579768@qq.com>
	 */
	public function edit($config_id = 0) {
		if (IS_POST) {

			$Config = D('Config');
			$data   = $Config->create();
			if ($data) {
				if (0 < $Config->save()) {

					F('DB_CONFIG_DATA', null);
					//$this->success(L('_UPDATE_SUCCESS_'), U('index', array('group' => I('group'))));
					$this->success(L('_UPDATE_SUCCESS_'), __PAGEURL__);
				} else {
					$this->error('没有更改');
				}
			} else {
				$this->error($Config->getError());
			}
		} else {
			$info = array();
			/* 获取数据 */
			$info = M('Config')->field(true)->find($config_id);
			if (false === $info) {
				$this->error('获取配置信息错误');
			}
			$field = get_model_attr('config');
			$this->assign('data', $info);
			$this->assign('fieldarr', $field);
			$this->meta_title = '编辑配置';
			$this->display();
		}
	}
	/**
	 * 配置分组
	 * @author 枫叶 <735579768@qq.com>
	 */
	function group($id = null) {
		$this->meta_title = '网站配置';
		$id               = $id ? $id : 1;
		$grouplist        = extra_to_array(C('CONFIG_GROUP'));
		$this->assign('group', $grouplist);
		$this->assign('id', $id);
		//循环每个分组的模型
		$data = null;
		if (APP_DEBUG) {
			$data = M('Config')->where("`group`=$id")->order('sort asc,config_id desc')->select();
		} else {
			$data = M('Config')->where("`group`=$id and no_del=1")->order('sort asc,config_id desc')->select();
		}
		//处理extra数据
		//进入单个分组中后对每个进行处理
		foreach ($data as $k => $v) {
			$nme                 = isset($v['name']) ? $v['name'] : '';
			$data[$k]['data']    = $v['value'];
			$data[$k]['field']   = 'config__' . $nme . '';
			$data[$k]['name']    = 'config__' . $nme . '';
			$data[$k]['note']    = $v['note'] . '。标识:' . $nme;
			$data[$k]['is_show'] = 3;
			$redata              = parse_string_function($v['extra']);
			if ($redata === false) {
				if (!empty($v['extra']) && $v['type'] != 'custom') {
					$data[$k]['extra'] = extra_to_array($v['extra']);
				}
			} else {
				$data[$k]['extra'] = $redata;
			}
		}
		// dump($data);
		// die();
		$this->assign('data', $data);
		$this->display();
	}
	/**
	 * 批量保存配置
	 * @author 枫叶 <735579768@qq.com>
	 */
	public function save() {
		if (IS_POST) {
			$config = null;
			$conf   = I('post.');
			foreach ($conf as $key => $val) {
				$a = explode('__', $key);
				if (count($a) === 2) {
					$config[$a[1]] = $val;
				}
			}
			$Config = M('Config');
			foreach ($config as $name => $value) {
				$map = array('name' => $name);
				$Config->where($map)->setField('value', $value);
			}

//			F('DB_CONFIG_DATA',null);
			update_config();
			$this->success('配置保存成功！');
		} else {
			$this->error('非法访问');
		}

	}
	/**
	 * 删除配置
	 * @author 枫叶 <735579768@qq.com>
	 */
	public function del($id = '') {
		//$config_id = array_unique((array)I('get.config_id',0));
		$config_id = $id;
		if (empty($config_id)) {
			$this->error('请选择要操作的数据!');
		}
		$map['config_id'] = array('in', $config_id);
		$map['no_del']    = 0;
		$result           = M('Config')->where($map)->delete();
		if (0 < $result) {
			F('DB_CONFIG_DATA', null);
			$this->success(L('_DELETE_SUCCESS_'));
		} else {
			$this->error('删除失败！系统配置不允许删除！');
		}
	}
}