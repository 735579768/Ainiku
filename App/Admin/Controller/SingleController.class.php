<?php
// | Author: 枫叶 <735579768@qq.com> <http://www.zhaokeli.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;

/**
 * 后台用户控制器
 * @author 枫叶 <735579768@qq.com>
 */
class SingleController extends AdminController {
 public function index(){
	 	$title=I('title');
		$map['title']=array('like','%'.$title.'%');
    	$this->pages(array(
					'model'=>'Single',
					'where'=>$map,
					'order'=>'status asc,single_id desc'
					));
	$this->meta_title='单页管理';
	 $this->display();
	 }
 public function add(){
	 if(IS_POST){
		 $model=D('Single');
		 if($model->create()){
			 $result=$model->add();
			 	 if($result>0){
				 	$this->success('添加成功',U('Single/index'));
				 }else{
					$this->error('添加失败,未知错误');	 
					 }
			 }else{
			$this->error($model->geterror());	 
				 }
	  }else{
		//$field=Api('Model/SingleModel');
		$field=getModel('single');
		$this->meta_title = '添加单页';
		$this->assign('fieldarr',$field);
		$this->display('edit');	 
			 }
	
	 }
 public function edit($single_id=null){
	 $model=D('Single');
	 if(IS_POST){
		 if($model->create()){
			 $result=$model->save();
			 if($result>0){
				 	$this->success('更新成功',U('Single/index'));
				 }else{
					$this->error('更新失败，未知错误');	 
					 }
			 
			 }else{
			$this->error($model->geterror());	 
				 }		 
		 }else{
		if(empty($single_id))$this->error('single_id不能为空');
		$data=M('Single')->where("single_id=$single_id")->find();
		
		//$field=Api('Model/SingleModel');
		$field=getModel('single');
		$this->meta_title = '编辑单页';
		$this->assign('fieldarr',$field);
		$this->assign('data',$data); 
		$this->display();	 
			 }
	 
	 }
public function del($single_id){
	$result=M('Single')->where("single_id=$single_id")->delete();
	 if($result>0){
		 $this->success('删除成功',U('Single/index'));
	 }else{
		$this->error('删除失败，未知错误');	 
		 }

	}
}
