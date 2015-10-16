<?php
// | Author: 枫叶 <735579768@qq.com> <http://www.zhaokeli.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;
if(!defined("ACCESS_ROOT"))die("Invalid access");
/**
 * 后台用户控制器
 * @author 枫叶 <735579768@qq.com>
 */
class ModuleposController extends AdminController {
 public function index(){
	 	$title=I('title');
		$map['title']=array('like','%'.$title.'%');
    	$this->pages(array(
					'model'=>'modulepos',
					'where'=>$map,
					'order'=>'modulepos_id  desc'
					));
	$this->meta_title='模块位置列表';
	 $this->display();
	 }
 public function add(){
	 if(IS_POST){
		 F('sys_modulepos_tree',null);
		 $model=D('modulepos');
		 if($model->create()){
			 $result=$model->add();
			 	 if($result!==false){
				 	$this->success(L('_ADD_SUCCESS_'),U('modulepos/index'));
				 }else{
					$this->error(L('_UNKNOWN_ERROR_'));	 
					 }
			 }else{
			$this->error($model->geterror());	 
				 }
	  }else{
		//$field=Api('Model/moduleposModel');
		$field=getModelAttr('modulepos');
		$this->meta_title = '添加模块位置';
		$this->assign('fieldarr',$field);
		$this->display('edit');	 
			 }
	
	 }
 public function edit($modulepos_id=null){
	 $model=D('modulepos');
	 if(IS_POST){
		 F('sys_modulepos_tree',null);
		 if($model->create()){
			 $result=$model->save();
			 if($result!==false){
				 	$this->success(L('_UPDATE_SUCCESS_'),U('modulepos/index'));
				 }else{
					$this->error(L('_UNKNOWN_ERROR_'));	 
					 }
			 
			 }else{
			$this->error($model->geterror());	 
				 }		 
		 }else{
		if(empty($modulepos_id))$this->error(L('_ID_NOT_NULL_'));
		$data=M('modulepos')->where("modulepos_id=$modulepos_id")->find();
		
		//$field=Api('Model/moduleposModel');
		$field=getModelAttr('modulepos');
		$this->meta_title = '编辑模块位置';
		$this->assign('fieldarr',$field);
		$this->assign('data',$data); 
		$this->display();	 
			 }
	 
	 }
public function del($modulepos_id){
	$rows=M('Ad')->where("modulepos_id=$modulepos_id")->select();
	if(!empty($rows))$this->error('请删除此模块位置下的模块');
	$result=M('modulepos')->where("modulepos_id=$modulepos_id")->delete();
	 if($result!==false){
		 $this->success(L('_DELETE_SUCCESS_'),U('modulepos/index'));
	 }else{
		$this->error(L('_UNKNOWN_ERROR_'));	 
		 }

	}
}
