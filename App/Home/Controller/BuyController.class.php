<?php
namespace Home\Controller;
use Think\Controller;
if(!defined("ACCESS_ROOT"))die("Invalid access");
class BuyController extends LoginController {
 public function checkout(){
 	//取地址列表
 	$map['uid']=UID;
 	$list=M('ConsigneeAddress')->where($map)->select();
 	$this->assign('addresslist',$list);
 	$this->display('checkout');
 }
 public function addaddress(){
 	if(IS_POST){
 		$model=D('ConsigneeAddress');
 		if($model->create()){
 			$data['address_id']=$model->consignee_address_id;
 			$data['name']=$model->consignee_name;
 			$data['mobile']=$model->consignee_mobile;
 			$data['diqu']=getRegion($model->consignee_diqu).'<br>'.$model->consignee_detail;
 			if(empty($model->consignee_address_id)){
	 			//$model->create_time=NOW_TIME;
	 			//$model->update_time=NOW_TIME;
	 			$result=$model->add();
	 			if($result>0){
	 				$data['address_id']=$result;
	 				$this->ajaxreturn(array(
	 							status=>1,
	 							action=>'add',
	 							info=>'添加成功',
	 							data=>$data,
	 							));
	 			}else{
	 				$this->error('添加失败');
	 			}
 			}else{
	 			//$model->update_time=NOW_TIME;
	 			$result=$model->save();
	 			if($result>0){
	 				$this->ajaxreturn(array(
	 							status=>1,
	 							action=>'edit',
	 							info=>'修改成功',
	 							data=>$data,
	 							));
	 			}else{
	 				$this->error('没有更改');
	 			} 				
 			}

 		}else{
 			$this->error($model->geterror());
 		}
 	}else{
 		$this->success($this->fetch('addaddress'));
 	}
 }
 public function editaddress(){
 	$consignee_address_id=I('consignee_address_id');
 	$action=I('action');
 	empty($consignee_address_id)&&$this->error('非法请求');
 	if($action=='edit'){
 	 	$map['uid']=UID;
	 	$map['consignee_address_id']=$consignee_address_id;
	 	$info=M('ConsigneeAddress')->where($map)->find();
	 	$this->assign('info',$info);
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
 public  function deladdress(){
	$consignee_address_id=I('consignee_address_id');
	$map['consignee_address_id']=array('in',"$consignee_address_id");
	$map['uid']=UID;
 	$result=M('ConsigneeAddress')->where($map)->delete();
 	if($result>0){
 		$this->success('删除成功');
 	}else{
 		$this->error('删除失败');
 	}
 }
 //提交订单
 public function submitorder(){
 	$consignee_address_id=I('consignee_address_id');
 	//查询配送地址
 	$map=array();
 	$map['uid']=UID;
 	$map['consignee_address_id']=$consignee_address_id;
 	$info=M('ConsigneeAddress')->where($map)->find();
 	empty($info)&&$this->error('配送地址错误!');

	if(IS_POST){
		//查询购物车是不是有商品
		//$jon=__DB_PREFIX__.'goods as a   on  '.__DB_PREFIX__.'cart.goods_id=a.goods_id';
		$map=array();
		$map['uid']=UID;
		$map['selected']=1;
		$cartlist=D('CartView')->where($map)->select();
		empty($cartlist)&&$this->error('购物车是空的!',U('Cart/index'));
			$ordernum=createOrderSn();
			$order_total=0.00;//订单总额

			//把购物车中的产品生成订单保存到order_goods
			$datalist=array();
			foreach($cartlist as $val){
				$num=intval($val['num']);
				$price=doubleval($val['price']);
				$order_total+=$num*$price;
				$datalist[]=array(
						'goods_id'=>$val['goods_id'],
						'uid'=>UID,
						'num'=>$num,
						'price'=>$price,
						'order_id'=>0,
						'create_time'=>NOW_TIME
					);
				//从购物车中删除
				M('Cart')->delete($val['cart_id']);
				}

			//把订单的所有产品价格生成一个支付信息保存到order
			$data=array();
			$data['uid']=UID;
			$data['order_sn']=$ordernum;
			$data['create_time']=NOW_TIME;
			$data['update_time']=NOW_TIME;	
			$data['order_total']=$order_total;
			$data['consignee_name']=$info['consignee_name'];
			$data['consignee_mobile']=$info['consignee_mobile'];
			$data['consignee_city']=$info['consignee_mobile'];
			$data['consignee_detail']=$info['consignee_detail'];
			$data['youbian']=$info['consignee_youbian'];
			$data['order_status']=1;
			//$data['consignee_email']=$info['consignee_email'];
			$data['order_note']='';

			$result=M('Order')->add($data);	
			if(0<$result){
				$res=0;
				foreach ($datalist as $k=>$v) {
					$datalist[$k]['order_id']=$result;
					$re=M('OrderGoods')->add($datalist[$k]);
					($re>0)||$res++;
				}

				//$re=M('OrderGoods')->addAll($dataList);
				//var_dump($datalist);
				//var_dump($re);
				if($res>0){
					$this->error('下单失败');
				}else{
					F('__ORDERSUCCESS__'.$result,'true');
					$this->success('下单成功',U('Buy/pay',array('order_id'=>$result)));
				}
			}else{
				$this->error('提交订单失败');
			}	
				

	}else{
		$this->error('参数错误!');
		}
 }
 public function pay(){
 	$order_id=I('order_id');
 	$verify=F('__ORDERSUCCESS__'.$order_id);
 	//F('__ORDERSUCCESS__'.$order_id,null);
 	//($verify!='true')&&redirect('/');
 	$info=M('Order')->find($order_id);
 	$this->assign('info',$info);
 	$this->display();
 }
}
