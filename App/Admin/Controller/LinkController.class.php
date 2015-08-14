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
		 F(md5('sys_link_tree'),null);
		 $model=D('Link');
		 if($model->create()){
			 $result=$model->add();
			 	 if($result!==false){
				 	$this->success('添加成功',U('Link/index'));
				 }else{
					$this->error('添加失败,未知错误');	 
					 }
			 }else{
			$this->error($model->geterror());	 
				 }
	  }else{
		//$field=Api('Model/linkModel');
		$field=getModel('link');
		$this->meta_title = '添加友情链接';
		$this->assign('fieldarr',$field);
		$this->display('edit');	 
			 }
	
	 }
 public function edit($link_id=null){
	 $model=D('Link');
	 if(IS_POST){
		  F(md5('sys_link_tree'),null);
		 if($model->create()){
			 $result=$model->save();
			 if($result!==false){
				 	$this->success('更新成功',U('Link/index'));
				 }else{
					$this->error('更新失败，未知错误');	 
					 }
			 
			 }else{
			$this->error($model->geterror());	 
				 }		 
		 }else{
		if(empty($link_id))$this->error('链接id不能为空');
		$data=D('Link')->where("link_id=$link_id")->find();
		$field=getModel('link');
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
		 $this->success('删除成功',U('Link/index'));
	 }else{
		$this->error('删除失败，未知错误');	 
		 }

	}
}
