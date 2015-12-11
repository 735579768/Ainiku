<?php
/**
 *资源管理类
 *到最后渲染的时候这些资源文件会添加到页面的head标签中
 *AssetsManager::getInstance()->register();
 *echo AssetsManager::getInstance()->registerend();
 *使用方法
 *import('Ainiku.AssetsManager');
 *$assets = \Ainiku\AssetsManager::getInstance();
 *设置资源路径
 *$assets->addSourcePath(array(
 *__ROOT__ . '/Public/' . MODULE_NAME . '/' . C('DEFAULT_THEME') . '/css',
 *__ROOT__ . '/Public/' . MODULE_NAME . '/' . C('DEFAULT_THEME') . '/js',
 *__ROOT__ . '/Public/Static/css',
 *__ROOT__ . '/Public/Static/js',
 *));
 *注册css文件
 *$assets->registercss('reset,common,index,404');
 *注册js文件
 *$assets->registerjs('ajax,functions');
 *
 *echo ($assets->registerend());
 *dump($assets);
 *
 *
 *
 **/
namespace Ainiku;
defined('APP_DEBUG') or define('APP_DEBUG', true);
defined('APP_DEBUG') or define('APP_DEBUG', true);
class AssetsManager {
	static public $_instance = null;
	private $js              = array();
	private $css             = array();
	private $jsstr           = '';
	private $cssstr          = '';
	private $sourcePath      = array();
	/**
	 * Description:私有化构造函数，防止外界实例化对象
	 */
	private function __construct() {
	}
	/**
	 * Description:私有化克隆函数，防止外界克隆对象
	 */
	private function __clone() {
	}
	static public function getInstance() {
		if (self::$_instance === null) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	/**
	 *注册资源到类中
	 **/
	public function register($conf = array()) {
		if (isset($conf['js']) && is_array($conf['js'])) {
			$this->registerjs($conf['js']);
		}
		if (isset($conf['css']) && is_array($conf['css'])) {
			$this->registercss($conf['css']);
		}
		$this->js  = array_unique($this->js);
		$this->css = array_unique($this->css);
	}
	/**
	 *重置注册的资源
	 */
	public function resetRegister() {
		$this->js  = array();
		$this->css = array();
	}
	public function registercss($conf) {
		if (is_string($conf)) {
			$conf = explode(',', $conf);
		}
		$this->css = array_merge($this->css, $conf);
	}
	public function registerjs($conf) {
		if (is_string($conf)) {
			$conf = explode(',', $conf);
		}
		$this->js = array_merge($this->js, $conf);
	}
	/**
	 *输出资源到文件中
	 **/
	public function registerend() {
		//查找真实路径,先从当前模块查找
		$ismodcss = false;
		$ismodjs  = false;
		$cssname  = md5(implode($this->css));
		$jsname   = md5(implode($this->js));
		$csscache = STYLE_CACHE_DIR . MODULE_NAME . '/' . $cssname . '.css';
		$jscache  = STYLE_CACHE_DIR . MODULE_NAME . '/' . $jsname . '.js';
		//查找css文件
		foreach ($this->css as $k => $v) {
			$filepath = $this->getFilePath($v, 'css');
			if ($filepath) {
				$this->css[$k] = $filepath;
				if (APP_DEBUG) {
					$this->cssstr .= '<link href="' . $filepath . '" type="text/css" rel="stylesheet" />' . "\n";
				} else {
					if (file_ismod('.' . $filepath) || !file_exists($csscache)) {
						$ismodcss = true;
						$this->cssstr .= $this->compress_css('.' . $filepath);
					}
				}
			} else {
				$this->css[$k] .= '.css--->file is not exists!';
			}
		}
		//查找js文件
		foreach ($this->js as $k => $v) {
			$filepath = $this->getFilePath($v, 'js');
			if ($filepath) {
				$this->js[$k] = $filepath;
				if (APP_DEBUG) {
					$this->jsstr .= '<script src="' . $filepath . '" type="text/javascript" ></script>' . "\n";
				} else {
					if (file_ismod('.' . $filepath) || !file_exists($jscache)) {
						$ismodjs = true;
						$this->jsstr .= $this->compress_js('.' . $filepath);
					}
				}
			} else {
				$this->js[$k] .= '.js--->file is not exists!';
			}
		}
		if (!APP_DEBUG) {
			mkdir(dirname($csscache), 0777, true);
			$ismodcss && file_put_contents($csscache, $this->cssstr);
			$ismodjs && file_put_contents($jscache, $this->jsstr);
			$this->cssstr = '<link href="' . substr($csscache, 1) . '" type="text/css" rel="stylesheet" />' . "\n";
			$this->jsstr  = '<script src="' . substr($jscache, 1) . '" type="text/javascript" ></script>' . "\n";
		}
		return $this->cssstr . $this->jsstr;
	}
	/**
	 *在路径中查找是否存在文件
	 **/
	private function getFilePath($filename, $type = 'css') {
		foreach ($this->sourcePath as $value) {
			$filepath = "{$value}/{$filename}.{$type}";
			if (file_exists('.' . $filepath)) {
				return $filepath;
			}
		}
		return false;
	}
	public function addSourcePath($conf) {
		if (is_string($conf)) {
			$conf = explode(',', $conf);
		}
		$this->sourcePath = array_merge($this->sourcePath, $conf);
	}
	/**
	 *压缩css
	 **/
	private function compress_css($path) {
		$dirname = dirname($path); //当前css文件的路径目录
		$ipath   = $path;
		$str     = '';
		if ($ipath) {
			$str = file_get_contents($ipath);
//把文件压缩
			$arr = array('/(\n|\t|\s)*/i', '/\n|\t|\s(\{|}|\,|\:|\;)/i', '/(\{|}|\,|\:|\;)\s/i');
			$str = preg_replace($arr, '$1', $str);
			$str = preg_replace('/(\/\*.*?\*\/\n?)/i', '', $str);

//查找出样式文件中的图片
			preg_match_all("/url\(\s*?[\'|\"]?(.*?)[\'|\"]?\)/", $str, $out);
			foreach ($out[1] as $v) {
				if (strpos($v, '../images') !== false) {
					$src_new = str_replace("../images", $dirname . "/images", $v); //源绝对路径
					$src_new = str_replace('css/', '', $src_new);
					$new     = str_replace("../images", STYLE_CACHE_DIR . MODULE_NAME . "/images", $v); //设置新路径
					$new     = __SITE_ROOT__ . __ROOT__ . str_replace('./', '/', $new);
					createFolder(dirname($new));
					if (file_exists($src_new)) { //判断是否存在
						copy($src_new, $new); //复制到新目录
					}
				}
			}
			$str = str_replace('../images', './images', $str);
		} else {
			die("path is not found:" . $ipath);
		}
		return $str;
	}
	/**
	 *压缩JS文件并替换JS嵌套include文件
	 */
	private function compress_js($jspath) {
		$js = file_get_contents($jspath);
		import('Ainiku.JSMin');
		return \JSMin::minify($js);
	}
}