<?php
// | Author: 枫叶 <735579768@qq.com> <http://www.zhaokeli.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;
if(!defined("ACCESS_ROOT"))die("Invalid access");
/**
 * 后台用户控制器
 * @author 枫叶 <735579768@qq.com>
 */
class LinkController extends AdminController {
 public function index(){
	 	$title=I('title');
		$map['title']=array('like','%'.$title.'%');
		$map['status']=array('egt',0);
    	$this->pages(array(
					'model'=>'Link',
					'where'=>$map,
					'order'=>'sort asc,status asc,link_id desc'
					));
	$this->meta_title='友情链接';
	 $this->display();
	 }
 public function add(){
	 if(IS_POST){
		 F('sys_link_tree',null);
		 $model=D('Link');
		 if($model->create()){
			 $result=$model->add();
			 	 if($result!==false){
				 	$this->success(L('_ADD_SUCCESS_'),U('Link/index'));
				 }else{
					$this->error(L('_UNKNOWN_ERROR_'));	 
					 }
			 }else{
			$this->error($model->geterror());	 
				 }
	  }else{
		//$field=Api('Model/linkModel');
		$field=getModelAttr('link');
		$this->meta_title = '添加友情链接';
		$this->assign('fieldarr',$field);
		$this->display('edit');	 
			 }
	
	 }
 public function edit($link_id=null){
	 $model=D('Link');
	 if(IS_POST){
		  F('sys_link_tree',null);
		 if($model->create()){
			 $result=$model->save();
			 if($result!==false){
				 	$this->success(L('_UPDATE_SUCCESS_'),U('Link/index'));
				 }else{
					$this->error(L('_UNKNOWN_ERROR_'));	 
					 }
			 
			 }else{
			$this->error($model->geterror());	 
				 }		 
		 }else{
		if(empty($link_id))$this->error(L('_ID_NOT_NULL_'));
		$data=D('Link')->where("link_id=$link_id")->find();
		$field=getModelAttr('link');
		$this->meta_title = '编辑友情链接';
		$this->assign('fieldarr',$field);
		$this->assign('data',$data); 
		$this->display();	 
			 }
	 
	 }
public function del($link_id){
	$result=D('Link')->where("link_id=$link_id")->delete();
	 if($result!==false){
		  F('sys_link_tree',null);
		 $this->success(L('_DELETE_SUCCESS_'),U('Link/index'));
	 }else{
		$this->error(L('_UNKNOWN_ERROR_'));	 
		 }

	}
}
