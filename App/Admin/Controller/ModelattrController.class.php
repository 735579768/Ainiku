<?php
// | Author: 枫叶 <735579768@qq.com> <http://www.zhaokeli.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;
/**
 * 后台用户控制器
 * @author 枫叶 <735579768@qq.com>
 */
class ModelattrController extends AdminController {
 public function __construct(){
		 parent::__construct();
		 $this->model_name='ModelAttr';
		 //$this->primarykey='model_attr_id';
		 } 
 public function index(){
	 	$title=I('title');
		$model_id=I('model_id');
		$map[__DB_PREFIX__.'model_attr.title']=array('like','%'.$title.'%');
		$map[__DB_PREFIX__.'model_attr.status']=array('egt',0);
		$join=__DB_PREFIX__.'model as a on '.__DB_PREFIX__.'model_attr.model_id=a.model_id';
		$field='*,a.title as modeltitle,'.__DB_PREFIX__.'model_attr.name as name,'.__DB_PREFIX__.'model_attr.title as title,'.__DB_PREFIX__.'model_attr.sort as sort,'.__DB_PREFIX__.'model_attr.update_time as update_time,'.__DB_PREFIX__.'model_attr.status as status';
		if(!empty($model_id))$map[__DB_PREFIX__.'model_attr.model_id']=$model_id;
    	$this->pages(array(
					'model'=>'ModelAttr',
					'field'=>$field,
					'join'=>$join,
					'where'=>$map,
					'order'=>__DB_PREFIX__.'model_attr.sort asc,'.__DB_PREFIX__.'model_attr.status asc,'.__DB_PREFIX__.'model_attr.model_attr_id asc'
					));
	$this->meta_title='表单模型>'.getModelTitle($model_id);
	 $this->display();
	 }
 public function add(){
	 if(IS_POST){
		 $Modelattr=D('ModelAttr');
		 if($Modelattr->create()){
			 			 //为必填项时设置默认正则
			 if($Modelattr->is_require=='1'){
				 if(empty($Modelattr->data_ts))$Modelattr->data_ts="请输入内容!";
				 if(empty($Modelattr->data_ok))$Modelattr->data_ok="格式正确!";
				 if(empty($Modelattr->data_err))$Modelattr->data_err="内容不能为空!";
				 if(empty($Modelattr->data_reg))$Modelattr->data_reg=".*";
				 }
			 $result=$Modelattr->add();
			 	 if(0<$result){
					$atype=I('type');
					if($atype!='custom')$this->addmodelfield($result);
				 	$this->success('添加成功',U('Modelattr/index',array('model_id'=>I('model_id'))));
				 }else{
					$this->error('添加失败,未知错误');	 
					 }
			 }else{
			$this->error($Modelattr->geterror());	 
				 }
	  }else{
		//$field=Api('Model/ModeattrlModel');
		$field=getModel('modelattr');
		$this->meta_title ='表单模型>'.getModelTitle(I('model_id')).'>添加表单';
		$this->assign('fieldarr',$field);
		$this->display('edit');	 
			 }
	
	 }
 //在对应模型中添加字段
 private function addmodelfield($model_attr_id=null){
	 $Modelattr=D('ModelAttr')->find($model_attr_id);
	 
	 //查询模型对应的表名字
	 $modeltable=M('Model')->field('table')->find($Modelattr['model_id']);
	 $table_name=getTable($modeltable['table']);
	 
	 $sql="Describe {$table_name} `{$Modelattr['field']}`";
	 $res=M()->execute($sql);
	 if($res==null){
		 //添加字段
		 $sql="alter table {$table_name} add `{$Modelattr['field']}` ".getFormType($Modelattr['type'],true);
		 $res=M()->execute($sql);		 
		 }
	 
	 }
 public function edit($model_attr_id=null){
	 
	 $Modelattr=D('ModelAttr');
	 if(IS_POST){
		 if($Modelattr->create()){
			 			 //为必填项时设置默认正则
			 if($Modelattr->is_require=='1'){
				 if(empty($Modelattr->data_ts))$Modelattr->data_ts="请输入内容!";
				 if(empty($Modelattr->data_ok))$Modelattr->data_ok="格式正确!";
				 if(empty($Modelattr->data_err))$Modelattr->data_err="内容不能为空!";
				 if(empty($Modelattr->data_reg))$Modelattr->data_reg=".+";
				 }
			 $result=$Modelattr->save();
			 if(0<$result){
					$atype=I('type');
					if($atype!='custom')$this->addmodelfield($model_attr_id);
				 	$this->success('更新成功',U('Modelattr/index',array('model_id'=>I('model_id'))));
				 }else{
					$this->error('更新失败，未知错误');	 
					 }
			 
			 }else{
			$this->error($Modelattr->geterror());	 
				 }		 
		 }else{
		if(empty($model_attr_id))$this->error('id不能为空');
		$data=D('ModelAttr')->where("model_attr_id=$model_attr_id")->find();
		
		//$field=Api('Model/ModeattrlModel');
		$field=getModel('modelattr');
		$this->meta_title ='表单模型>'.getModelTitle(I('model_id')).'>编辑表单';
		$this->assign('fieldarr',$field);
		$this->assign('data',$data); 
		$this->display();	 
			 }
	 
	 }
public function del($model_attr_id=null){
	 $Modelattr=D('ModelAttr')->find($model_attr_id);
	 if($Modelattr['type']!='custom'){
	 //查询模型对应的表名字
	 $modeltable=M('Model')->field('table')->find($Modelattr['model_id']);
	  $table_name=getTable($modeltable['table']);
	 
	 $sql="Describe {$table_name} `{$Modelattr['field']}`";
	 $res=M()->execute($sql);
	 if($res!=null){	 
	//删除数据表中的字段
		 $sql="alter table {$table_name} drop `{$Modelattr['field']}` ";
		 $res=M()->execute($sql);	
		 }	 

		 
	$result=D('ModelAttr')->where("model_attr_id=$model_attr_id")->delete();
	 if($result!==false){
		  F('sys_Modelattr_tree',null);
		 $this->success('删除成功',U('Modelattr/index?model_id='.I('get.model_id')));
	 }else{
		$this->error('删除失败，未知错误');	 
	 }
	}else{
		$this->success('删除成功');
		}
}
}
