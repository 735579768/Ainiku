<?php
namespace Admin\Controller;
defined("ACCESS_ROOT") || die("Invalid access");
class OrderController extends AdminController {
	public function index() {
		$this->assign('meta_title', '订单列表');
		$order_sn     = I('order_sn');
		$order_status = I('order_status');

		($order_status === '') && $order_status = 6;

		$field = array(
			'field'   => 'order_status',
			'name'    => 'order_status',
			'type'    => 'select',
			'title'   => '订单状态',
			'note'    => '',
			'extra'   => array(
				6 => '全部订单',
				0 => '失效订单',
				1 => '等待付款',
				2 => '已经支付',
				3 => '已经发货',
				4 => '已经收货',
				5 => '交易完成',
			),
			'is_show' => 3,
			'value'   => $order_status,
		);
		$this->assign('orderstatus', $field);

		$map = array();
		if ($order_status !== '') {
			if ($order_status == '6') {
				$map['order_status'] = array('neq', '0');
			} else {
				$map['order_status'] = $order_status;
			}
		}

		empty($order_sn) || ($map['order_sn'] = $order_sn);
		$list = $this->pages(array(
			'where' => $map,
			'model' => 'OrderView',
			'order' => 'order_status asc,order_id desc',
		));
		$this->display();
	}
	public function check($order_id = '') {
		$this->assign('meta_title', '查看订单');
		$info = M('Order')->find($order_id);
		//查询订单中的商品信息
		$map['order_id'] = $order_id;
		$list            = D('OrderGoodsView')->where($map)->select();
		$this->assign('info', $info);
		$this->assign('_list', $list);
		$this->display();
	}
	public function del($order_id = '') {
		empty($order_id) && ($order_id = I('id'));
		$map['order_id'] = array('in', "$order_id");
		M('OrderGoods')->where($map)->delete();
		M('Order')->where($map)->delete();
		$this->success(L('_DELETE_SUCCESS_'));
	}
	public function updateorder() {
		$model    = M('Order');
		$order_id = I('order_id');
		$status   = I('order_status');
		if ($model->create()) {
			switch ($status) {
			case '2':
				$model->pay_time = NOW_TIME;
				break;
			case '3':
				$model->fahuo_time = NOW_TIME;
				break;
			case '4':
				$model->shouhuo_time = NOW_TIME;
				break;

			default:
				# code...
				break;
			}
			$result = $model->save();
			M('OrderGoods')->where("order_id=$order_id")->setField('status', $status);
			($result > 0) ? $this->success('设置成功') : $this->error('没有更改');
		} else {
			$this->error($model->geterror());
		}
	}
}