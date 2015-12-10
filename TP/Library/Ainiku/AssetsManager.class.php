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
	private $sourcePath      = array(
		// __ROOT__ . '/Public/' . MODULE_NAME . '/' . C('DEFAULT_THEME') . '/css',
		// __ROOT__ . '/Public/Static/css',
		// __ROOT__ . '/Public/' . MODULE_NAME . '/' . C('DEFAULT_THEME') . '/js',
		// __ROOT__ . '/Public/Static/js',

	);
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
}