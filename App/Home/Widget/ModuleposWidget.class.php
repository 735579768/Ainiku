<?php
namespace Home\Widget;
use Think\Controller;
class ModuleposWidget extends Controller {
	function index($id=null){
		if(empty($id))return false;
		$key=md5('modulepos_id'.$id);
		$str=S($key);
		if(empty($str)||APP_DEBUG){
			$map=array();
			if(is_numeric($id)){
				$map['modulepos_id']=$id;
			}else{
				$map['name']=$id;
				}
			
			$pos=M('Modulepos')->where($map)->find();
			$str='';
			if(!empty($pos)){
				//$list=M('ad')->where("status=1")->order('sort asc,id desc')->select();
				$str=$pos['tplcode'];
				$str=empty($str)?'  ':$str;
				$str=$this->fetch('',$str);
			}else{
				$str=$this->fetch('',$str);
				}
			S($key,$str);			
		}
		echo $str;

		}
}
