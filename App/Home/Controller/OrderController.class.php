<?php
namespace Home\Controller;
use Think\Controller;

defined("ACCESS_ROOT") || die("Invalid access");
class OrderController extends LoginController {
	/**
	 * 配置管理
	 * @author 枫叶 <735579768@qq.com>
	 */
	public function index() {
		$starttime = strtotime(I('starttime'));
		$endtime   = strtotime(I('endtime'));
		$field     = array(
			'start' => array(
				'field'   => 'starttime',
				'name'    => 'starttime',
				'type'    => 'datetime',
				'title'   => '开始时间',
				'note'    => '',
				'extra'   => null,
				'is_show' => 1,
				'value'   => $starttime,
			),
			'end'   => array(
				'field'   => 'endtime',
				'name'    => 'endtime',
				'type'    => 'datetime',
				'title'   => '结束时间',
				'note'    => '',
				'extra'   => null,
				'is_show' => 1,
				'value'   => $endtime,
			),
		);
		$this->assign('fieldarr', $field);
		$this->assign('data', null);

		$map = '__DB_PREFIX__order_pay.del_status>=1 ';
		if ($starttime != $endtime) {
			if (!empty($starttime) && !empty($endtime)) {
				$map .= ' and __DB_PREFIX__order_pay.create_time > ' . $starttime . ' and ' . '__DB_PREFIX__order_pay.create_time < ' . $endtime;
			} else if (!empty($starttime)) {
				$map .= ' and __DB_PREFIX__order_pay.create_time > ' . $starttime;
			} else if (!empty($endtime)) {
				$map .= ' and __DB_PREFIX__order_pay.create_time < ' . $endtime;
			}
		}

		$this->meta_title = '我的订单';
		$order            = I('ordernum');
		$username         = I('username');
//		$map=array();
		//		$uinfo=session('uinfo');
		//		$member_group_id=$uinfo['member_group_id'];
		$map .= ' and uid=' . UID;
		if (!empty($order)) {
			$map .= ' and ordernum_id=\'' . $order . '\'';
		}

		if (!empty($username)) {
			$map .= ' and a.username=\'' . $username . '\'';
		}

		$jon = "__DB_PREFIX__member as a on a.member_id=__DB_PREFIX__order_pay.uid";
		$fie = "*,__DB_PREFIX__order_pay.create_time as create_time,__DB_PREFIX__order_pay.status as status";
		if (!empty($order)) {$map['ordernum_id'] = $order;}
		$list = $this->pages(array(
			'field' => $fie,
			'join'  => $jon,
			'model' => 'OrderPay',
			'where' => $map,
			'order' => '__DB_PREFIX__order_pay.status asc, order_pay_id  desc',
		));

		$this->display();
	}
	//订单统计
	public function tongji() {
		$starttime = strtotime(I('starttime'));
		$endtime   = strtotime(I('endtime'));
		$status    = I('status', -1);
		$field     = array(
			'start' => array(
				'field'   => 'starttime',
				'name'    => 'starttime',
				'type'    => 'datetime',
				'title'   => '开始时间',
				'note'    => '',
				'extra'   => null,
				'is_show' => 1,
				'value'   => $starttime,
			),
			'end'   => array(
				'field'   => 'endtime',
				'name'    => 'endtime',
				'type'    => 'datetime',
				'title'   => '结束时间',
				'note'    => '',
				'extra'   => null,
				'is_show' => 1,
				'value'   => $endtime,
			),
		);
		$this->assign('fieldarr', $field);
		$this->assign('data', null);

		$map = '__DB_PREFIX__order.del_status>=1';
		if ($status != -1) {
			$map .= ' and __DB_PREFIX__order.status=' . $status;
		}

		if ($starttime != $endtime) {
			if (!empty($starttime) && !empty($endtime)) {
				$map .= ' and __DB_PREFIX__order.create_time > ' . $starttime . ' and ' . '__DB_PREFIX__order.create_time < ' . $endtime;
			} else if (!empty($starttime)) {
				$map .= ' and __DB_PREFIX__order.create_time > ' . $starttime;
			} else if (!empty($endtime)) {
				$map .= ' and __DB_PREFIX__order.create_time < ' . $endtime;
			}
		}

		$this->meta_title = '我的订单';
		$order            = trim(I('ordernum'));
		$username         = trim(I('username'));
		$title            = trim(I('title'));
		if (!IS_ADMIN && $this->member_group_id != 5) {$map .= 'uid=' . UID;}
		if (!empty($title)) {
			$map .= ' and  b.title like \'%' . $title . '%\'';
		}

		if (!empty($order)) {
			$map .= ' and ordernum_id=\'' . $order . '\'';
		}

		if (!empty($username)) {
			$map .= ' and a.username=\'' . $username . '\'';
		}

		$jon[] = "__DB_PREFIX__member as a on a.member_id=__DB_PREFIX__order.uid";
		$jon[] = "__DB_PREFIX__goods as b on b.goods_id=__DB_PREFIX__order.goods_id";
		$fie   = "*,__DB_PREFIX__order.create_time as create_time,__DB_PREFIX__order.status as status";
		if (!empty($order)) {$map['ordernum_id'] = $order;}
		$rows  = 10;
		$excel = I('export');
		if ($excel == 'true') {$rows = 1000;}
		$list = $this->pages(array(
			'field' => $fie,
			'join'  => $jon,
			'model' => 'Order',
			'where' => $map,
			'rows'  => $rows,
			'order' => '__DB_PREFIX__order.status asc, order_id  desc',
		));

		if ($excel == 'true') {

			foreach ($list as $key => $val) {
				$list[$key]['create_time'] = time_format($val['create_time']);
				$list[$key]['diqu']        = getRegion($val['diqu']);
				$list[$key]['xtotal']      = $val['xtotal'] . '元';
				$list[$key]['url']         = 'http://www.0yuanwang.com' . U('/Goods/' . $val['goods_id']);
				//$list[$key]['pic']='.'.getPicture($val['pic'],'thumbpath');
			}
			$field      = "'ordernum_id,diqu,username,title,num,xtotal,xobi,xshouxufei,wuliuname,'wuliuordernum,create_time,#link#url";
			$fieldtitle = "订单号,市场,用户名,产品名字,数量,小计,沃币,手续费,物流名字,物流单号,下单时间,地址";
			$str        = runPluginMethod('Phpexcel', 'export', array($list,
				'order-tongji',
				array($field, $fieldtitle)));
		}
		$this->display();
	}
	//更改单个产品状态为发货
	public function setfh2($id = null) {
		if (!empty($id) && (IS_ADMIN || $this->member_group_id != 2)) {
			//只有管理员才能修改状态
			$result = M('Order')->where("order_id=$id")->save(array('status' => 2));
			if (0 < $result) {
				$this->success('<a href="javascript:;" class="green">已发货</a>');
			} else {
				$this->error('修改失败');
			}
		} else {
			$this->error('error');
		}
	}

