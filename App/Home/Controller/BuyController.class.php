<?php
namespace Home\Controller;
use Think\Controller;

defined("ACCESS_ROOT") || die("Invalid access");
class BuyController extends LoginController {
	public function checkout() {
		//取地址列表
		$map['uid'] = UID;
		$list       = M('ConsigneeAddress')->where($map)->select();
		$this->assign('addresslist', $list);
		//取购物车中的商品
		$map['uid']      = UID;
		$map['selected'] = 1;
		$goodslist       = D('CartView')->where($map)->select();
		$this->assign('goodslist', $goodslist);
		//取商品详情
		$data               = M('Cart')->where($map)->field('sum(num) as num')->find();
		$info['totalnum']   = $data['num'];
		$data               = D('CartView')->where($map)->field('sum(num*price) as totalprice')->find();
		$info['totalprice'] = $data['totalprice'];
		$this->assign('info', $info);
		$this->display('checkout');
	}
	public function addaddress() {
		if (IS_POST) {
			$model = D('ConsigneeAddress');
			if ($model->create()) {
				$data['address_id'] = $model->consignee_address_id;
				$data['name']       = $model->consignee_name;
				$data['mobile']     = $model->consignee_mobile;
				$data['diqu']       = getRegion($model->consignee_diqu) . '<br>' . $model->consignee_detail;
				if (empty($model->consignee_address_id)) {
					//$model->create_time=NOW_TIME;
					//$model->update_time=NOW_TIME;
					$result = $model->add();
					if ($result > 0) {
						$data['address_id'] = $result;
						$this->ajaxreturn(array(
							status => 1,
							action => 'add',
							info   => '添加成功',
							data   => $data,
						));
					} else {
						$this->error('添加失败');
					}
				} else {
					//$model->update_time=NOW_TIME;
					$result = $model->save();
					if ($result > 0) {
						$this->ajaxreturn(array(
							status => 1,
							action => 'edit',
							info   => '修改成功',
							data   => $data,
						));
					} else {
						$this->error('没有更改');
					}
				}

			} else {
				$this->error($model->geterror());
			}
		} else {
			$this->success($this->fetch('addaddress'));
		}
	}
	public function editaddress() {
		$consignee_address_id = I('consignee_address_id');
		$action               = I('action');
		empty($consignee_address_id) && $this->error('非法请求');
		if ($action == 'edit') {
			$map['uid']                  = UID;
			$map['consignee_address_id'] = $consignee_address_id;
			$info                        = M('ConsigneeAddress')->where($map)->find();
			$this->assign('info', $info);
			$this->success($this->fetch('addaddress'));
		}
/* 	if(IS_POST){
$model=D('ConsigneeAddress');
if($model->create()){
//$model->create_time=NEW_TIME;
$model->update_time=NEW_TIME;
$result=$model->save();
if($result>0){
$this->success('修改成功');
}else{
$this->error('修改失败');
}
}else{
$this->error($model->geterror());
}
}*/

	}
	public function deladdress() {
		$consignee_address_id        = I('consignee_address_id');
		$map['consignee_address_id'] = array('in', "$consignee_address_id");
		$map['uid']                  = UID;
		$result                      = M('ConsigneeAddress')->where($map)->delete();
		if ($result > 0) {
			$this->success('删除成功');
		} else {
			$this->error('删除失败');
		}
	}
	//提交订单
	public function submitorder() {
		$consignee_address_id = I('consignee_address_id');
		$order_note           = I('order_note');
		//查询配送地址
		$map                         = array();
		$map['uid']                  = UID;
		$map['consignee_address_id'] = $consignee_address_id;
		$info                        = M('ConsigneeAddress')->where($map)->find();
		empty($info) && $this->error('配送地址错误!');

		if (IS_POST) {
			//查询购物车是不是有商品
			//$jon=__DB_PREFIX__.'goods as a   on  '.__DB_PREFIX__.'cart.goods_id=a.goods_id';
			$map             = array();
			$map['uid']      = UID;
			$map['selected'] = 1;
			$cartlist        = D('CartView')->where($map)->select();
			empty($cartlist) && $this->error('购物车是空的!', U('Cart/index'));
			$ordernum    = createOrderSn();
			$order_total = 0.00; //订单总额

			//把购物车中的产品生成订单保存到order_goods
			$datalist = array();
			foreach ($cartlist as $val) {
				$num   = intval($val['num']);
				$price = doubleval($val['price']);
				$order_total += $num * $price;
				$datalist[] = array(
					'goods_id'    => $val['goods_id'],
					'uid'         => UID,
					'num'         => $num,
					'price'       => $price,
					'total'       => $num * $price,
					'order_id'    => 0,
					'create_time' => NOW_TIME,
				);
				//从购物车中删除
				M('Cart')->delete($val['cart_id']);
			}

			//把订单的所有产品价格生成一个支付信息保存到order
			$data                     = array();
			$data['uid']              = UID;
			$data['order_sn']         = $ordernum;
			$data['create_time']      = NOW_TIME;
			$data['update_time']      = NOW_TIME;
			$data['order_total']      = $order_total;
			$data['consignee_name']   = $info['consignee_name'];
			$data['consignee_mobile'] = $info['consignee_mobile'];
			$data['consignee_diqu']   = getRegion($info['consignee_diqu']);
			$data['consignee_detail'] = $info['consignee_detail'];
			$data['youbian']          = $info['consignee_youbian'];
			$data['order_status']     = 1;
			//$data['consignee_email']=$info['consignee_email'];
			$data['order_note'] = $order_note;

			$result = M('Order')->add($data);
			if (0 < $result) {
				$res = 0;
				foreach ($datalist as $k => $v) {
					$datalist[$k]['order_id'] = $result;
					$re                       = M('OrderGoods')->add($datalist[$k]);
					($re > 0) || $res++;
				}

				//$re=M('OrderGoods')->addAll($dataList);
				//var_dump($datalist);
				//var_dump($re);
				if ($res > 0) {
					$this->error('下单失败');
				} else {
					//F('__ORDERSUCCESS__' . $result, 'true');
					$this->success('下单成功', U('Buy/pay', array('order_id' => $result)));
				}
			} else {
				$this->error('提交订单失败');
			}

		} else {
			$this->error('参数错误!');
		}
	}
	public function pay() {
		$order_id = I('order_id');
		$verify   = F('__ORDERSUCCESS__' . $order_id);
		//F('__ORDERSUCCESS__'.$order_id,null);
		//($verify!='true')&&redirect('/');
		$map['order_status'] = array('gt', 0);
		$map['order_id']     = $order_id;
		$info                = M('Order')->where($map)->find();
		empty($info) && $this->error('订单错误或已失效!');
		$this->assign('info', $info);
		$list = D('OrderGoodsView')->where("order_id=$order_id")->select();
		$this->assign('_list', $list);
		$this->display();
	}

