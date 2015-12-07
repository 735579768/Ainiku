<?php
namespace Plugins\Seokeyword;
require_once pathA('/Plugins/Plugin.class.php');
class SeokeywordPlugin extends \Plugins\Plugin {
	protected $config = array(
		'version' => '1.0',
		'author'  => 'qiaokeli',
		'name'    => '关键字优化',
		'descr'   => '优化文章中的关键字',
	);
	//钩子默认的调用方法
	public function run() {
		$this->display('content');
	}
	public function replace($str) {
		$list = M('Seokeyword')->field('keyword,url')->select();
		$str  = $str[0];
		foreach ($list as $val) {
			$patte = "/(>[^<]*?)({$val['keyword']})([^>]*?<)/";
			$str   = preg_replace($patte, '$1<a target="_blank"  href="' . $val['url'] . '">$2</a>$3', $str);
		}
		return $str;
	}
	public function lists() {
		$starttime = strtotime(I('starttime'));
		$endtime   = strtotime(I('endtime'));
		$map       = array();
		$field     = array(
			'start' => array(
				'field'   => 'starttime',
				'name'    => 'starttime',
				'type'    => 'datetime',
				'title'   => '开始时间',
				'note'    => '',
				'extra'   => null,
				'is_show' => 3,
				'value'   => $starttime,
			),
			'end'   => array(
				'field'   => 'endtime',
				'name'    => 'endtime',
				'type'    => 'datetime',
				'title'   => '结束时间',
				'note'    => '',
				'extra'   => null,
				'is_show' => 3,
				'value'   => $endtime,
			),
		);
		$this->assign('fieldarr', $field);
		$this->assign('data', null);

		if ($starttime != $endtime) {
			if (!empty($starttime) && !empty($endtime)) {
				$map['__DB_PREFIX__seokeyword.create_time'] = array(array('gt', $starttime), array('lt', $endtime), 'and');
			} else if (!empty($starttime)) {
				$map['__DB_PREFIX__seokeyword.create_time'] = array('gt', $starttime);
			} else if (!empty($endtime)) {
				$map['__DB_PREFIX__seokeyword.create_time'] = array('lt', $endtime);
			}
		}

//	 $data=M('Addons')->field('param')->where("mark='Seokeyword'")->find();
		//	  $sp=json_decode($data['param'],true);
		//	  $Seokeywordarr=array();
		//		foreach($sp as $val){
		//			$Seokeywordarr[]=$this->Seokeywordinfo[$val];
		//			}
		//		$this->assign('spinfo',$Seokeywordarr);
		$keyword        = I('keyword');
		$map['keyword'] = array('like', "%$keyword%");
		$this->pages(array(
			'where' => $map,
			'model' => 'Seokeyword',
			'order' => 'id desc',
		));
		return $this->fetch('lists');
	}
	public function delall() {
		$result = M('Seokeyword')->where('1=1') > delete();
		if ($result > 0) {
			$this->success('清空成功');
		} else {
			$this->error('清空失败');
		}
	}
	public function add() {
		$id    = I('id');
		$model = DP('Seokeyword', 'Seokeyword');
		if (IS_POST) {

			if ($model->create()) {
				$result = 0;
				if (empty($id)) {
					$result = $model->add();
					if ($result > 0) {
						$this->success('添加成功', __FORWARD__);
					} else {
						$this->error('添加失败');
					}
				} else {
					$result = $model->save();
					if ($result > 0) {
						$this->success('更新成功', __FORWARD__);
					} else {
						$this->error('更新失败');
					}

				}

			} else {
				$this->error($model->geterror());
			}
		} else {
			$keyword = I('keyword');
			$url     = I('url');
			$field   = array(
				'start' => array(
					'field'   => 'keyword',
					'name'    => 'keyword',
					'type'    => 'string',
					'title'   => '关键字',
					'note'    => '',
					'extra'   => null,
					'is_show' => 3,
					'value'   => $keyword,
				),
				'end'   => array(
					'field'   => 'url',
					'name'    => 'url',
					'type'    => 'string',
					'title'   => '地址',
					'note'    => '',
					'extra'   => null,
					'is_show' => 3,
					'value'   => $url,
				),
			);
			$data = array();
			if (!empty($id)) {
				$data = $model->find($id);
			}
			$this->assign('fieldarr', $field);
			$this->assign('data', $data);
			return $this->fetch('edit');
		}

	}

	public function addinfo() {
		$mar = $this->get_naps_bot();
		if ($mar !== false) {
			$data['Seokeyword_name'] = strtolower($mar);
			$data['url']             = $_SERVER['REQUEST_URI'];
			$data['ip']              = get_client_ip();
			$data['location']        = getIpLocation($data['ip']);
			//$result=M('Seokeyword')->where($data)->setInc('views');
			//if(!$result){
			$data['views']       = 1;
			$data['create_time'] = NOW_TIME;
			M('Seokeyword')->add($data);
			//}
		}
	}
	public function getConfig() {
		return $this->config;
	}
	public function install() {
		$prefix = C('DB_PREFIX');
		$sql    = <<<sql
				DROP TABLE IF EXISTS `{$prefix}seokeyword`;
				CREATE TABLE `{$prefix}seokeyword` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `keyword` varchar(255) DEFAULT NULL,
				  `url` varchar(255) DEFAULT NULL,
				  `create_time` int(11) DEFAULT NULL,
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
			'title' => '关键字优化', //插件后台菜单名字
			'pid'   => ADDONS_MENU, //不用改变
			'url'   => 'Addons/plugin?pn=Seokeyword&pm=lists', //填写后台菜单url名称和方法
			'group' => '已装插件', //不用改变
			'type'  => 'Seokeyword', //填写自己的插件名字
		);
		//添加到数据库
		if (M('Menu')->add($data)) {
			return true;
		} else {
			return false;
		}
	}

	public function uninstall() {
		$prefix = C('DB_PREFIX');
		$sql    = <<<sql
						DROP TABLE IF EXISTS `{$prefix}seokeyword`;
sql;
		$arr = explode(';', $sql);
		foreach ($arr as $val) {
			if (!empty($val)) {
				M()->execute($val);
			}

		}

		//删除后台添加的菜单，如果没有直接返回真
		$map['type'] = 'Seokeyword';
		if (M('Menu')->where($map)->delete()) {
			return true;
		} else {
			return false;
		}
	}
	public function set() {
		if (IS_POST) {
			$data   = I('Seokeyword');
			$model  = M('Addons');
			$result = $model->where("mark='Seokeyword'")->save(array('param' => json_encode($data)));
			if (0 < $result) {
				$this->success('保存成功');
			} else {
				$this->error('保存失败');
			}
		} else {
			$data = M('Addons')->field('param')->where("mark='Seokeyword'")->find();
			$this->assign('info', json_decode($data['param'], true));
			$str = $this->fetch('config');
			return $str;
		}
	}
	function addSeokeywordlog() {
		$searchbot = get_naps_bot(); //判断是不是蜘蛛
		$url       = $_SERVER['HTTP_REFERER']; //来源网站
		//下面判断如果是来自百度的用户或是你网站内部的链接
		if ($searchbot || ($url != '' and strpos($url, 'baidu.com') !== false) || strpos($url, '你网站的域名')) {
			//符合的要求的链接可以进入你的网站
		} else {
			//不符合的话就显示提示信息

			die();
		}
	}
}
