<?php
namespace Home\Controller;
use Think\Controller;

defined("ACCESS_ROOT") || die("Invalid access");
class ArticleController extends HomeController {
	public function index($acate = null) {
		$info = get_category($acate);
		if (empty($info)) {$this->_empty();}

		$tpl = empty($info['list_tpl']) ? 'index' : $info['list_tpl'];
		$this->assign('category', $info);
		$this->display($tpl);
	}
	function detail($article_id = null) {
		if (empty($article_id)) {
			$this->_empty();
		}

		$map['status']     = 1;
		$map['article_id'] = $article_id;
		$info              = M('Article')->where($map)->find();
		if (empty($info)) {
			$this->_empty();
		}

		$category = get_category($info['category_id']);
		$tpl      = empty($category['detail_tpl']) ? 'detail' : $category['detail_tpl'];

		M('Article')->where("article_id=$article_id")->setInc('views');
		$this->assign('arcinfo', $info);
		$this->assign('category', $category);
		$this->display($tpl);
	}
	function pay() {
		$alipayconf = array(
			//必填
			'order'         => '111111111111',
			'ordername'     => '订单名称',
			'money'         => 100,
			//必填

			//选填
			'goodsurl'      => $goodsurl,
			'receivename'   => $receivename,
			'receiveadr'    => $receiveadr,
			'receivezip'    => $receivezip, //邮编
			'receivephone'  => $receivephone,
			'receivemobile' => $receivemobile,
			//选填

		);
		$alipay = A('Pay');
		$alipay->dopay($alipayconf);

	}
}