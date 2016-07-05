<?php
namespace Plugins\Notepad;
// require_once path_a('/Plugins/Plugin.class.php');
class NotepadPlugin extends \Plugins\Plugin {
	protected $config = array(
		'version' => '1.0',
		'author'  => 'qiaokeli',
		'name'    => '记事本',
		'descr'   => '记事本',
	);
	//钩子默认的调用方法
	public function run($a = '', $b = '') {
		//当前页和上一页
		$p = I('get.p', 1);
		$this->assign('cur_p', $p);
		$this->assign('next_p', $p + 1);
		$this->assign('prev_p', ($p === 1) ? 1 : ($p - 1));
		$map = null;
		$this->pages(array(
			'model' => 'PluginNotepad',
			'where' => $map,
			'order' => 'notepad_id desc',
			'rows'  => 10,
		));
		$this->display('floatlist');
	}

	/**
	 * 编辑
	 * @return [type] [description]
	 */
	public function saveNotepad() {
		$notepad_id = I('post.notepad_id');
		if (IS_POST) {
			$model  = M('PluginNotepad');
			$result = 0;
			if (empty($notepad_id)) {
				$result = $model->add(array(
					'content'     => I('post.content'),
					'uid'         => UID,
					'create_time' => NOW_TIME,
					'update_time' => NOW_TIME,
				));
				if ($result > 0) {
					$this->ajaxreturn(array(
						'status' => 1,
						'info'   => '添加成功',
						'data'   => $this->getAjaxLists(),
					));
				} else {
					$this->error('添加失败');
				}
			} else {
				$result = $model->where(array('notepad_id' => $notepad_id))->save(array(
					'content'     => I('post.content'),
					'update_time' => NOW_TIME,
				));
				if ($result > 0) {
					$this->ajaxreturn(array(
						'status' => 1,
						'info'   => '更新成功',
						'data'   => $this->getAjaxLists(),
					));
				} else {
					$this->error('更新失败');
				}
			}

		} else {

		}
	}
	private function getAjaxLists() {
		//当前页和上一页
		$p = I('get.p', 1);
		$this->assign('cur_p', $p);
		$this->assign('next_p', $p + 1);
		$this->assign('prev_p', ($p === 1) ? 1 : ($p - 1));
		$map = null;
		$this->pages(array(
			'model' => 'PluginNotepad',
			'where' => $map,
			'order' => 'notepad_id desc',
			'rows'  => 10,
		));
		return $this->fetch('ajaxlist');
	}
	public function ajaxList() {

		$this->ajaxreturn(array(
			'status' => 1,
			'info'   => 'success',
			'data'   => $this->getAjaxLists(),
		));
	}
	public function edit() {
		$notepad_id = I('get.notepad_id');
		if (empty($notepad_id)) {
			$this->assign('info', $null);
		} else {

			$info = M('PluginNotepad')->find($notepad_id);
			$this->assign('info', $info);
		}
		$this->display('add');
		die();
	}
	public function delNotepad() {
		$notepad_id = I('get.notepad_id');
		$result     = 0;
		if (!empty($notepad_id)) {
			$result = M('PluginNotepad')->delete($notepad_id);
		}
		if ($result > 0) {
			$this->success("删除成功<script>$('#notepad_{$notepad_id}').remove();</script>");
		} else {
			$this->error('删除失败');
		}
		die();
	}
	public function getConfig() {
		return $this->config;
	}
	public function install() {
		if (MODULE_NAME !== 'Admin') {
			die('');
		}

		$prefix = C('DB_PREFIX');
		$sql    = <<<sql
				DROP TABLE IF EXISTS `{$prefix}plugin_notepad`;
				CREATE TABLE `{$prefix}plugin_notepad` (
				  `notepad_id` int(11) NOT NULL AUTO_INCREMENT,
				  `uid` int(11) DEFAULT 0,
				  `content` varchar(255) DEFAULT '',
				  `create_time` int(11) DEFAULT 0,
				  `update_time` int(11) DEFAULT 0,
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;
sql;
		$arr = explode(';', $sql);
		foreach ($arr as $val) {
			if (!empty($val)) {
				M()->execute($val);
			}
		}
		//向后台添加菜单，如果不添加的话直接返回真
		$data = array(
			'title' => '记事本', //插件后台菜单名字
			'pid'   => ADDONS_MENU, //不用改变
			'url'   => 'Addons/plugin?pn=Notepad&pm=set', //填写后台菜单url名称和方法
			'group' => '已装插件', //不用改变
			'type'  => 'Notepad', //填写自己的插件名字
		);
		//添加到数据库
		if (M('Menu')->add($data)) {
			return true;
		} else {
			return false;
		}
	}
	public function uninstall() {
		//只允许后台访问
		if (MODULE_NAME !== 'Admin') {
			die('');
		}

		$prefix = C('DB_PREFIX');
		$sql    = <<<sql
						DROP TABLE IF EXISTS `{$prefix}plugin_notepad`;
sql;
		$arr = explode(';', $sql);
		foreach ($arr as $val) {
			if (!empty($val)) {
				M()->execute($val);
			}

		}
		//删除后台添加的菜单，如果没有直接返回真
		$map['type'] = 'Notepad';
		if (M('Menu')->where($map)->delete()) {
			return true;
		} else {
			return false;
		}
	}
	public function tijiao() {
		$this->success('提交成功');
	}
	public function set() {
		//插件工菜单后台设置,没有的话直接返回真
		if (IS_POST) {
			$data = array(
				'update_time' => NOW_TIME,
				'title'       => I('post.title'),
				'url'         => I('post.url'),
				'sort'        => I('post.sort'),
			);
			$model  = M('Addons');
			$result = $model->where("mark='Notepad'")->save(array('param' => json_encode($data)));
			if (0 < $result) {
				$this->success('保存成功');
			} else {
				$this->error('保存失败');
			}
		} else {
			$data = M('Addons')->field('param')->where("mark='Notepad'")->find();
			$this->assign('info', json_decode($data['param'], true));
			$str = $this->fetch('config');
			return $str;
		}

	}
	/**
	 * 记事本列表
	 * @return [type] [description]
	 */
	public function lists() {
		$map = null;
		$this->pages(array(
			'model' => 'PluginNotepad',
			'where' => $map,
			'order' => 'notepad_id desc',
		));
		return $this->fetch('lists');
	}
}
