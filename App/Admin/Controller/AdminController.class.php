<?php
//Author: 枫叶 <735579768@qq.com> <http://www.zhaokeli.com>
namespace Admin\Controller;
use Common\Controller\CommonController;

defined("ACCESS_ROOT") || die("Invalid access");
class AdminController extends CommonController {
	protected $model_name = null;
	protected $primarykey = null;
	protected $auth       = null;
	protected function _initialize() {
		(get_naps_bot() !== false) && die(''); //不让蜘蛛抓取

		// 获取当前用户ID
		$uid = is_login();
		if ($uid) {
			defined('UID') or define('UID', $uid);
		} else {
			$login = A('Public');
			$uid   = $login->autologin();
			$uid > 0 ? define('UID', $uid) : redirect(U('Public/login'));
		}
		defined('IS_ADMIN') or ((UID == 1) ? define('IS_ADMIN', true) : define('IS_ADMIN', false));

		//先读取缓存配置
		$config = F('DB_CONFIG_DATA');
		if (!$config || APP_DEBUG) {
			$config = api('Config/lists');
			F('DB_CONFIG_DATA', $config);
		}
		C($config); //添加配置
		if (!defined('MAIN_IFRAME')) {
			if (I('mainmenu') == 'true') {
				define('MAIN_IFRAME', 'true');
				C('SHOW_PAGE_TRACE', false);
			} else {
				define('MAIN_IFRAME', 'false');
			}
		}
		$this->assign('MAIN_IFRAME', MAIN_IFRAME);
		//定义数据表前缀
		defined('__DB_PREFIX__') or define('__DB_PREFIX__', C('DB_PREFIX'));
		//主题默认为空
		C('DEFAULT_THEME', '');
		//检查访问权限
		import('Ainiku.Auth');
		$this->auth = new \Ainiku\Auth;
		if (!$this->auth->check()) {
			$this->error('啊哦,没有此权限,请联系管理员！', U(session('uinfo')['admin_index']));
		}

		$this->addForward();
		//设置全局的模板变量
		$this->assign('meta_title', '首页');
		$this->assign('uinfo', session('uinfo'));
		//防止重复请求,如果是主框架请求就只输出个目录菜单
		$this->getMainNav();
		// if (MAIN_IFRAME == 'true' || (CONTROLLER_NAME == 'Index' && ACTION_NAME == 'index')) {
		// 	//取主导航
		// 	$this->getMainNav();
		// 	$this->display(CONTROLLER_NAME . '/' . ACTION_NAME);
		// 	die();
		// }
	}
	private function addForward() {
		// 记录当前列表页的cookie
		$fw = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
		//判断如果是百度来的直接退出
		preg_match('/.*baidu\.com.*/', $fw) && die('');
		//分页记录
		$forward     = cookie('__pageurl__');
		$scontroller = cookie('__controller__');
		if (IS_GET) {
			$pg = I('get.p');
			if (!empty($pg)) {
				$forward = __SELF__;
				cookie('__pageurl__', __SELF__, array('expire' => 600));
			} else {
				if ($scontroller != CONTROLLER_NAME) {
					$forward = '';
					cookie('__pageurl__', ' ', array('expire' => 600));
				}
			}
			//记录当前操作的控制器

			cookie('__controller__', CONTROLLER_NAME, array('expire' => 600));
		}

		defined('__PAGEURL__') || define('__PAGEURL__', $forward);
	}
	/**
	 *取主导航
	 */
	public function getMainNav() {
		if (!IS_AJAX) {
			$nav = F('sys_mainnav');
			if (empty($nav) || APP_DEBUG) {
				$where = "pid=0 and hide=0";
				$nav   = M('menu')->where($where)->order('sort asc')->select();
				F('sys_mainnav', $nav);
			}
			$this->assign('_MAINNAV_', $nav);
		}
	}
	/**
	 *后台模块通用改变状态
	 **/
	public function updatefield($table = '', $id = '', $field = '', $value = '') {
		if (empty($table) || empty($id) || empty($field)) {
			$this->error(L('_PARAM_NOT_NULL_'));
		}
		$data = array(
			$field        => $value,
			'update_time' => NOW_TIME,
		);
		$result = M($table)->where(M($table)->getPk() . "=$id")->save($data);
		(0 < $result) ? $this->success(L('_UPDATE_SUCCESS_')) : $this->error(L('_UPDATE_FAIL_'));
	}
	function setposition($table = null, $id = null, $field = null, $value = null) {
		if (IS_POST) {
			$position = I('position');
			$postr    = implode(',', $position);
			$result   = M($table)->where($table . '_id=' . $id)->save(array($field => $postr));
			if (0 < $result) {
				$this->success(array(status_to_text($postr, $table, $field), $postr));
			}
		} else {
			$url   = U('setposition');
			$stext = status_to_text($value, $table, $field, true);
			$str   = <<<eot
<form id="positionform" class="positionfield" method="post" action="{$url}">
<input type="hidden" name="table" value="{$table}" />
<input type="hidden" name="id" value="{$id}" />
<input type="hidden" name="field" value="{$field}" />
{$stext}
	<a href="javascript:;" onClick=""  style="display:none;">添加</a>
</form>'
eot;
			$this->success($str);
		}
	}
	protected function display($templateFile = '', $charset = '', $contentType = '', $content = '', $prefix = '') {
		$str = $this->fetch($templateFile);
		$str = $this->auth->replaceurl($str);
		//查看是不是tab中的数据
		$mainmenu = I('get.mainmenu');
		if ($mainmenu == 'true') {
			$pattern = '/<\/html>(.+)/si';
			$str     = preg_replace($pattern, '</html>', $str);
		}
		if (!APP_DEBUG) {
			//如果不是调试模式
			$pa = array(
				'/<\!\-\-.*?\-\->/i', //去掉html注释
				'/(\s*?\r?\n\s*?)+/i', //删除空白行
			);
			$pr  = array('', "\n");
			$str = preg_replace($pa, $pr, $str);
		}
		echo $str;
	}
	/**
	 * 操作错误跳转的快捷方法
	 * @access protected
	 * @param string $message 错误信息
	 * @param string $jumpUrl 页面跳转地址
	 * @param mixed $ajax 是否为Ajax方式 当数字时指定跳转时间
	 * @return void
	 */
	protected function error($message = '', $jumpUrl = '', $ajax = false) {
		$this->dispatchJump($message, 0, $jumpUrl, $ajax);
	}

