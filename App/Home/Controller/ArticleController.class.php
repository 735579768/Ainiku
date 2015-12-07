<?php
namespace Home\Controller;
use Think\Controller;

defined("ACCESS_ROOT") || die("Invalid access");
class ArticleController extends HomeController {
	public function index($cate = null) {
		$info = getCategory($cate);
		if (empty($info)) {$this->_empty();}

		$tpl = empty($info['list_tpl']) ? 'index' : $info['list_tpl'];
		$this->assign('category', $info);
		$this->display($tpl);
	}
	function detail($article_id = null) {
		if (empty($article_id)) {
			$this->_empty();
		}

		$map['status'] = 1;
		$info          = M('Article')->where($map)->find($article_id);
		if (empty($info)) {$this->_empty();}

		$category = getCategory($info['category_id']);
		$tpl      = empty($info['detail_tpl']) ? 'detail' : $info['detail_tpl'];

		M('Article')->where("article_id=$article_id")->save(array('views' => $info['views'] + 1));
		$this->assign('arcinfo', $info);
		$this->assign('info', $info);
		$this->assign('category', $category);
		$this->display($tpl);
	}
	function sendmail() {
		$result = sendMail(array(
			'to'       => '735579768@qq.com',
			'subject'  => '邮件主题',
			'fromname' => '我是赵克立你好哦',

		));
		if ($result === true) {
			echo '发送成功';
		} else {
			echo $result;
		}
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