<?php
//Author: 枫叶 <735579768@qq.com> <http://www.zhaokeli.com>
namespace Admin\Controller;
class IndexController extends AdminController {
	public function index() {
		if (MAIN_IFRAME != 'true') {
			redirect(U($this->uinfo['admin_index']));
		} else {
			$this->display();
		}
	}
}