<?php
namespace Home\Controller;
use Think\Controller;

defined("ACCESS_ROOT") || die("Invalid access");
class SingleController extends HomeController {
	public function index($single_id = null) {
		$info = getSingle($single_id);
		if (empty($info)) {$this->_empty();}
		$tpl = empty($info['index_tpl']) ? 'index' : $info['index_tpl'];
		$this->assign('single', $info);
		$this->display($tpl);
	}
}