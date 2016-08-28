<?php
// | Author: 枫叶 <735579768@qq.com> <http://www.zhaokeli.com>
// +----------------------------------------------------------------------
namespace Home\Controller;
// use Common\FileController;

defined("ACCESS_ROOT") || die("Invalid access");
/**
 * 文件控制器
 *
 */
class FileController extends HomeController {
	/**
	 * 主要用于文件上传和下载调用共用模块中的File类
	 * @param  [type] $method [description]
	 * @param  [type] $args   [description]
	 * @return [type]         [description]
	 */
	public function __call($method, $args) {
		$fileo = A('Common/File');
		if (method_exists($fileo, $method)) {
			return call_user_func(array($fileo, $method), $args);
		} else {
			E('没有此该当' . ':' . $method);
		}
	}

}
