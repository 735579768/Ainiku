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
				 	$this->success(L('_ADD_SUCCESS_'),U('Single/index'));
				 }else{
					$this->error(L('_UNKNOWN_ERROR_'));	 
					 }
			 }else{
			$this->error($model->geterror());	 
				 }
	  }else{
		//$field=Api('Model/SingleModel');
		$field=getModelAttr('single');
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
				 	$this->success(L('_UPDATE_SUCCESS_'),U('Single/index'));
				 }else{
					$this->error(L('_UNKNOWN_ERROR_'));	 
					 }
			 
			 }else{
			$this->error($model->geterror());	 
				 }		 
		 }else{
		if(empty($single_id))$this->error(L('_ID_NOT_NULL_'));
		$data=M('Single')->where("single_id=$single_id")->find();
		
		//$field=Api('Model/SingleModel');
		$field=getModelAttr('single');
		$this->meta_title = '编辑单页';
		$this->assign('fieldarr',$field);
		$this->assign('data',$data); 
		$this->display();	 
			 }
	 
	 }
public function del($single_id){
	$result=M('Single')->where("single_id=$single_id")->delete();
	 if($result>0){
		 $this->success(L('_DELETE_SUCCESS_'),U('Single/index'));
	 }else{
		$this->error(L('_UNKNOWN_ERROR_'));	 
		 }

	}
}
