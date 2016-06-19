<?php
namespace Home\Controller;
use Think\Controller;

defined("ACCESS_ROOT") || die("Invalid access");
class SearController extends HomeController {
	public function index() {
		$position = I('position');
		$keywords = to_utf8(I('keywords'));
		if (!empty($position)) {
			$map['_string'] = "find_in_set('$position',position)";
		}

		$map['title'] = array('like', '%' . $keywords . '%');
		$list         = $this->pages(array(
			'model' => 'Article',
			'where' => $map,
			'rows'  => 6,
			'order' => 'create_time desc,article_id desc',
		));
		$this->display();
	}
}