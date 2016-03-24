<?php
// | Author: yangweijie <yangweijiester@gmail.com> <code-tech.diandian.com>
// +----------------------------------------------------------------------

namespace Plugins;
use Common\Controller\CommonController;

/**
 * 插件类
 * @author yangweijie <yangweijiester@gmail.com>
 */
class Plugin extends CommonController {
	/**
	 * 视图实例对象
	 * @var view
	 * @access protected
	 */
	protected $view = null;

	/**
	 * $info = array(
	 *  'name'=>'Editor',
	 *  'title'=>'编辑器',
	 *  'description'=>'用于增强整站长文本的输入和显示',
	 *  'status'=>1,
	 *  'author'=>'thinkphp',
	 *  'version'=>'0.1'
	 *  )
	 */
	public $info             = array();
	public $addon_path       = '';
	public $config_file      = '';
	public $custom_config    = '';
	public $admin_list       = array();
	public $custom_adminlist = '';
	public $access_url       = array();

	protected function _initialize() {
		$this->view                         = \Think\Think::instance('Think\View');
		$this->addon_path                   = ADDONS_PATH . $this->getName() . '/View/';
		$TMPL_PARSE_STRING                  = C('TMPL_PARSE_STRING');
		$TMPL_PARSE_STRING['__ADDONROOT__'] = ADDONS_PATH . $this->getName();
		C('TMPL_PARSE_STRING', $TMPL_PARSE_STRING);
		if (is_file($this->addon_path . 'config.php')) {
			$this->config_file = $this->addon_path . 'config.php';
		}
	}

	/**
	 * 模板主题设置
	 * @access protected
	 * @param string $theme 模版主题
	 * @return Action
	 */
	protected function theme($theme) {
		$this->view->theme($theme);
		return $this;
	}

	//显示方法
	protected function display($templateFile = '', $charset = '', $contentType = '', $content = '', $prefix = '') {
		if ($templateFile == '') {
			$templateFile = CONTROLLER_NAME;
		}

		echo ($this->fetch($templateFile));
	}
	public function run() {
		$this->display('content');
	}
	/**
	 * 模板变量赋值
	 * @access protected
	 * @param mixed $name 要显示的模板变量
	 * @param mixed $value 变量的值
	 * @return Action
	 */
	protected function assign($name, $value = '') {
		$this->view->assign($name, $value);
		return $this;
	}

	//用于显示模板的方法
	protected function fetch($templateFile = CONTROLLER_NAME, $content = '', $prefix = '') {
		if (!is_file($templateFile)) {
			$templateFile = $this->addon_path . $templateFile . C('TMPL_TEMPLATE_SUFFIX');
			if (!is_file($templateFile)) {
				throw new \Exception("模板不存在:$templateFile");
			}
		}
		return $this->view->fetch($templateFile);
	}

	public function getName() {
		$class = get_class($this);
		return substr($class, strrpos($class, '\\') + 1, -6);
	}

	public function checkInfo() {
		$info_check_keys = array('name', 'title', 'description', 'status', 'author', 'version');
		foreach ($info_check_keys as $value) {
			if (!array_key_exists($value, $this->info)) {
				return FALSE;
			}

		}
		return TRUE;
	}

	/**
	 * 获取插件的配置数组
	 */
	public function getConfig() {
		return $this->config;
	}
	//必须实现安装
	public function install() {}

	//必须卸载插件方法
	public function uninstall() {}

	//必须插件后台菜单设置
	public function set() {}
}
