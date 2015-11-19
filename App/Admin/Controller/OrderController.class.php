<?php
namespace Admin\Controller;
if(!defined("ACCESS_ROOT"))die("Invalid access");
class OrderController extends AdminController {
	public function index(){
	$this->assign('meta_title','订单列表');
	$order_sn=I('order_sn');
	$map=array();
	empty($order_sn)||($map['order_sn']=$order_sn);
	$list=$this->pages(array(
			'where'=>$map,
			'model'=>'OrderView',
			'order'=>'order_id desc'
	));
	$this->display();
	}
	public function check($order_id=''){
		$this->assign('meta_title','查看订单');
		$info=M('Order')->find($order_id);
		//查询订单中的商品信息
		$map['order_id']=$order_id;
		$list=D('OrderGoodsView')->where($map)->select();
		$this->assign('info',$info);
		$this->assign('_list',$list);
		$this->display();
		}
	public function del(){
		$this->display();
		}
	public function updateorder(){
		$model=M('Order');
		if($model->create()){
			$result=$model->save();
			($result>0)?$this->success('设置成功'):$this->error('没有更改');
			}else{
			$this->error($model->geterror());	
				}
		}
}