<?php
/**
 *资源管理类
 *到最后渲染的时候这些资源文件会添加到页面的head标签中
 *AssetsManager::getInstance()->register();
 *echo AssetsManager::getInstance()->registerend();
 **/
namespace Ainiku;
class AssetsManager {
	public static $_instance = null;
	private $js              = array();
	private $css             = array();
	private $jsstr           = '';
	private $cssstr          = '';
	private $sourcePath      = array();
	private function __construct() {

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
			$this->js = array_merge($this->js, $conf['js']);
		}
		if (isset($conf['css']) && is_array($conf['css'])) {
			$this->css = array_merge($this->css, $conf['css']);
		}
		$this->js  = array_unique($this->js);
		$this->css = array_unique($this->css);
	}
	/**
	 *输出资源到文件中
	 **/
	public function registerend() {
		//查找真实路径,先从当前模块查找
		foreach ($this->sourcePath as $value) {
			//查找css文件
			foreach ($this->css as $k => $v) {
				$filepath = $value . '/' . $file;
				if (file_exists('.' . $filepath)) {
					$this->cssstr .= '<link href="' . $filepath . '" type="text/css" rel="stylesheet" />' . "\n";
					unset($this->css[$k]);
				}
			}
			//查找js文件
			foreach ($this->js as $k => $v) {
				$filepath = $value . '/' . $file;
				if (file_exists('.' . $filepath)) {
					$this->jsstr .= '<script src="' . $filepath . '" type="text/javascript" ></script>' . "\n";
					unset($this->js[$k]);
				}
			}

		}
		return $this->cssstr . $this->jsstr;
	}
	public function addSourcePath($conf) {
		if (is_string($conf)) {
			$this->sourcePath[] = $conf;
		}
		if (is_array($conf)) {
			$this->sourcePath = array_merge($this->sourcePath, $conf);
		}
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
		return JSMin::minify($js);
	}
}