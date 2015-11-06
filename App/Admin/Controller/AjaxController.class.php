<?php
namespace Admin\Controller;
if(!defined("ACCESS_ROOT"))die("Invalid access");
class AjaxController extends AdminController {
 function admininfo(){
	 echo hook('Admininfo');
	 die();
	 }
 function menusort(){
	 $json=json_decode(I('post.sortda'));
	 foreach($json as $key=>$val){
		 $pid=$val->pid;
		 if(count($val->child)>0){
			foreach($val->child as $k=>$v){
				$id=$v->id;
				$sort=$v->sorts;
				//echo $pid.'_'.$id.'_'.$sort;
				//保存到数据库
				$result=M('Menu')->where("id=$id")->save(array('pid'=>$pid,'sort'=>$sort,'update_time'=>NOW_TIME));
				($result>0)||$this->error('数据保存失败');
			}
			 
		}
	}
	$this->success('数据保存成功');
	 die();
	 }
	function setdefaultcolor($color){
		M('Config')->where("name='DEFAULT_COLOR'")->setField('value',$color);
		}
}