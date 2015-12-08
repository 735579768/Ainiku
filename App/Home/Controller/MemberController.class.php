<?php
//Author: 枫叶 <735579768@qq.com> <http://www.zhaokeli.com>
namespace Home\Controller;
use Think\Controller;

defined("ACCESS_ROOT") || die("Invalid access");
class MemberController extends LoginController {
	/**
	 *会员中心
	 **/
	function portal() {
		$this->assign('member_title', '会员中心');
		$this->display();
	}
	/**
	 *我的全部订单
	 **/
	public function order($order_status = 1) {
		$this->assign('member_title', '全部订单');
		$map['order_status'] = $order_status;
		$map['uid']          = UID;
		$this->pages(array(
			'model' => 'Order',
			'where' => $map,
			'order' => 'order_id desc',
			'rows'  => 5,
		));
		$this->display();
	}
	/**
	 *查看订单
	 **/
	public function checkorder($order_id = '') {
		empty($order_id) && $this->error('参数错误!');
		$this->assign('member_title', '订单详情');
		$map['uid']      = UID;
		$map['order_id'] = $order_id;
		$info            = M('Order')->where($map)->find();
		//查询订单中的商品信息
		$list = D('OrderGoodsView')->where($map)->select();
		$this->assign('info', $info);
		$this->assign('_list', $list);
		$this->display();
	}
	/**
	 *修改密码
	 **/
	public function modpwd($new_password = '', $re_password = '') {
		$this->assign('member_title', '修改密码');
		if (IS_POST) {
			if (empty($new_password) || empty($re_password)) {
				$this->error('密码不能为空');
			}
			if ($new_password !== $re_password) {
				$this->error('两次输入的密码不一样！');
			}
			if (!preg_match('/[\w]{6,15}/', $new_password)) {
				$this->error('密码格式不正确！');
			}
			$result = M('Member')->where("member_id=" . UID)->save(array(
				'password'    => $new_password,
				'update_time' => NOW_TIME,
			));

			$new_password = ainiku_ucenter_md5($new_password);
			($result > 0) ? $this->success('修改成功') : $this->error('修改失败');
		} else {
			$this->display();
		}

	}
	/**
	 *收货地址
	 **/
	public function address() {
		$this->assign('member_title', '收货地址');
		$map['uid'] = UID;
		$list       = M('ConsigneeAddress')->where($map)->select();
		$this->assign('_list', $list);
		$this->display('consigneeaddress');
	}
}