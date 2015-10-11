<?php
namespace Admin\Controller;
if(!defined("ACCESS_ROOT"))die("Invalid access");
class RegionController extends AdminController {
function index($id=null,$selid=null){
		$map['parent_id']=$id;
		$list=M('area')->where($map)->field("area_id,area_name")->order('area_id asc')->select();
		$str=F('_region/area_pid_'.$id.'selid'.$selid);
		if(empty($str)){
		$str='<option value="0">请选择--</option>';
		foreach($list as $val){
			$sel='';
			if($val['area_id']==$selid){$sel='selected';}
			$str.="<option value='{$val['area_id']}' $sel >{$val['area_name']}</option>";
			}
		F('_region/area_pid_'.$id.'selid'.$selid,$str);
		}
		$this->ajaxreturn($str);		
	}
}