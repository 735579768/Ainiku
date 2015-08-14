<?php
// | Author: 枫叶 <735579768@qq.com> <http://www.zhaokeli.com>
// +----------------------------------------------------------------------

namespace Home\Controller;

/**
 * 后台用户控制器
 * @author 枫叶 <735579768@qq.com>
 */
class AlipayController extends HomeController {
	function pay(){
		if(!IS_POST){
				$money=intval(I('money'));
				if(empty($money)||!is_numeric($money) || $money<0){
						$this->error('输入的金额不合法请重新输入');
						}
					$alipayconf=array(
						//必填
						'order'=>'111111111111',
						'ordername'=>'订单名称',
						'money'=>$money,
						//必填
						
						//选填
						'goodsurl'=>$goodsurl,
						'receivename'=>$receivename,
						'receiveadr'=>$receiveadr,
						'receivezip'=>$receivezip,//邮编
						'receivephone'=>$receivephone,
						'receivemobile'=>$receivemobile
						//选填
						
					);
//				//把订单添加到数据库
//				$model=D('order');
//				$data['ordernum']=date('YmdHis').rand(10000,99999);
//				$data['money']=$money;
//				if($model->create($data)){
//					$model->add();
//				}else{
//				$this->error($model->geterror());	
//					}
//			//把订单添加到数据库
			
			$alipay=A('Pay');
			$alipay->dopay($alipayconf);
		}else{
				die('参数不对');
			}
		}
	public function suc($ordernum="2014100422482039314",$tradestatus='no'){
		if(empty($ordernum))$this->error('非法请求');
		$info=D('order')->where("ordernum='$ordernum' and chongzhi=0")->find();
		if(empty($info))$this->error('非法请求');
		$status=array(
			'no'=>'未知状态',
			'WAIT_BUYER_PAY'=>'等待买家付款',
			'WAIT_SELLER_SEND_GOODS'=>'等待卖家发货',
			'WAIT_BUYER_CONFIRM_GOODS'=>'卖家已发货，等待买家确认收货',
			'TRADE_FINISHED'=>'充值成功',
			'TRADE_SUCCESS'=>'充值成功'
		);
		if($info['chongzhi']=='0' and ($info['status']=='TRADE_SUCCESS' or $info['status']=='TRADE_FINISHED')){
			//添加在线充值日志
			addlog(UID,'在线充值',$info['money']);

			//会员添加钱数
			$result=M('member')->where("id=".UID)->setInc('points',$info['money']);
			if(0<$result){
			//当前订单状态完成
			D('order')->where("ordernum='$ordernum'")->setField('chongzhi',1);				
				}
			}
		$this->assign('info',$info);
		$this->assign('status',$status[$info['status']]);
		$this->display();
		}
}
