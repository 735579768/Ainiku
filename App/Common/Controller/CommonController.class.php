<?php
//所有模块共用的一个类
namespace Common\Controller;
use Think\Controller;

class CommonController extends Controller {
	protected $assets;
	public function __construct() {
		parent::__construct();
		import('Ainiku.AssetsManager');
		$this->assets = \Ainiku\AssetsManager::getInstance();
		//设置资源路径
		$this->assets->addSourcePath(array(
			__ROOT__ . '/Public/' . MODULE_NAME . '/' . C('DEFAULT_THEME') . '/css',
			__ROOT__ . '/Public/' . MODULE_NAME . '/' . C('DEFAULT_THEME') . '/js',
			__ROOT__ . '/Public/Static/css',
			__ROOT__ . '/Public/Static/js',
			__ROOT__ . '/Public/Plugin/css',
			__ROOT__ . '/Public/Plugin/js',
		));
/*//注册css文件
$assets->registercss('reset,common,index,404');
//注册js文件
$assets->registerjs('jquery-1.9.1.min,ajax,functions');

echo ($assets->registerend());
dump($assets);*/
		//查询黑名单
		$ip     = get_client_ip();
		$iplist = C('IP_BLACKLIST');
		$iplist = extra_to_array($iplist);
		if (!empty($iplist)) {
			if (in_array($ip, $iplist)) {
				die('ip is access denied!');
			}

		}

	}
	protected function _empty() {
		if (!APP_DEBUG) {
			//前台统一的404页面
			header('HTTP/1.1 404 Not Found');
			header("status: 404 Not Found");
			$this->display('Public:404');
		} else {
			//调试状态下抛出异常
			E(L('_ERROR_ACTION_') . ':' . ACTION_NAME);
		}

	}
	/**
	 * 前台台控制器初始化
	 */
	protected function _initialize() {
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
		//var_dump($config);
		//		if(!UID){
		//			//没有登陆的情况
		//			 $this->redirect(U('Member/login'));
		//		 }
	}
	/**
	 * 通用分页列表数据集获取方法
	 *
	 *  可以通过url参数传递where条件,例如:  index.html?name=asdfasdfasdfddds
	 *  可以通过url空值排序字段和方式,例如: index.html?_field=id&_order=asc
	 */
	protected function Pages($conf) {
		$model = @$conf['model'];
		$whe   = isset($conf['where']) ? $conf['where'] : '';
		$join  = isset($conf['join']) ? $conf['join'] : '';
		$field = isset($conf['field']) ? $conf['field'] : '';
		$order = isset($conf['order']) ? $conf['order'] : '';
		$rows  = isset($conf['rows']) ? $conf['rows'] : 15;
		$url   = isset($conf['url']) ? $conf['url'] : '';
		$User  = preg_match('/[a-zA-Z0-9]+View/', $model) ? D($model) : M($model);
		$count = 0;
		if (is_string($whe)) {
			$whe = str_replace('__DB_PREFIX__', __DB_PREFIX__, $whe);
		} else {
			if (is_array($whe)) {
				$temarr = array();
				foreach ($whe as $key => $val) {
					$temarr[str_replace('__DB_PREFIX__', __DB_PREFIX__, $key)] = $val;
				}
				$whe = $temarr;
			}
		}
		$field = str_replace('__DB_PREFIX__', __DB_PREFIX__, $field);
		$order = str_replace('__DB_PREFIX__', __DB_PREFIX__, $order);
		if (is_array($join)) {
			$join[0] = str_replace('__DB_PREFIX__', __DB_PREFIX__, $join[0]);
			$join[1] = str_replace('__DB_PREFIX__', __DB_PREFIX__, $join[1]);
			$count   = $User->where($whe)->field($field)->order($order)->join($join[0])->join($join[1])->count(); // 查询满足要求的总记录数
		} else {
			$join  = str_replace('__DB_PREFIX__', __DB_PREFIX__, $join);
			$count = $User->where($whe)->field($field)->order($order)->join($join)->count(); // 查询满足要求的总记录数
		}

		$Page = new \Think\Page($count, $rows); // 实例化分页类 传入总记录数和每页显示的记录数(25)
		if (!empty($url)) {
			$Page->url = $url;
		}

		//$Page->url=$pageurl;
		$Page->setConfig('theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');

		//分页跳转的时候保证查询条件
		$mp = array_merge(I('post.'), I('get.'));
		if (is_array($mp)) {
			foreach ($mp as $key => $val) {
				if (!is_array($val)) {
					$Page->parameter[$key] = to_utf8($val);
				}
			}
		}

		$show = $Page->show(); // 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		if (is_array($join)) {
			$list = $User->where($whe)->field($field)->order($order)->join($join[0])->join($join[1])->limit($Page->firstRow . ',' . $Page->listRows)->select();
		} else {
			$list = $User->where($whe)->field($field)->join($join)->order($order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
		}

		$this->assign('_total', $count);
		$this->assign('_page', $show); // 赋值分页输出
		$this->assign('_list', $list);
		return $list;
	}
	//重写输出模板
	protected function display($templateFile = '', $charset = '', $contentType = '', $content = '', $prefix = '') {
		$str            = $this->fetch($templateFile);
		$patterns[]     = '/\n\s*\r/';
		$replacements[] = '';
		$regstr         = C('TPL_REG');

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
			$patterns[]     = '/<img(.*?)\s{1}src=["|\']([^\'|\"]+?)["|\'](.*?)>/';
			$replacements[] = '<img$1 data-original="$2" src="' . __STATIC__ . '/images/preload.png"$3>';
		}
		$patterns[]     = '/<img(.*?)src=["|\']["|\'](.*?)>/';
		$replacements[] = '<img$1src="' . C('DEFAULT_IMG') . '"$2>';

		$str    = preg_replace($patterns, $replacements, $str);
		$cssjss = \Ainiku\AssetsManager::getInstance()->registerend();
		str_replace('</head>', "$cssjss\n</head>", $str);
		echo $str;
	}
	protected function returnExit($result = 0, $url = '') {
		if ($result > 0) {
			empty($url) ? $this->success('操作成功') : $this->success('操作成功', $url);
		} else {
			$this->error('操作失败');
		}
	}
	/**
	 * 魔术方法 有不存在的操作的时候执行
	 * @access public
	 * @param string $method 方法名
	 * @param array $args 参数
	 * @return mixed
	 */
	public function __call($method, $args) {
		if (0 === strcasecmp($method, ACTION_NAME . C('ACTION_SUFFIX'))) {
			if (method_exists($this, '_empty')) {
				// 如果定义了_empty操作 则调用
				$this->_empty($method, $args);
			} elseif (file_exists_case($this->view->parseTemplate())) {
				// 检查是否存在默认模版 如果有直接输出模版
				$this->display();
			} else {
				E(L('_ERROR_ACTION_') . ':' . ACTION_NAME);
			}
		} else {
			E(__CLASS__ . ':' . $method . L('_METHOD_NOT_EXIST_'));
			return;
		}
	}
}
