<?php
namespace Home\Controller;
use Think\Controller;

class LoginController extends HomeController {
	protected function _empty() {
		//前台统一的404页面
		header('HTTP/1.1 404 Not Found');
		header("status: 404 Not Found");
		$this->display('Public:404');
		die();
	}
	/**
	 * 前台台控制器初始化
	 */
	protected function _initialize() {
		/* 读取数据库中的配置 */
		$config = S('DB_CONFIG_DATA');
		if (!$config || APP_DEBUG) {
			$config = api('Config/lists');
			S('DB_CONFIG_DATA', $config);
		}
		//trace($config);
		C($config); //添加配置
		//定义数据表前缀
		defined('DBPREFIX') or define('DBPREFIX', C('DB_PREFIX'));
		defined('__DB_PREFIX__') or define('__DB_PREFIX__', C('DB_PREFIX'));
		C('TMPL_PARSE_STRING', array(
			'__STATIC__' => __ROOT__ . '/Public/Static',
			'__IMG__'    => __ROOT__ . '/Public/' . MODULE_NAME . '/' . C('DEFAULT_THEME') . '/images',
			'__CSS__'    => __ROOT__ . '/Public/' . MODULE_NAME . '/' . C('DEFAULT_THEME') . '/css',
			'__JS__'     => __ROOT__ . '/Public/' . MODULE_NAME . '/' . C('DEFAULT_THEME') . '/js',
		));
		define('UID', 1);
		//过滤掉支付接口的异步通知url
		if (CONTROLLER_NAME != 'Buy' && ACTION_NAME != 'dopayok') {
			defined('UID') or define('UID', auto_login());
			if (!UID) {
				//没有登陆的情况
				if (IS_AJAX) {
					$this->error($this->fetch('Public/ajaxlogin'));
				} else {
					redirect(U('Public/login'));
				}

			} else {
				//赋值当前登陆用户信息
				$map['member_id'] = UID;
				$user             = D('MemberView')->where($map)->find();
				$this->uinfo      = $user;
			}
		}

	}

}