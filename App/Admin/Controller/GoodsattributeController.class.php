<?php
// | Author: 枫叶 <735579768@qq.com> <http://www.zhaokeli.com>
// +----------------------------------------------------------------------
namespace Admin\Controller;
use \Admin\Controller\AdminController;

defined("ACCESS_ROOT") || die("Invalid access");
class GoodsattributeController extends AdminController {
	public function index() {
		$this->display();
	}
	public function add() {
		$this->display('edit');
	}
	public function edit() {
		$this->display();
	}
	public function del() {
		$this->display();
	}

}
