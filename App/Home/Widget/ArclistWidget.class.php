<?php
namespace Home\Widget;
use Think\Controller;

class ArclistWidget extends Controller {
	function upimglist($category_id = 80, $rows = 10) {
		if (empty($category_id)) {
			return '';
		}

		$map['category_id'] = $category_id;
		$map['_string']     = 'find_in_set(\'3\',position)';
		$list               = S(json_encode($map) . $rows);
		if (empty($list) || APP_DEBUG) {
			$list = M('Article')->where($map)->limit($rows)->select();
			$this->assign('list', $list);
			$list = $this->fetch('Widget:upimglist');
			S(json_encode($map) . $rows, $list);
		}
		echo $list;
	}
	function textlist($category_id = '', $rows = 10) {
		if (empty($category_id)) {
			return '';
		}

		$map['category_id'] = $category_id;
		$map['_string']     = 'find_in_set(\'3\',position)';

		$list = S(json_encode($map) . $rows);
		if (empty($list) || APP_DEBUG) {
			$list = M('Article')->where($map)->limit($rows)->select();
			$this->assign('info', getCategory($category_id));
			$this->assign('list', $list);
			$list = $this->fetch('Widget:textlist');
			S(json_encode($map) . $rows, $list);
		}
		echo $list;
	}

	function rtextlist($category_id = '', $rows = 10) {
		if (empty($category_id)) {
			return '';
		}

		$map['category_id'] = $category_id;
		$map['_string']     = 'find_in_set(\'3\',position)';

		$list = S(json_encode($map) . $rows);
		if (empty($list) || APP_DEBUG) {
			$list = M('Article')->where($map)->limit($rows)->select();
			$this->assign('info', getCategory($category_id));
			$this->assign('list', $list);
			$list = $this->fetch('Widget:rtextlist');
			S(json_encode($map) . $rows, $list);
		}
		echo $list;
	}
}