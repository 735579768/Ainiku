<?php
namespace Home\Controller;
use Think\Controller;
defined("ACCESS_ROOT") || die("Invalid access");
class GoodsController extends HomeController {
    public function index(){
//$Ip = new \Org\Net\IpLocation(); // 实例化类
//$location = $Ip->getlocation('127.0.0.1'); // 
  $this->display();
    }
	function detail($goods_id){
		if(empty($goods_id))$this->_empty();
		$info=getGoods($goods_id,'status=1');
		if(empty($info))$this->_empty();
		$this->assign('info',$info);
		$this->display();
		}
	function sendmail(){
		$result=sendMail(array(
			'to'=>'735579768@qq.com',
			'subject'=>'邮件主题',
			'fromname'=>'我是赵克立你好哦'
			
		));
		if($result===true){
			echo '发送成功';
			}else{
			echo $result;
				}		
		}
	function pay(){
		$alipayconf=array(
			//必填
			'order'=>'111111111111',
			'ordername'=>'订单名称',
			'money'=>100,
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
		$alipay=A('Pay');
		$alipay->dopay($alipayconf);
		
		}
}