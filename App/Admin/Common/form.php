<?php
//表单列表缓存函数库
///**
// *表单类型
// */	
// function A_getFormType($key=null){
//    // TODO 可以加入系统配置
//	$formtype=array(
//		'string'    => '字符串',
//        'select'    =>  '枚举',
//        'radio'     =>  '单选',
//        'checkbox'  =>  '多选',
//        'number'       => '数字',
//	    'double'		=>'双精度数字',
//		'password'       => '密码',
//        'datetime'  =>  '时间',
//        'editor'    =>  '编辑器',
//		'textarea'  =>  '文本框', 
//		'picture'   =>  '上传图片',
//        'file'      =>  '上传附件',
//		'bool'      =>  '布尔',
//		'umeditor'    =>'UM简化编辑器',
//		'batchpicture'   => '批量上传图片',
//		'liandong'=>'城市联动',
//		'custom'    => '自定义表单',
//		'attribute'=>'内容属性',
//    );
//	if(empty($key)){
//    return $formtype;
//	}else{
//	return $formtype[$key];	
//		}
//	 }

/**
 *取模型列表
 */	
 function A_getModellist(){
	 $rearr=array();
	 $list=M('Model')->where('status=1')->select();
	 foreach($list as $val){
		 $rearr[$val['model_id']]=$val['title'];
		 }
	 return $rearr;
	 }
/**返回几个空白字符串***/
function A_getSpace($num){
	$str='';
	for($i=0;$i<$num;$i++){
		$str.='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		}
	$str.='┗━━━';
	return $str;
	}
/**
 *取后台菜单列表缓存
 */
function F_getMenuList($first=true){
	$menulist=F(md5('sys_menu_tree'));
	if(empty($menulist)){
		$menulist=getMenuList();
		F(md5('sys_menu_tree'),$menulist);
	}
	if(!$first)unset($menulist[0]);
	return $menulist;
	}
/**
 *取后台菜单列表
 */
 $menu_lev=0;
function getMenuList($pid=0,$child=false){
		global $menu_lev;
		$menu_lev++;
	    $rearr=array();
		if(!$child and empty($rearr))$rearr[0]='顶级菜单';
		$where['status']=1;
		$where['pid']=$pid;
		//if(!APP_DEBUG)$where['no_del']=0;
		$list=M('menu')->where($where)->order('sort asc,`group` desc')->select();
		if($list){
		foreach($list as $val){
			if($child){
				
				$rearr[$val['id']]=A_getSpace($menu_lev).$val['title']."->({$val['url']})";
				}else{
				$rearr[$val['id']]=$val['title']."->({$val['url']})";	
					}
			$temarr=getMenuList($val['id'],true);
			foreach($temarr as $key=>$v){$rearr[$key]=$v;}
			}
		}
		$menu_lev--;
		return $rearr;
	}
/**
 *取分类导航树缓存
 */
 function F_getNavlist(){
	 $navlist=F(md5('sys_nav_tree'));
	if(empty($catelist)){
	$navlist=A_getNavList();
	F(md5('sys_nav_tree'),$navlist);
	}
	return $navlist;
	 }
/**
 *取分类导航树
 */
function A_getNavList($pid=0,$child=false){
	    global $menu_lev;
	 	$menu_lev++;
	    $rearr=array();
		if(!$child and empty($rearr))$rearr[0]='顶级导航';
		$where['status']=1;
		$where['pid']=$pid;
		$list=M('Nav')->where($where)->order('sort asc')->select();
		if($list){
		foreach($list as $val){
			if($child){	
				$rearr[$val['nav_id']]=A_getSpace($menu_lev).$val['title'];
				}else{
				$rearr[$val['nav_id']]=$val['title'];	
					}
			$temarr=A_getNavList($val['nav_id'],true);
			foreach($temarr as $key=>$v){$rearr[$key]=$v;}
			}
		}
		$menu_lev--;
		return $rearr;		
	}
/**
 *取模块位置置列表带缓存
 */
 function F_getmoduleposList(){
	$menulist=F(md5('sys_modulepos_tree'));
	if(empty($menulist)){
	 $menulist=getmoduleposList();
	 F(md5('sys_modulepos_tree'),$menulist);
	} 
	 return $menulist;	 
	 }
/**
 *取模块位置置列表
 */
 function getmoduleposList(){
	 $rows=M('modulepos')->select();
	 $rearr[1]='默认位置';
	 foreach($rows as $val){
		 $rearr[$val['modulepos_id']]=$val['title'];
		 }
	 return $rearr;
	 }
/**
 *取模块位置置标题
 */
 function getmoduleposTitle($posid=null){
	 if(empty($posid))return '';
	 $rows=M('modulepos')->find($posid);
	 return $rows['title'];
	 }
function F_getGoodsCatelist($first=true){
//	$catelist=F(md5('sys_category_goods_tree'));
//	if(empty($catelist)){
//		$catelist=A_getCatelist(0,false,'goods');
//		F(md5('sys_category_goods_tree'),$catelist);
//	}
//	if(!$first)unset($catelist[0]);
//	return $catelist;	
	
	return  F_getCatelist(true,'goods');
	}
	
function F_getCatelist($first=true,$type=null){
	$catetype=I('category_type');
	$category_type=empty($type)?(empty($catetype)?'article':$catetype):$type;
	$catelist=F(md5('sys_category_'.$category_type.'_tree'));
	if(empty($catelist)){
		$catelist=A_getCatelist(0,false,$category_type);
		F(md5('sys_category_'.$category_type.'_tree'),$catelist);
	}
	if(!$first)unset($catelist[0]);
	return $catelist;	
	}
 function A_getCatelist($pid=0,$child=false,$type='article'){
	    global $menu_lev;
	 	$menu_lev++;
	    $rearr=array();
		if(!$child and empty($rearr))$rearr[0]='顶级分类';
		$where['status']=1;
		$where['pid']=$pid;
		$where['category_type']=$type;
		//if(!APP_DEBUG)$where['dev_show']=0;
		$list=M('category')->where($where)->order('sort asc')->select();
		if($list){
		foreach($list as $val){
			if($child){	
				$rearr[$val['category_id']]=A_getSpace($menu_lev).$val['title'];
				}else{
				$rearr[$val['category_id']]=$val['title'];	
					}
						$temarr=A_getCatelist($val['category_id'],true,$type);
			foreach($temarr as $key=>$v){$rearr[$key]=$v;}
			}
		}
		$menu_lev--;
		return $rearr;	 
	 }