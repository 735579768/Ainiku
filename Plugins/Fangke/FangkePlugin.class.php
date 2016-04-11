<?php
namespace Plugins\Fangke;
require_once pathA('/Plugins/Plugin.class.php');
class FangkePlugin extends \Plugins\Plugin {
	protected $config = array(
		'version' => '1.0',
		'author'  => 'qiaokeli',
		'name'    => '访客记录',
		'descr'   => '分析网站访问记录',
	);
	//钩子默认的调用方法plugin('Fangke')
	public function run($a, $b) {
		//注册js文件
		$this->assets->registerjs('fangke');
		//$this->record();
	}
	/**
	 * 记录访客信息
	 */
	public function record() {

		$enter_time  = NOW_TIME;
		$ip          = get_client_ip();
		$referer_url = $_SERVER['HTTP_REFERER'];
		$request_url = $_SERVER['REQUEST_URI'];
		$useragent   = $_SERVER['HTTP_USER_AGENT'];
		$views       = cookie('fangke');
		//当前日期的0点
		$cur_time = strtotime(date('Y/m/d'));
		$gqtime   = 24 * 3600 - (NOW_TIME - strtotime(date('Y/m/d'))) - 2;
		if (empty($views)) {
			$views = 1;
			cookie('fangke', $views, $gqtime);
		} else {
			$views++;
			cookie('fangke', $views, $gqtime);
		}
		//var_dump($_SERVER);
		$data = array(
			'ip'          => $ip,
			'referer_url' => $referer_url,
			'request_url' => $request_url,
			'enter_time'  => $enter_time,
			'user-agent'  => $useragent,
			'views'       => 1,
		);
		M('PluginFangke')->add($data);
		die();
	}
	public function chart() {
		$starttime = I('post.starttime', date('Y-m-d', NOW_TIME) . ' 00:00:00');
		$field     = array(
			'start' => array(
				'field'   => 'starttime',
				'name'    => 'starttime',
				'type'    => 'datetime',
				'title'   => '时间',
				'note'    => '',
				'extra'   => null,
				'is_show' => 3,
				'value'   => $starttime,
			),
		);
		$this->assign('fieldarr', $field);
		$this->assign('data', null);
		//trace($starttime);
		//当前一天0点时间
		$curday  = strtotime($starttime);
		$onehour = strtotime('2015-06-01 01:00:00') - strtotime('2015-06-01 00:00:00');

		//查询每个小时的访问量
		$viewnums   = array(0);
		$duliipnums = array(0);

		//设置坐标点
		$_y = 0;
		//查询浏览次数
		for ($i = 1; $i <= 24; $i++) {
			$map['enter_time'] = array(array('gt', $curday), array('lt', $curday + $onehour), 'and');
			$nums              = M('PluginFangke')->field('sum(views) views')->where($map)->select();
			$nums              = $nums[0]['views'];
			$nums              = empty($nums) ? 0 : $nums;
			$viewnums[]        = $nums;
			if ($nums > $_y) {
				$_y = $nums;
			}

			$curday += $onehour;
		}
		//查找独立ip数据
		//$curday = strtotime(date('Y-m-d', NOW_TIME) . ' 00:00:00');
		$curday = strtotime($starttime);
		for ($i = 1; $i <= 24; $i++) {
			$map['enter_time'] = array(array('gt', $curday), array('lt', $curday + $onehour), 'and');
			$nums              = M('PluginFangke')->distinct(true)->field('ip')->where($map)->select();
			$nums              = count($nums);
			$nums              = empty($nums) ? 0 : $nums;
			$duliipnums[]      = $nums;
			if ($nums > $_y) {
				$_y = $nums;
			}

			$curday += $onehour;
		}

		//平均Y轴
		//$_y    = 100;
		$ydata = array();
		$_y    = ($_y < 6) ? 6 : $_y;
		$dijia = (($_y % 6) > 0) ? (($_y + (6 - ($_y % 6))) / 6) : ($_y / 6);
		for ($i = 0; $i <= 6 * $dijia; $i += $dijia) {
			$ydata[] = $i;
		}
		$this->assign('viewnums', $viewnums);
		$this->assign('duliipnums', $duliipnums);
		$this->assign('ydata', $ydata);
		return $this->fetch('content');
	}
	/**
	 * 访问列表后台查看信息
	 */
	public function lists() {
		//只允许后台访问
		if (MODULE_NAME !== 'Admin') {
			die('');
		}

		//$name        = I('name');
		//$map['name'] = array('like', '%' . $name . '%');
		//$map['status']=array('egt',0);
		$this->pages(array(
			'model' => 'PluginFangke',
			'where' => $map,
			'order' => 'id desc',
		));
		$this->meta_title = "访客记录";
		return $this->fetch('lists');
	}
	function del() {
		//只允许后台访问
		if (MODULE_NAME !== 'Admin') {
			die('');
		}

		//$id=I("id");//I('get.article_id');
		$id = isset($_REQUEST['id']) ? I('get.id') : I("id");
		if (empty($id)) {
			$this->error('请先进行选择');
		}

		$model  = M('PluginFangke');
		$result = $model->where("id in ($id)")->delete();
		if (result) {
			$this->success('已经彻底删除');
		} else {
			$this->error('操作失败');
		}
	}
	/**
	 * 删除记录
	 */
	public function delall() {
		//只允许后台访问
		if (MODULE_NAME !== 'Admin') {
			die('');
		}

		$result = M('PluginFangke')->where("1=1")->delete();
		if (result) {
			$this->success('已经清空', U('index'));
		} else {
			$this->error('操作失败');
		}
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
				DROP TABLE IF EXISTS `{$prefix}plugin_fangke`;
				CREATE TABLE `{$prefix}plugin_fangke` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `referer_url` varchar(255) DEFAULT NULL,
				  `request_url` varchar(255) DEFAULT NULL,
				  `user-agent` varchar(255) DEFAULT NULL,
				  `ip` varchar(255) DEFAULT NULL,
				  `views` int(11) DEFAULT 1,
				  `enter_time` int(11) DEFAULT NULL,
				  `out_time` int(11) DEFAULT NULL,
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
			'title' => '访客记录', //插件后台菜单名字
			'pid'   => ADDONS_MENU, //不用改变
			'url'   => 'Addons/plugin?pn=Fangke&pm=chart', //填写后台菜单url名称和方法
			'group' => '已装插件', //不用改变
			'type'  => 'Fangke', //填写自己的插件名字
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
						DROP TABLE IF EXISTS `{$prefix}plugin_fangke`;
sql;
		$arr = explode(';', $sql);
		foreach ($arr as $val) {
			if (!empty($val)) {
				M()->execute($val);
			}

		}
		//删除后台添加的菜单，如果没有直接返回真
		$map['type'] = 'Fangke';
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
			$result = $model->where("mark='Fangke'")->save(array('param' => json_encode($data)));
			if (0 < $result) {
				$this->success('保存成功');
			} else {
				$this->error('保存失败');
			}
		} else {
			$data = M('Addons')->field('param')->where("mark='Fangke'")->find();
			$this->assign('info', json_decode($data['param'], true));
			$str = $this->fetch('config');
			return $str;
		}

	}
}
