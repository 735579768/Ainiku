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
	public function order() {
		$this->assign('member_title', '全部订单');
		$this->display();
	}
	/**
	 *待收货订单
	 **/
	public function shouhuoorder() {
		$this->assign('member_title', '待收货订单');
		$this->display();
	}
	/**
	 *待支付订单
	 **/
	public function zhifuorder() {
		$this->assign('member_title', '待支付订单');
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
}