	//总订单更改状态为发货
	public function setfh($id = null) {
		if (!empty($id) && (IS_ADMIN || $this->member_group_id != 2)) {
			//只有管理员才能修改状态
			$result = M('OrderPay')->where("order_pay_id=$id")->save(array('status' => 3));
			if (0 < $result) {
				$this->success('<a href="javascript:;" class="green">交易完成</a>');
			} else {
				$this->error('修改失败');
			}
		} else {
			$this->error('error');
		}
	}
	public function shouhuo($id = null) {
		if (!empty($id) && (IS_ADMIN || $this->member_group_id == 2)) {
			//只有管理员才能修改状态
			$result = M('Order')->where("order_id=$id")->save(array('status' => 3));
			if (0 < $result) {
				$this->success('<a href="javascript:;" class="green">交易完成</a>');
			} else {
				$this->error('修改失败');
			}
		} else {
			$this->error('error');
		}
	}
	public function check($ordernum_id = null) {
		$this->meta_title = '订单详情';
		if (empty($ordernum_id)) {
			die();
		}

		$map                = array();
		$map['ordernum_id'] = $ordernum_id;
		if (!IS_ADMIN && $this->member_group_id != 4 && $this->member_group_id != 5) {$map['uid'] = UID;}
		$jon  = '__DB_PREFIX__goods as a on a.goods_id=__DB_PREFIX__Order.goods_id';
		$fie  = "*,__DB_PREFIX__order.create_time as create_time,__DB_PREFIX__order.status as status";
		$list = $this->pages(array(
			'rows'  => 100,
			'field' => $fie,
			'model' => 'Order',
			'join'  => $jon,
			'where' => $map,
		));
		$this->display();
	}
	function add() {
		if (IS_POST) {
			$this->meta_title = '订单提交失败';
			//查询购物车是不是有商品
			$jon  = __DB_PREFIX__ . 'goods as a   on  ' . __DB_PREFIX__ . 'cart.goods_id=a.goods_id';
			$info = M('Cart')->where('uid=' . UID)->join($jon)->select();
			if (empty($info)) {
				$this->error('请添加产品', U('Goods/index'));
			}
			$model     = D('Order');
			$ordernum  = createOrdernum();
			$total     = 0.00; //订单总额
			$shouxufei = 0.00; //订单总手续费
			$obi       = 0.00; //订单总沃币
			$points    = 0.00; //送的沃币
			//把购物车中的产品生成订单
			foreach ($info as $val) {
				$data                = array();
				$data['ordernum_id'] = $ordernum;
				$data['goods_id']    = $val['goods_id'];
				$data['uid']         = UID;
				$data['num']         = intval($val['num']);
				$data['price']       = doubleval($val['price']);

				$data['spoints'] = $val['spoints']; //此产品送的沃币
				$points += $val['spoints'];
				$data['create_time'] = NOW_TIME;
				$data['update_time'] = NOW_TIME;

				$goodsinfo = getGoods($val['goods_id']);
				$cpid      = getCategoryParent($goodsinfo['category_id']);
				if ($cpid == '83') {
					//a区
					$data['xtotal'] = 0;
					$data['xobi']   = doubleval($val['price']) * intval($val['num']);
					$obi += $data['xobi'];
				} else if ($cpid == '84') {
					//b区
					$data['xtotal'] = doubleval($val['price']) * intval($val['num']);
					$total += $data['xtotal'];
					//$data['spoints']=$val['spoints'];//此产品送的沃币
				} else if ($cpid == '109') {
					//c区
					$data['xtotal'] = 0;
					$data['xobi']   = doubleval($val['price']) * intval($val['num']);
					$obi += $data['xobi'];
					//手续费
					$shouxuf            = getGoods($val['goods_id'], 'shouxufei') / 100.00;
					$data['xshouxufei'] = $data['xobi'] * $shouxuf;
					$shouxufei += $data['xshouxufei'];
				}
				//$total+=$data['xtotal'];
				//$shouxufei+='';

				if ($model->create($data)) {
					$result = $model->add();
					if (0 < $result) {
						//减少库存
						M('Goods')->where("goods_id=" . $data['goods_id'])->setDec('stock', $data['num']);
						//$this->success('添加产品成功', U('index'));
					} else {
						$this->error('生成订单失败', U('Cart/index'));
					}
				} else {
					$this->error($model->getError());
				}
				//从购物车中删除
				M('Cart')->delete($val['cart_id']);
			}

			//把订单的所有产品价格生成一个支付信息保存到order_pay
			$data                = array();
			$data['qaddress']    = I('address');
			$data['create_time'] = NOW_TIME;
			$data['update_time'] = NOW_TIME;
			$data['total']       = $total;
			$data['points']      = $points;
			$data['obi']         = $obi;
			$data['shouxufei']   = $shouxufei;
			$data['uid']         = UID;
			$data['ordernum_id'] = $ordernum;
			$result              = M('OrderPay')->add($data);
			if (0 < $result) {
				$this->meta_title = '订单提交成功';
				F('__ORDERSUCCESS__', true);
				$this->success('下单成功', U('pay', array('order_pay_id' => $result)));
			} else {
				$this->error('生成订单失败', U('Cart/index'));
			}
		} else {
			$this->error('参数错误!');
		}

	}
	//订单生成成功后去支付页面
	public function pay($order_pay_id = null) {
		//	die(__ORDERSUCCESS__);
		if (F('__ORDERSUCCESS__') !== true) {$this->redirect('Order/index');}
		F('__ORDERSUCCESS__', null);
		$this->meta_title = '订单提交成功';
		if (empty($order_pay_id)) {die('error');}
		$map['uid']          = UID;
		$map['order_pay_id'] = $order_pay_id;
		$info                = M('OrderPay')->where($map)->find();
		if (empty($info)) {die('error');}
		$this->assign('info', $info);
		$this->sendordermail($info['ordernum_id']);
		$this->display('pay');
	}
	private function sendordermail($ordernum_id) {
		$this->meta_title = '订单详情';
		if (empty($ordernum_id)) {
			return '';
		}

		$map                = array();
		$map['ordernum_id'] = $ordernum_id;
		if (!IS_ADMIN) {$map['uid'] = UID;}
		$jon  = '__DB_PREFIX__goods as a on a.goods_id=__DB_PREFIX__Order.goods_id';
		$fie  = "*,__DB_PREFIX__order.create_time as create_time";
		$list = $this->pages(array(
			'field' => $fie,
			'model' => 'Order',
			'join'  => $jon,
			'where' => $map,
			'rows'  => 100,
		));
		$str = $this->fetch('ordermail');
		//发送邮件

		$conf = array(
			'host'      => C('MAIL_SMTP_HOST'),
			'port'      => C('MAIL_SMTP_PORT'),
			'username'  => C('MAIL_SMTP_USER'),
			'password'  => C('MAIL_SMTP_PASS'),

			'fromemail' => C('MAIL_SMTP_FROMEMAIL'),
			'to'        => C('MAIL_SMTP_TESTEMAIL'),
			'toname'    => C('MAIL_SMTP_TESTEMAIL'),
			'subject'   => C('MAIL_SMTP_EMAILSUBJECT'), //主题标题
			'fromname'  => C('MAIL_SMTP_FROMNAME'), //发件人
			'body'      => C('MAIL_SMTP_CE'), //邮件内容
		);

		$remail = C('ORDER_NOTICE');
		$remail = explode(',', $remail);
		foreach ($remail as $val) {
			$conf['to']       = $val;
			$conf['toname']   = $val;
			$conf['subject']  = '用户' . getMember(UID, 'username') . '提交啦订单';
			$conf['fromname'] = '九沃订货平台';
			$conf['body']     = $str . date('Y/m/d H:i:s'); //邮件内容l;
			$result           = sendMail($conf);
			if ($result !== true) {
				\Think\Log::write("发送邮件时出错:" . $result, 'WARN');
			}
		}
	}
//    function del($order_pay_id=null,$ordernum_id=null){
	//    	$order_pay_id=isset($_REQUEST['order_pay_id'])?I('get.order_pay_id'):I("id");//I('get.order_id');
	//		if(empty($order_pay_id))$this->error('请先进行选择');
	//    	$result=M('Order')->where("ordernum_id='".I('get.ordernum_id')."'")->delete();
	//		M('OrderPay')->delete($order_pay_id);
	//    	if($result){
	//    	  $this->success('已经删除订单',U('index'));
	//    	}else{
	//    	  $this->error('操作失败');
	//    	}
	//    }
	function del($order_pay_id = null, $ordernum_id = null) {
		$order_pay_id = isset($_REQUEST['order_pay_id']) ? I('get.order_pay_id') : I("id"); //I('get.order_id');
		if (empty($order_pay_id)) {
			$this->error('请先进行选择');
		}

		$result = M('Order')->where("ordernum_id='" . I('get.ordernum_id') . "'")->save(array('del_status' => -1));
		M('OrderPay')->where("order_pay_id=$order_pay_id")->save(array('del_status' => -1));
		if ($result) {
			$this->success('已经删除订单', U('index'));
		} else {
			$this->error('操作失败');
		}
	}
	//删除单个订单
	function delsingle($order_id = null) {
		$order_pay_id = isset($_REQUEST['order_id']) ? I('get.order_id') : I("id"); //I('get.order_id');
		if (empty($order_id)) {
			$this->error('请先进行选择');
		}

		$result = M('Order')->where("order_id='" . I('get.order_id') . "'")->delete();
		if ($result) {
			$this->success('已经删除订单');
		} else {
			$this->error('操作失败');
		}
	}
//	function delall(){
	//		$result=M('Order')->where("status=-1")->delete();
	//    	if(result){
	//    	  $this->success('回收站已经清空',U('recycle'));
	//    	}else{
	//    	  $this->error('操作失败');
	//    	}
	//		}
	//去支付订单
	function dopay($order_pay_id = null) {
		if (empty($order_pay_id)) {
			$this->error('非法访问');
		}

		$info = M('OrderPay')->find($order_pay_id);
		if (empty($info)) {
			$this->error('非法访问');
		}

		$total = 'yes';
		$obi   = 'yes';
		//如果有金额的话去网银支付
		if ($info['total'] > $this->uinfo['money']) {$total = 'no';}
		//如果有沃币的话查询账户上够不够
		if ($info['obi'] > $this->uinfo['points']) {$obi = 'no';}
		if ($info['status'] != '0') {
			$this->error('已经支付,请不要重复操作!');
		}

		if (IS_POST) {
			if ($total == 'no' || $obi == 'no') {
				$this->error('账户余额不足!');
			}

			//符合条件进行支付

			//减去相应的总金额
			$totalmon = $info['total'] + $info['shouxufei'];
			if ($totalmon > 0) {
				$result = M('Member')->where('member_id=' . UID)->setDec('money', $totalmon);
				if ($result > 0) {
					addActionLog($info['uid'], -$totalmon, $this->uinfo['money'] - $totalmon, '金额', '消费产品');
					//添加相应的沃币
					M('Member')->where('member_id=' . UID)->setInc('points', $totalmon);
					//记录沃币记录
					addActionLog($info['uid'], $info['points'], $this->uinfo['points'] + $info['points'], '沃币', '消费产品赠送');
				} else {
					\Think\Log::write("减去相应金额时发生错误:" . $result, 'WARN');
					$this->error('支付遇到问题请联系客服');
				}
			}

			//减去相应的手续费
			$totalsxf = $info['shouxufei'];
			if ($totalsxf > 0) {
				$result = M('Member')->where('member_id=' . UID)->setDec('money', $totalsxf);
				if ($result > 0) {
					addActionLog($info['uid'], -$totalsxf, $this->uinfo['money'] - $totalmon - $totalsxf, '金额', '产品(手续费)');
				} else {
					\Think\Log::write("减去相应手续费时发生错误:" . $result, 'WARN');
					$this->error('支付遇到问题请联系客服');
				}
			}
			//减去相应的沃币
			if ($info['obi'] > 0) {
				$result = M('Member')->where('member_id=' . UID)->setDec('points', $info['obi']);
				if ($result > 0) {
					addActionLog($info['uid'], -$info['obi'], $this->uinfo['points'] - $info['obi'], '沃币', '消费产品');
				} else {
					\Think\Log::write("减去相应沃币时发生错误:" . $result, 'WARN');
					$this->error('支付遇到问题请联系客服');
				}
			}
			//支付完成改变订单状态
			$result = M('OrderPay')->where("order_pay_id=$order_pay_id and uid=" . UID)->save(array('status' => 1));
			if ($result > 0) {} else {
				\Think\Log::write("更新order_pay_id=$order_pay_id 订单支付状态时发生错误:" . $result, 'WARN');
				$this->error("支付遇到问题请联系客服");
			}
			$result = M('Order')->where("ordernum_id='{$info['ordernum_id']}'  and uid=" . UID)->save(array('status' => 1));
			if ($result > 0) {} else {
				\Think\Log::write("更新单个订单支付状态时发生错误:" . $result, 'WARN');
				$this->error('支付遇到问题请联系客服');
			}

			$this->dopaymail($info);
			$this->success('支付成功', U('Order/index'));

		} else {

			$this->assign('total', $total);
			$this->assign('obi', $obi);
			$this->assign('info', $info);
			$this->display();
		}

	}
	private function dopaymail($info = null) {
		$conf = array(
			'host'      => C('MAIL_SMTP_HOST'),
			'port'      => C('MAIL_SMTP_PORT'),
			'username'  => C('MAIL_SMTP_USER'),
			'password'  => C('MAIL_SMTP_PASS'),

			'fromemail' => C('MAIL_SMTP_FROMEMAIL'),
			'to'        => C('MAIL_SMTP_TESTEMAIL'),
			'toname'    => C('MAIL_SMTP_TESTEMAIL'),
			'subject'   => C('MAIL_SMTP_EMAILSUBJECT'), //主题标题
			'fromname'  => C('MAIL_SMTP_FROMNAME'), //发件人
			'body'      => C('MAIL_SMTP_CE'), //邮件内容
		);

		$remail = C('ORDER_NOTICE');
		$remail = explode(',', $remail);
		foreach ($remail as $val) {
			$conf['to']       = $val;
			$conf['toname']   = $val;
			$conf['subject']  = '用户' . getMember($info['uid'], 'username') . '已经支付';
			$conf['fromname'] = '九沃订货平台';
			$conf['body']     = '单号' . $info['ordernum_id'] . '已经支付请尽快发货' . date('Y/m/d H:i:s'); //邮件内容l;
			$result           = sendMail($conf);
			if ($result !== true) {
				\Think\Log::write("发送邮件时出错:" . $result, 'WARN');
			}
		}

	}
}