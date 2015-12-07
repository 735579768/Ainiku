<?php
namespace Home\Controller;
use Think\Controller;

class EmptyController extends HomeController {
	protected function _empty() {
		//前台统一的404页面
		header('HTTP/1.1 404 Not Found');
		header("status: 404 Not Found");
		$this->display('Public:404');
		die();
	}
}