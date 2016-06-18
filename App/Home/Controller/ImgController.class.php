<?php
//Author: 枫叶 <735579768@qq.com> <http://www.zhaokeli.com>
namespace Home\Controller;
use Think\Controller;

defined("ACCESS_ROOT") || die("Invalid access");
class ImgController extends HomeController {
	//根据参数生成图片大小
	//参数 id_width_height   例如:250_100_100
	public function cr($id = null) {
		empty($id) && $this->_empty();
		$imgsize = explode('_', $id);
		if (count($imgsize) !== 3) {
			$this->_empty();
		}

		$id     = intval($imgsize[0]);
		$width  = intval($imgsize[1]);
		$height = intval($imgsize[2]);

		if (!is_numeric($id) || !is_numeric($width) || !is_numeric($height)) {
			$this->_empty();
		}

		$info = M('Picture')->find($id);
		if (empty($info)) {
			$this->_empty();
		}

		$imgpath = '.' . $info['path'];
		//img2thumb($imgpath, '', $width, $height, $createimg = false, $proportion = true);

	}
	protected function _empty() {
		//前台统一的404页面
		header('HTTP/1.1 404 Not Found');
		header("status: 404 Not Found");
		die();
	}
}