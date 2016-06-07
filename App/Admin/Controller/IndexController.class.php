<?php
//Author: 枫叶 <735579768@qq.com> <http://www.zhaokeli.com>
namespace Admin\Controller;
class IndexController extends AdminController {
	public function index() {
		//$this->display();
		redirect(U($this->uinfo['admin_index'], array('mainmenu' => 'true')));
	}
}