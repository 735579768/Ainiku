<?php
namespace Plugins\Comments;
class CommentsPlugin extends \Plugins\Plugin {
	protected $config = array(
		'version' => '1.0',
		'author'  => 'qiaokeli',
		'name'    => '留言插件',
		'descr'   => '留言',
	);
	//钩子默认的调用方法
	public function run($arc_id = 0) {
		$this->add($arc_id);
	}
	public function ajaxlist($arc_id = 0, $pid = 0) {
		// empty($arc_id)&&die('没有评论');
		$map['status'] = 1;
		$map['pid']    = $pid;
		$map['arc_id'] = I('get.arc_id');
		$list          = $this->pages(array(
			'model' => 'PluginComments',
			'where' => $map,
			'order' => 'create_time desc',
			'rows'  => 2,
		));
		if (empty($list)) {
			die('没有评论');
		} else {
			$this->assign('_list', $list);
			$this->_page = preg_replace('/href\=\"(.*?)\"/i', 'href="javascript:;" data-url="$1"', $this->_page);
			$info        = $this->fetch('ajaxlist');
			$this->ajaxreturn(array('status' => 1, 'data' => $info));
		}
		die();
	}
	public function gettree($pid = 0) {
		if ($pid === 0) {return '';}
		$info          = M('PluginComments')->field('name')->find($pid);
		$map['pid']    = $pid;
		$map['status'] = 1;
		$list          = M('PluginComments')->where($map)->order('create_time desc')->select();
		$this->assign('pname', $info['name']);
		$this->assign('_list', $list);
		if (empty($list)) {
			echo '';
		} else {
			echo $this->fetch('tree');
		}

	}
	public function add($arc_id = 0) {

		if (IS_POST) {
			$verify = I('verify');
			if (empty($verify)) {$this->error('请输入验证码!');}
			if (!check_verify($verify)) {
				$this->error('验证码输入错误！');
			}
			$model = new \Plugins\Comments\PluginCommentsModel();
			if ($model->create()) {
				$model->url = preg_replace('/http\:\/\//i', '', $model->url);
				cookie('comment_name', $model->name, 3600 * 24 * 365);
				cookie('comment_email', $model->email, 3600 * 24 * 365);
				cookie('comment_url', $model->url, 3600 * 24 * 365);
				$result = $model->add();
				if (0 < $result) {
					$list[] = M('PluginComments')->find($result);
					$this->assign('_list', $list);
					$str = $this->fetch('ajaxlist');
					//检测发送邮件
					$this->sendemail();
					$this->success(array(
						'content' => $str,
						'msg'     => '留言成功',
						'pid'     => I('post.pid'),
					));
				} else {
					$this->error('留言失败');
				}
			} else {
				$this->error($model->geterror());
			}
		} else {
			//取插件配置参数
			$conf = F('plugin_comments');
			if (empty($conf) || APP_DEBUG) {
				$data = M('Addons')->field('param')->where("mark='Comments'")->find();
				$conf = json_decode($data['param'], true);
				F('plugin_comments', $conf);
			}
			$this->assign('arc_id', $arc_id);
			$this->assign('conf', $conf);
			$this->display('add');
		}
	}
	/**
	 * 有留言时回复
	 * @return [type]       [description]
	 */
	private function sendemail() {
		$pid = I('post.pid', 0);
		$url = C('WEBDOMIN') . "/article/{$rows['arc_id']}.html";
		//给站长发邮件
		$site_email = C('SITE_EMAIL');
		$site_title = C('WEB_SITE_TITLE');
		if (!empty($site_email)) {
			$result = send_mail(array(
				'to'       => $site_email,
				'toname'   => $site_title . '站长你好',
				'subject'  => "你有一条最新留言 ( $site_title )",
				'fromname' => $site_title,
				'body'     => "文章留言链接 <a target='_blank' href='$url'>点击查看: $url</a>",

			));
		}
		//给被回复的人发邮件
		if ($pid != 0) {
			$rows = M('PluginComments')->field('name,arc_id,email,email_notify')->find($pid);
			if (empty($rows) || empty($rows['email'])) {
				return;
			} else {
				if ($rows['email_notify'] != '1') {
					return;
				}
				$result = send_mail(array(
					'to'       => $rows['email'],
					'toname'   => '你好' . $rows['name'],
					'subject'  => '你好' . $rows['name'] . ': 赵克立博客有您的留言回复',
					'fromname' => $site_title,
					'body'     => "回复链接 <a target='_blank' href='$url'>点击查看回复: $url</a>",

				));
				if ($result !== true) {
					\Think\log::write('留言发送发送邮件时出错:' . $result, 'WARN');
				}
			}
		}
	}
	public function install() {
		if (MODULE_NAME !== 'Admin') {
			die('');
		}

		$prefix = C('DB_PREFIX');
		$sql    = <<<sql
				DROP TABLE IF EXISTS `{$prefix}plugin_comments`;
				CREATE TABLE `{$prefix}plugin_comments` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `pid` int(11)  NULL DEFAULT 0 ,
				  `arc_id` int(11)  NULL DEFAULT 0 ,
				  `title` varchar(255) DEFAULT NULL,
				  `content` varchar(255) DEFAULT NULL,
				  `name` varchar(255) DEFAULT NULL,
				  `mobile` varchar(255) DEFAULT NULL,
				  `email` varchar(255) DEFAULT NULL,
				  `url` varchar(255) DEFAULT NULL,
				  `qq` varchar(255) DEFAULT NULL,
				  `status` tinyint(1)  NULL DEFAULT 1 ,
				  `email_notify` tinyint(1)  NULL DEFAULT 1 ,
				  `ip` varchar(255) DEFAULT NULL,
				  `location` varchar(255) DEFAULT NULL,
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
			'title' => '留言管理', //插件后台菜单名字
			'pid'   => ADDONS_MENU, //不用改变
			'url'   => 'Addons/plugin?pn=Comments&pm=lists', //填写后台菜单url名称和方法
			'group' => '已装插件', //不用改变
			'type'  => 'Comments', //填写自己的插件名字
		);
		//添加到数据库
		if (M('Menu')->add($data)) {
			return true;
		} else {
			return false;
		}
	}
	public function lists() {
		//只允许后台访问
		if (MODULE_NAME !== 'Admin') {
			die('');
		}

		$name        = I('name');
		$map['name'] = array('like', '%' . $name . '%');
		//$map['status']=array('egt',0);
		$this->pages(array(
			'model' => 'PluginComments',
			'where' => $map,
			'order' => 'status asc,id desc',
		));
		$this->meta_title = "留言列表";
		return $this->fetch('lists');
	}
	public function redirect($url, $params = array(), $delay = 0, $msg = '') {
		$url = I('url');
		empty($url) ? ($url = C('WEBDOMIN')) : ($url = ainiku_decrypt($url));
		$url = preg_replace('/http\:\/\//i', '', $url);
		redirect('http://' . $url);
		die('');
	}
	public function check() {
		//只允许后台访问
		if (MODULE_NAME !== 'Admin') {
			die('');
		}

		$id = I('get.id');
		empty($id) && die('ID不能为空');
		$data = M('PluginComments')->find($id);
		$this->assign('info', $data);
		$this->display('check');
		die();
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

		$model  = M('PluginComments');
		$result = $model->where("id in ($id)")->delete();
		if ($result) {
			$this->success('已经彻底删除');
		} else {
			$this->error('操作失败');
		}
	}
	function delall() {
		//只允许后台访问
		if (MODULE_NAME !== 'Admin') {
			die('');
		}

		$result = M('PluginComments')->where("1=1")->delete();
		if ($result) {
			$this->success('已经清空', U('index'));
		} else {
			$this->error('操作失败');
		}
	}
	public function uninstall() {
		//只允许后台访问
		if (MODULE_NAME !== 'Admin') {
			die('');
		}

		$prefix = C('DB_PREFIX');
		$sql    = <<<sql
						DROP TABLE IF EXISTS `{$prefix}plugin_comments`;
sql;
		$arr = explode(';', $sql);
		foreach ($arr as $val) {
			if (!empty($val)) {
				M()->execute($val);
			}

		}
		//删除后台添加的菜单，如果没有直接返回真
		$map['type'] = 'Comments';
		if (M('Menu')->where($map)->delete()) {
			return true;
		} else {
			return false;
		}
	}
	public function set() {
		$this->meta_title = '留言设置';
		//插件工菜单后台设置,没有的话直接返回真
		if (IS_POST) {
			$data = array(
				'update_time' => NOW_TIME,
				'name'        => I('post.name'),
				'email'       => I('post.email'),
				'url'         => I('post.url'),
			);
			$model  = M('Addons');
			$result = $model->where("mark='Comments'")->save(array('param' => json_encode($data)));
			if (0 < $result) {
				$this->success('保存成功');
			} else {
				$this->error('保存失败');
			}
		} else {
			$data = M('Addons')->field('param')->where("mark='Comments'")->find();
			$this->assign('info', json_decode($data['param'], true));
			$str = $this->fetch('config');
			return $str;
		}
	}
}