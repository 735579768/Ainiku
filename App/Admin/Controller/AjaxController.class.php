<?php
namespace Admin\Controller;
defined("ACCESS_ROOT") || die("Invalid access");
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
	 /*设置后台默认主题色*/
	function setdefaultcolor($color){
		M('Config')->where("name='DEFAULT_COLOR'")->setField('value',$color);
		updateConfig();
		}
	/*取自定义菜单列表*/
	function getmenu($url=''){
		empty($url)||$this->getchildmenu($url);
		$str=$this->fetch();
		$this->success($str);
		die();
		}
	 /**
	  *取主导航
	  */
	private function getchildmenu($url){
			//查到当前页面地址
			preg_match('/.*?m\=(.*?)&c\=(.*?)&a\=(.*?)&.*?/',$url,$match);
			$module=$match[1];
			$controll_name=$match[2];
			$action_name=$match[3];
			$current=array();
			if(empty($menu_id)){
				$act=$controll_name;
				$url=$act."/".$action_name;
				$url_sha1=sha1($url);
				$current=F('sys_current_url'.$url_sha1);
				if(empty($current)||APP_DEBUG){

					$current = M('Menu')->where(" url like '%".$url."%'")->find();
					F('sys_current_url'.$url_sha1,$current);
				}
			}else{
				$current=M('Menu')->find($menu_id);
				}
			
			if(empty($current))return false;
			$curid='';
			 if($current['pid']!=0){
				$curid=$current['pid'];
				 }else{
				$curid=$current['id'];	 
					 }	 
			 //取当前分组列表
			 $grouplist=F('sys_grouplist'.$curid);
			 $childnav=F('sys_childnavlist'.$curid);
			 if(APP_DEBUG||empty($grouplist)||empty($childnav)){
				$map['hide']=0;
				$map['pid']=$curid;
				$model=M('menu');
				 $grouplist=$model->where($map)->group('`group`')->order('`group` asc')->select();
				 foreach($grouplist as $key=>$val){
					 $grouplist[$key]['group']=preg_replace('/\d*/','',$val['group']);
					 }
				 
				$childnav=M('menu')->where($map)->order('sort asc')->select();
				 foreach($childnav as $key=>$val){
					 $childnav[$key]['group']=preg_replace('/\d*/','',$val['group']);
					 }
				 F('sys_grouplist'.$curid,$grouplist);
				 F('sys_childnavlist'.$curid,$childnav);
			 }
			$this->assign('_GROUPLIST_',$grouplist);
		 	$this->assign('_CHILDNNAV_',$childnav); 		
		}
}