	/**
	 * 操作成功跳转的快捷方法
	 * @access protected
	 * @param string $message 提示信息
	 * @param string $jumpUrl 页面跳转地址
	 * @param mixed $ajax 是否为Ajax方式 当数字时指定跳转时间
	 * @return void
	 */
	protected function success($message = '', $jumpUrl = '', $ajax = false) {
		$this->dispatchJump($message, 1, $jumpUrl, $ajax);
	}
	/**
	 * 默认跳转操作 支持错误导向和正确跳转
	 * 调用模板显示 默认为public目录下面的success页面
	 * 提示页面为可配置 支持模板标签
	 * @param string $message 提示信息
	 * @param Boolean $status 状态
	 * @param string $jumpUrl 页面跳转地址
	 * @param mixed $ajax 是否为Ajax方式 当数字时指定跳转时间
	 * @access private
	 * @return void
	 */
	private function dispatchJump($message, $status = 1, $jumpUrl = '', $ajax = false) {
		if (true === $ajax || IS_AJAX) {
// AJAX提交
			$data           = is_array($ajax) ? $ajax : array();
			$data['info']   = $message;
			$data['status'] = $status;
			$data['url']    = $jumpUrl;
			$this->ajaxReturn($data);
		}
		if (is_int($ajax)) {
			$this->assign('waitSecond', $ajax);
		}

		if (!empty($jumpUrl)) {
			$this->assign('jumpUrl', $jumpUrl);
		}

		// 提示标题
		$this->assign('msgTitle', $status ? L('_OPERATION_SUCCESS_') : L('_OPERATION_FAIL_'));
		//如果设置了关闭窗口，则提示完毕后自动关闭窗口
		if ($this->get('closeWin')) {
			$this->assign('jumpUrl', 'javascript:window.close();');
		}

		$this->assign('status', $status); // 状态
		//保证输出不受静态缓存影响
		C('HTML_CACHE_ON', false);
		if ($status) {
			//发送成功信息
			$this->assign('message', $message); // 提示信息
			// 成功操作后默认停留1秒
			if (!isset($this->waitSecond)) {
				$this->assign('waitSecond', '1');
			}

			// 默认操作成功自动返回操作前页面
			if (!isset($this->jumpUrl)) {
				$this->assign("jumpUrl", $_SERVER["HTTP_REFERER"]);
			}

			$this->display(C('TMPL_ACTION_SUCCESS'));
		} else {
			$this->assign('error', $message); // 提示信息
			//发生错误时候默认停留3秒
			if (!isset($this->waitSecond)) {
				$this->assign('waitSecond', '3');
			}

			// 默认发生错误的话自动返回上页
			if (!isset($this->jumpUrl)) {
				$this->assign('jumpUrl', "javascript:history.back(-1);");
			}

			$this->display(C('TMPL_ACTION_ERROR'));
			// 中止执行  避免出错后继续执行
			exit;
		}
	}
}
