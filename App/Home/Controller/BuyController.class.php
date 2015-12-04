<?php
namespace Home\Controller;
use Think\Controller;
if(!defined("ACCESS_ROOT"))die("Invalid access");
class BuyController extends LoginController {
 public function checkout(){
 	//取地址列表
 	//$map['uid']=UID;
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
 	 	//$map['uid']=UID;
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
	//$map['uid']=UID;
 	$result=M('ConsigneeAddress')->where($map)->delete();
 	if($result>0){
 		$this->success('删除成功');
 	}else{
 		$this->error('删除失败');
 	}
 }
 public function pay(){
 	$this->display();
 }
}