	public function dopay() {
		$dopaylock = S('dopaylock');
		//empty($dopaylock) || $this->error('为避免重复支付,请等待1分钟后再尝试支付!');
		//S('dopaylock', true, 60);
		$order_id = I('order_id');
		empty($order_id) && $this->error('参数错误!');
		//查询订单
		$info = M('Order')->find($order_id);
		($info['order_status'] != 1) && $this->error('此订单已经支付,请不要重复支付!');
		$order_sn    = $info['order_sn'];
		$order_title = '产品订单支付:' . $info['order_sn'];
		$order_total = $info['order_total'];
		$info        = M('Member')->field('money')->find(UID);
		if ($info['money'] >= $order_total) {
			//扣款
			$result = M('Member')->where(array('member_id' => UID))->setDec('money', $order_total);
			if ($result) {
				$resu = M('Order')->where(array('order_id' => $order_id))->setField('order_status', 2);
				if ($resu) {
					$this->error('支付成功!', U('Buy/checkpay', array('order_id' => $order_id)));
				} else {
					$this->error('支付失败请联系客服!');
				}

			} else {
				$this->error('支付失败请联系客服!');
			}
		} else {
			$this->error('余额不足请充值!');
		}

	}
	//支付成功后前台跳转通知
	public function payok($order_id = '') {
		//支付宝通知
		$data1 = runPluginMethod('Alipay', 'return_url');
		//dump($data);
		//($data['status'] == 1) && exit();
		//财付通通知
		$data2 = runPluginMethod('Tenpay', 'return_url');
		//dump($data);
		//银联通知
		$data3 = runPluginMethod('Unionpay', 'return_url');
		//dump($data);
		//($data['status'] == 1) && exit();
		$chongzhi_sn = '';
		$money       = 0;
		if ($data1['status']) {
			$chongzhi_sn = $data1['order_sn'];
			$money       = $data1['money'];
		} else if ($data2['status']) {
			$chongzhi_sn = $data2['order_sn'];
			$money       = $data2['money'];
		} else if ($data3['status']) {
			$chongzhi_sn = $data3['order_sn'];
			$money       = $data3['money'];
		}
		if ($chongzhi_sn) {
			var_dump($_POST);
			echo "$chongzhi_sn $money success";
		} else {
			echo 'fail';
		}
		//测试后台通知
		$this->dopayok();
		exit();
	}
	//支付结果后台通知
	public function dopayok() {
		/*$data格式 array(
			'status'   => 1,
			'str'      => '验签成功',
			'pay_type' => '支付宝',
			'money'    => $money,
			'order_sn' => $order_sn,
			'extra'    => '',
		*/
		//支付宝通知
		$data1 = runPluginMethod('Alipay', 'notify_url');
		//($data1['status'] == 1) && exit();
		//财付通通知
		$data2 = runPluginMethod('Tenpay', 'notify_url');
		//($data2['status'] == 1) && exit();
		//银联通知
		$data3 = runPluginMethod('Unionpay', 'notify_url');
		//($data3['status'] == 1) && exit();

		$chongzhi_sn = '';
		$money       = 0;
		if ($data1['status']) {
			$chongzhi_sn = $data1['order_sn'];
			$money       = $data1['money'];
		} else if ($data2['status']) {
			$chongzhi_sn = $data2['order_sn'];
			$money       = $data2['money'];
		} else if ($data3['status']) {
			$chongzhi_sn = $data3['order_sn'];
			$money       = $data3['money'];
		}

		if ($data1['status'] || $data2['status'] || $data3['status']) {
			$result = M('Chongzhi')->where(array('chongzhi_sn' => $chongzhi_sn))->setField('status', 2);
			if ($result) {
				$resu = M('Member')->where('member_id=' . UID)->setInc('money', $money);
				if (!$resu) {
					//记录失败日志
				}
			}

		}
		exit();
	}
	//前台查询支付状态url
	public function checkpay($order_id) {
		$order_id = I('order_id');
		$info     = M('Order')->field('order_status')->where("order_id=$order_id")->find();
		if ($info['order_status'] == 2) {
			$this->success('支付成功', U('Buy/payok', array('order_id' => $order_id)));
		} else {
			$this->error('未支付');
		}
	}
	/**
	 * 取余额
	 */
	function getyue() {
		$info = M('Member')->field('money')->find(UID);
		if (empty($info)) {
			$this->error(0);
		} else {
			$this->success($info['money']);
		}
	}
	/**
	 * 在线充值
	 * 调用支付接口完成支付
	 */
	function chongzhi() {
		$rearr = array(
			'status' => 1,
			'info'   => $this->fetch(),
			'data'   => '',
		);
		$online_pay  = strtolower(I('online_pay')); //支付类型
		$order_total = floatval(I('money')); //支付金额
		$order_sn    = createorder();
		$order_title = '在线充值';

		$pay_title = array(
			'unionpay'     => '中国银联',
			'alipay_jishi' => '支付宝',
			'tenpay'       => '财付通',
		);
		$result = M('Chongzhi')->add(array(
			'chongzhi_type' => $pay_title[$online_pay],
			'money'         => $order_total,
			'uid'           => UID,
			'chongzhi_sn'   => $order_sn,
			'create_time'   => NOW_TIME,
			'status'        => 1,
		));
		if ($result) {
			M('Chongzhi')->where(array('chongzhi_id' => $result))->setField('chongzhi_sn', $order_sn . $result);
		} else {
			$rearr['data']   = '支付接口调用失败';
			$rearr['status'] = 0;
			$this->ajaxreturn($rearr);
		}
		$order_sn = $order_sn . $result;
		$data     = '';
		if (strpos($online_pay, 'payOnlineBank_') !== false) {
			//支付宝网银
		} else if (strpos($online_pay, 'alipay_') !== false) {

			$rearr['data'] = runPluginMethod('Alipay', 'dopay', array($order_total, $order_sn, $order_title, $online_pay));
		} else {
			//其它支付平台
			switch ($online_pay) {

			case 'unionpay':
				$rearr['data'] = runPluginMethod('Unionpay', 'dopay', array($order_total, $order_sn, $order_title));
				break;
			case 'tenpay':
				$rearr['data'] = runPluginMethod('Tenpay', 'dopay', array($order_total, $order_sn, $order_title));
				break;
			default:
				$rearr['data']   = '支付接口调用失败';
				$rearr['status'] = 0;
				break;
			}
		}
		//echo (strpos('alipay_', $online_pay));
		$this->ajaxreturn($rearr);
	}

}
