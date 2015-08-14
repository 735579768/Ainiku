<?php
namespace Admin\Widget;
use Think\Controller;
class FormWidget extends Controller {
	private function isvar(){
		if(!isset($GLOBALS['formjs'])){
			$GLOBALS['formjs']=array('datetime'=>0,'picture'=>0,'editor'=>0,'umeditor'=>0,'color'=>0,'dandu'=>0);
		}
		}
	//返回一个单个的表单数据
//	public function getForm($datatype=array(),$data=null){
//		$this->isvar();
//		if(!empty($data))$datatype['data']=$data;
//		$formjs=array('datetime'=>false,'picture'=>false,'editor'=>false);
//		$val=$datatype;
//		if($val['type']==='file' || $val['type']==='picture' || $val['type']==='batchpicture')$GLOBALS['formjs']['picture']++;
//		if($val['type']==='datetime')$GLOBALS['formjs']['datetime']++;
//		if($val['type']==='editor')$GLOBALS['formjs']['editor']++;
//		if($val['type']==='umeditor')$GLOBALS['formjs']['umeditor']++;
//		$datatype['data']=$datatype['value'];
//		$field[]=$datatype;
//		$this->assign('fieldarr',$field);
//		$this->assign('formjs',$GLOBALS['formjs']);
//		$this->display('Widget:Form/index');		
//		}
	public function data($field,$da=array()){
		//单独一个表单
		$this->isvar();
		$GLOBALS['formjs']['dandu']++;
		$this->tabdata($field,$da);
		////初始化全局js变量
//		$this->isvar();
//		if(isset($field['title']))$field=array($field);
//		//下面变量保证只自加一次
//		$pic=false;$dtm=false;$edr=false;$umedr=false;$colo=false;
//		foreach($field as $key=>$val){
//			if($val['type']==='file' || $val['type']==='picture' || $val['type']==='batchpicture')$pic=true;
//			if($val['type']==='datetime'){$dtm=true;}
//			if($val['type']==='editor'){$edr=true;}
//			if($val['type']==='umeditor'){$umedr=true;}
//			if($val['type']==='color'){$colo=true;}
//			$ttt=trim($val['field']);
//			$field[$key]['data']=isset($da[$ttt])?$da[$ttt]:'';
//			if($field[$key]['data']==='' || $field[$key]['data']===null ){$field[$key]['data']=isset($val['value'])?$val['value']:'';	}
//			if(!isset($val['is_require'])){$field[$key]['is_require']=false;}			
//			}
//		if($pic)$GLOBALS['formjs']['picture']++;
//		if($dtm)$GLOBALS['formjs']['datetime']++;
//		if($edr)$GLOBALS['formjs']['editor']++;
//		if($umedr)$GLOBALS['formjs']['umeditor']++;
//		if($colo)$GLOBALS['formjs']['color']++;
//		//var_dump($field);
//		$this->assign('fieldarr',$field);
//		$this->assign('formjs',$GLOBALS['formjs']);
//		$this->display('Widget:Form/index');
		}
	public function tabdata($field,$da=array()){
		//判断是不是编辑状态
		if(strpos(ACTION_NAME,'edit')!==false){
			$this->assign('actionstatus','edit');
			}else if(strpos(ACTION_NAME,'add')!==false){
			$this->assign('actionstatus','add');
				}else{
			$this->assign('actionstatus','other');		
					}
		//缓存
		$cacheform=sha1(json_encode($field).json_encode($da));
		$formstr=F($cacheform);
		if(empty($formstr)|| APP_DEBUG){
		$this->isvar();
		//判断是不是数组不是的话组合成数组
		if(isset($field['title']))$field=array($field);
		//下面变量保证只自加一次
		$pic=false;$dtm=false;$edr=false;$umedr=false;$colo=false;
		$data['jc']=null;
		$data['kz']=null;
		foreach($field as $key=>$val){
			if($val['type']==='file' || $val['type']==='picture' || $val['type']==='batchpicture')$pic=true;
			if($val['type']==='datetime'){$dtm=true;}
			if($val['type']==='editor'){$edr=true;}
			if($val['type']==='umeditor'){$umedr=true;}
			if($val['type']==='color'){$colo=true;}
			$ttt=trim($val['field']);
			$field[$key]['data']=isset($da[$ttt])?$da[$ttt]:'';
			if($field[$key]['data']==='' || $field[$key]['data']===null ){$field[$key]['data']=isset($val['value'])?$val['value']:'';	}
			if(!isset($val['is_require'])){$field[$key]['is_require']=false;}	
			//判断is_show是不是空或不符合规则
			if(!isset($field[$key]['is_show']))$field[$key]['is_show']=3;
			if(isset($val['attrtype']) && $val['attrtype']=='0'){
				$data['jc'][]=$field[$key];
				}else{
				$data['kz'][]=$field[$key];	
					}
			}
		if($pic)$GLOBALS['formjs']['picture']++;
		if($dtm)$GLOBALS['formjs']['datetime']++;
		if($edr)$GLOBALS['formjs']['editor']++;
		if($umedr)$GLOBALS['formjs']['umeditor']++;
		if($colo)$GLOBALS['formjs']['color']++;

		$fiearr[]=$data['jc'];
		$fiearr[]=$data['kz'];
		$this->assign('fiearr',$fiearr);
		$this->assign('formjs',$GLOBALS['formjs']);
		$formstr=$this->fetch('Widget:Form/tab');
		F($cacheform,$formstr);
		}
		echo $formstr;
		
		}
}
