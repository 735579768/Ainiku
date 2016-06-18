<?php
namespace Home\Controller;
use Common\Controller\CommonController;

class HomeController extends CommonController {
	/**
	 * 前台台控制器初始化
	 */
	protected function _initialize() {
		// plugin('Fangke');
		/* 读取数据库中的配置 */
		$config = F('DB_CONFIG_DATA');

		if (!$config || APP_DEBUG) {
			$config = api('Config/lists');
			F('DB_CONFIG_DATA', $config);
		}
		C($config); //添加配置
		C('TMPL_PARSE_STRING', array(
			'__STATIC__' => __ROOT__ . '/Public/Static',
			'__IMG__'    => __ROOT__ . '/Public/' . MODULE_NAME . '/' . C('DEFAULT_THEME') . '/images',
			'__CSS__'    => __ROOT__ . '/Public/' . MODULE_NAME . '/' . C('DEFAULT_THEME') . '/css',
			'__JS__'     => __ROOT__ . '/Public/' . MODULE_NAME . '/' . C('DEFAULT_THEME') . '/js',
		));
		defined('__DB_PREFIX__') or define('__DB_PREFIX__', C('DB_PREFIX'));
		defined('UID') or define('UID', is_login());
		if (C('WEB_SITE_CLOSE') && UID != 1) {$this->show('网站维护中请稍后访问');die();}
		$str = run_plugin_method('Spider', 'addinfo');
		//var_dump($config);
		if (UID) {
			//登陆的情况
			//赋值当前登陆用户信息
			$uinfo                                   = session('uinfo');
			$map[getAccountType($uinfo['username'])] = $uinfo['username'];
			$jin                                     = __DB_PREFIX__ . "member_group as a on " . __DB_PREFIX__ . "member.member_group_id=a.member_group_id";
			$field                                   = "*," . __DB_PREFIX__ . "member.status as status";
			$user                                    = D('Member')->field($field)->where($map)->join($jin)->find();
			session('uinfo', $user);
			$this->assign('uinfo', $user);
		}
	}
	//重写输出模板
	protected function display($templateFile = '', $charset = '', $contentType = '', $content = '', $prefix = '') {
		$str            = $this->fetch($templateFile);
		$patterns[]     = '/\n\s*\r/';
		$replacements[] = '';

		if (!APP_DEBUG) {
			$patterns[]     = '/<\!\-\-.*?\-\->/';
			$replacements[] = '';
		}
		$regstr = C('TPL_REG');

		$tema = explode('\n', $regstr);
		foreach ($tema as $val) {
			if (strpos($regstr, '#') !== false) {
				$temb = explode('#', $val);
				if (count($temb) === 2) {
					$patterns[]     = $temb[0];
					$replacements[] = $temb[1];
				}
			}
		}
		if (C('SITE_PRELOAD')) {
			$patterns[]     = '/<img(.*?preload.*?)\s{1}src=["|\']([^\'|\"]+?)["|\'](.*?)>/';
			$replacements[] = '<img$1 data-original="$2" src="' . __STATIC__ . '/images/preload.png"$3>';
		}
		$patterns[]     = '/(<img.*?src=["|\'])(["|\'].*?>)/';
		$replacements[] = '$1' . C('DEFAULT_IMG') . '$2';

		$str    = preg_replace($patterns, $replacements, $str);
		$cssjss = \Ainiku\AssetsManager::getInstance()->registerend();
		$str    = str_replace('</head>', "$cssjss</head>", $str);
		echo $str;
	}
}