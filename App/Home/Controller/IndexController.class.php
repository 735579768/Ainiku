<?php
namespace Home\Controller;
use Think\Controller;

defined("ACCESS_ROOT") || die("Invalid access");
class IndexController extends HomeController {
	public function index() {
		$this->display();
	}
	function send_mail() {
		$result = send_mail(array(
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