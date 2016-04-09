<?php
namespace Admin\Controller;
defined("ACCESS_ROOT") || die("Invalid access");
class AddonsController extends AdminController {
	public function index() {
		$this->meta_title = '插件管理';
		$dirlist          = getDirList(__SITE_ROOT__ . '/Plugins/');
		$dirlist          = str_replace('Plugin', '', $dirlist);
		$addoninfo        = array();
		foreach ($dirlist as $a) {
			$temarr = runPluginMethod($a, 'getConfig');
			$mark   = $a; //strtolower($temarr['mark']);
			$model  = M('Addons')->where("mark='$mark'")->find();
			if (!empty($model)) {
				//数据库中有插件的信息说明已经安装过啦
				//查询插件是不是有设置页面
				$setmenu = M('Menu')->where("`type`='$mark'")->find();
				//trace($setmenu);
				$addoninfo[] = array(
					'id'      => $model['id'],
					'name'    => $model['name'],
					'mark'    => $model['mark'],
					'author'  => $model['author'],
					'descr'   => $model['descr'],
					'install' => $model['install'],
					'status'  => $model['status'],
					'type'    => $model['type'],
					'setmenu' => $setmenu,
				);
			}
		}
		//trace($addoninfo);
		$list0 = array();
		//  $list1=array();
		//排序
		foreach ($addoninfo as $key => $a) {
			if ($a['install'] == '0') {
				$list0[] = $addoninfo[$key];
				unset($addoninfo[$key]);
				//     }else{
				//  $list1[]=$addoninfo[$key];
			}
		}
		foreach ($addoninfo as $key => $a) {
			if ($a['status'] == '0') {
				$list0[] = $addoninfo[$key];
				unset($addoninfo[$key]);
			}
		}
		foreach ($addoninfo as $key => $a) {
			if ($a['status'] == '1') {
				$list0[] = $addoninfo[$key];
				unset($addoninfo[$key]);
			}
		}
		//$addoninfo=array_merge($list0,$list1);
		$page        = new \Ainiku\Arrpage($list0, I('pg'), 10);
		$this->_list = $page->cur_page_data;
		$this->_page = $page->showpage(false);

		//$this->assign('_list',$list0);
		$this->display();
	}
	/**
	 * 安装新的插件
	 */
	public function newinstall() {
		$this->meta_title = '插件管理';
		$dirlist          = getDirList(__SITE_ROOT__ . '/Plugins/');
		$dirlist          = str_replace('Plugin', '', $dirlist);
		$addoninfo        = array();
		foreach ($dirlist as $a) {
			$temarr = runPluginMethod($a, 'getConfig');
			$mark   = $a; //strtolower($temarr['mark']);
			$model  = M('Addons')->where("mark='$mark'")->find();
			if (empty($model)) {
				//没有信息说明还没有安装
				$temarr['mark']    = $a; //strtolower($temarr['mark']);
				$temarr['install'] = 0;
				$temarr['status']  = 1;
				$temarr['type']    = 'other';
				$addoninfo[]       = $temarr;
			}
		}
		//trace($addoninfo);
		$list0 = array();
		//  $list1=array();
		//排序
		foreach ($addoninfo as $key => $a) {
			if ($a['install'] == '0') {
				$list0[] = $addoninfo[$key];
				unset($addoninfo[$key]);
				//     }else{
				//  $list1[]=$addoninfo[$key];
			}
		}
		foreach ($addoninfo as $key => $a) {
			if ($a['status'] == '0') {
				$list0[] = $addoninfo[$key];
				unset($addoninfo[$key]);
			}
		}
		foreach ($addoninfo as $key => $a) {
			if ($a['status'] == '1') {
				$list0[] = $addoninfo[$key];
				unset($addoninfo[$key]);
			}
		}
		//$addoninfo=array_merge($list0,$list1);
		$page        = new \Ainiku\Arrpage($list0, I('pg'), 10);
		$this->_list = $page->cur_page_data;
		$this->_page = $page->showpage(false);

		//$this->assign('_list',$list0);
		$this->display('index');

	}
	/**
	 * 新添加插件
	 */
	public function newadd() {
		if (IS_POST) {
		} else {
			//$field            = getModelAttr('plugin');
			$field = array(
				array(
					'field'   => 'title',
					'name'    => 'title',
					'type'    => 'string',
					'title'   => '插件名字',
					'note'    => '名称',
					'extra'   => null,
					'value'   => '测试',
					'is_show' => 3,
				),
				array(
					'field'   => 'name',
					'name'    => 'name',
					'type'    => 'string',
					'title'   => '插件标识',
					'note'    => '只能用英文名(单词第一个字母大写)',
					'extra'   => null,
					'is_show' => 3,
				),
				array(
					'field'   => 'author',
					'name'    => 'author',
					'type'    => 'string',
					'title'   => '插件作者',
					'note'    => '',
					'extra'   => null,
					'is_show' => 3,
				),
				array(
					'field'   => 'descr',
					'name'    => 'descr',
					'type'    => 'string',
					'title'   => '插件描述',
					'note'    => ')',
					'extra'   => null,
					'is_show' => 3,
				),
			);
			$this->meta_title = '添加新插件';
			$this->assign('fieldarr', $field);
			$this->assign('data', null);
			$this->display();
		}
	}
	/*
		     * 禁用插件
	*/
	public function jinyong($id = null) {
		if (D('Addons')->jinyong($id)) {
			$this->success('禁用成功', U('index'));
		} else {
			$this->error(L('_CAOZUO_FAIL_'));
		}
	}
	/*
		     * 启用插件
	*/
	public function qiyong($id = null) {
		if (D('Addons')->qiyong($id)) {
			$this->success('启用成功', U('index'));
		} else {
			$this->error(L('_CAOZUO_FAIL_'));
		}
	}
	/*
		     * 安装插件
	*/
	public function install($mark = null) {
		if (empty($mark)) {
			$this->error('非法安装');
		} else {
			$result = runPluginMethod($mark, 'install');
			if (!$result) {
				$this->error('插件安装失败,请联系做作者');
			}

			$data           = runPluginMethod($mark, 'getConfig');
			$data['mark']   = $mark; //strtolower($data['mark']);
			$data['status'] = 0;
			$model          = D('Addons');
			if ($model->create($data)) {
				$model->add();
				$this->success('安装成功', U('index'));
			} else {
				$this->error($model->getError());
			}
		}
//       }
	}
	/*
		     * 卸载插件
	*/
	public function uninstall($id = null) {
		$rows = M('Addons')->where("id=$id")->find();
		$mark = $rows['mark'];
		if (empty($mark)) {
			$this->error('卸载失败');
		} else {
			$result = runPluginMethod($mark, 'uninstall');
			if (!$result) {
				$this->error('插件卸载失败,请联系做作者');
			}

			$result = M('Addons')->where("id=$id")->delete();
			if ($result) {
				$this->success('卸载成功', U('index'));
			} else {
				$this->error('卸载失败');
			}
		}

	}

	/*
		     * 运行插件方法
	*/
	public function plugin($pn = null, $pm = null) {
		$str = runPluginMethod($pn, $pm);
		$this->assign('plugincontent', $str);
		$this->display();
	}
}
