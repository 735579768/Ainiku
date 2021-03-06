<?php
namespace Home\Controller;
use Think\Controller;

defined("ACCESS_ROOT") || die("Invalid access");
class GoodsController extends HomeController {
	public function index($gcate = '') {
		$info = get_category($gcate);
		if (empty($info)) {$this->error('没有此分类');}
		$tpl = empty($info['list_tpl']) ? 'index' : $info['list_tpl'];
		$this->assign('category', $info);
		$this->pages([
			'model' => 'Goods',
			'rows'  => 10,
		]);
		$this->display($tpl);
	}
	function detail($goods_id = '') {
		if (empty($goods_id)) {
			$this->error('产品不存在');
		}

		$map['status']   = 1;
		$map['goods_id'] = $goods_id;
		$info            = M('Goods')->where($map)->find();
		if (empty($info)) {
			$this->error('产品不存在');
		}

		$category = get_category($info['category_id']);
		$tpl      = empty($category['detail_tpl']) ? 'detail' : $category['detail_tpl'];

		M('Goods')->where("goods_id=$goods_id")->setInc('views');
		$this->assign('info', $info);
		$this->assign('category', $category);
		$this->display($tpl);
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