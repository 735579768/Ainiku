<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
namespace Ainiku\TagLib;
use Think\Template\TagLib;
/**
 * CX标签库解析类
 */
class Ank extends TagLib {
    // 标签定义
    protected $tags   =  array(
        // 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
		'insert'=>array('attr'=>'type,name','close'=>0),
		'single' =>array('attr'=>'id','close'=>0),
		'get' =>array('attr'=>'table,field,id,pic','close'=>0),
		'query' =>array('attr'=>'name,sql','close'=>1),
		'nav'   =>  array('attr'=>'name,catid,order,rows','close'=>1),
		'link'   =>  array('attr'=>'name','close'=>1),
		'module'   =>  array('attr'=>'name','close'=>1),
        );
    /**
     * insert标签解析
     * @access public
     * @param array $tag 标签属性
     * @param string $content  标签内容
     * @return string
     */	
	public function _insert($tag,$content){
        $hname  = isset($tag['name'])?$tag['name']:'';
		$htype  = isset($tag['type'])?$tag['type']:'css';	
		$dir  = isset($tag['dir'])?$tag['dir']:'';
		if(empty($dir)){
			if($htype=='js'){$dir=C('TMPL_PARSE_STRING.__JS__');}else{$dir=C('TMPL_PARSE_STRING.__CSS__');}
			}	
		if(empty($htype)&& $htype!='js' && $htype!='css')return '';
		$parse ='<?php ';
		//$parse .='$jscssarr='.;
			$parse .='$jscss=\'\';';
			$parse .='$temarr=explode(",","'.$hname.'");';
			$parse .='$newname=\''.sha1($hname).'\';';
			//$parse .='$newname=str_replace(",","_","'.$hname.'");';
			if($htype=='js'){
				if(!APP_DEBUG){
				$parse .='foreach($temarr as $val):';
				$parse .='$filepath="'.$dir.'/".$val.".js";';
				$parse .='if(!file_exists(__SITE_ROOT__.$filepath)):$filepath=\'__STATIC__/js/\'.$val.".js";endif;';
				$parse .='$jscss.=\'<script src="\'.$filepath.\'" type="text/javascript" ></script>\'."\r\n";';
				$parse .='endforeach;';							
				}else{
				$parse .='if(!file_exists(__SITE_ROOT__.STYLE_CACHE_DIR.MODULE_NAME.\'/\'.$newname.".js")):';
				$parse .='$compressstr=\'\';';
				$parse .='foreach($temarr as $val):';
				$parse .='$filepath="."."'.$dir.'/".$val.".js";';
				$parse .='if(!file_exists(realpath($filepath))):$filepath=\'.__STATIC__/js/\'.$val.".js";endif;';
				$parse .='$compressstr.=compress_js($filepath);';
				$parse .='endforeach;';
				$parse .='writetofile(STYLE_CACHE_DIR.MODULE_NAME.\'/\'.$newname.".js",$compressstr);';
				$parse .='endif;';
				$parse .='$jscss.=\'<script src="\'.str_replace("./","/",STYLE_CACHE_DIR).MODULE_NAME.\'/\'.$newname.\'.js" type="text/javascript" ></script>\'."\r\n";';							
				}
	
			}else{
		//$parse .='if(APP_DEBUG):';
			if(!APP_DEBUG){
				$parse .='foreach($temarr as $val):';
				$parse .='$filepath="'.$dir.'/".$val.".css";';
				$parse .='if(!file_exists(__SITE_ROOT__.$filepath)):$filepath=\'__STATIC__/css/\'.$val.".css";endif;';
				$parse .='$jscss.=\'<link href="\'.$filepath.\'" type="text/css" rel="stylesheet" />\'."\r\n";';
				$parse .='endforeach;';			
				}else{
				$parse .='if(!file_exists(__SITE_ROOT__.str_replace("./","/",STYLE_CACHE_DIR).MODULE_NAME.\'/\'.$newname.".css")):';
				$parse .='$compressstr=\'\';';
				$parse .='foreach($temarr as $val):';
				//$parse .='$jscss.=\'<link href="'.$dir.'/\'.$val.\'.css" type="text/css" rel="stylesheet" />\'."\r\n";';
				$parse .='$filepath="'.$dir.'/".$val.".css";';
				$parse .='if(!file_exists(__SITE_ROOT__.$filepath)):$filepath=\'.__STATIC__/css/\'.$val.".css";endif;';
				$parse .='$compressstr.=compress_css(__SITE_ROOT__.$filepath);';
				$parse .='endforeach;';
				$parse .='writetofile(STYLE_CACHE_DIR.MODULE_NAME.\'/\'.$newname.".css",$compressstr);';
				$parse .='endif;';
				$parse .='$jscss.=\'<link href="\'.str_replace("./","/",STYLE_CACHE_DIR).MODULE_NAME.\'/\'.$newname.\'.css" type="text/css" rel="stylesheet" />\'."\r\n";';					
					}

//		$parse .='else:';
//
//		$parse .='endif;';
			
				}
		$parse .= 'echo $jscss;';
		$parse .= ' ?>';
		return $parse;	
		}
    /**
     * single标签解析
     * @access public
     * @param array $tag 标签属性
     * @param string $content  标签内容
     * @return string

     */
	 public function _single($tag,$content){
		$id  = isset($tag['id'])?$tag['id']:'';
		if(empty($id))return '';
		$parse  = '<?php ';
		$parse .='$skey=md5("_single'.$id.'");';
		$parse .='$__SINGLE_LIST__=S($skey);';
		$parse .='if(empty($__SINGLE_LIST__)||APP_DEBUG):';
		$parse .= '$__SINGLE_LIST__ = M(\'Single\')->where(\'status=1\')->find('.$id.');';
		$parse .='S($skey,$__SINGLE_LIST__);';
		$parse .='endif;';
		$parse .= 'echo $__SINGLE_LIST__[\'content\'];';
		$parse .= ' ?>';
		return $parse;  
		 }
    /**
     * get标签解析
     * @access public
     * @param array $tag 标签属性
     * @param string $content  标签内容
     * @return string
     */
	 public function _get($tag,$content){
        $table  = isset($tag['table'])?$tag['table']:'';
		$id  = isset($tag['id'])?$tag['id']:'';
        $field    = isset($tag['field'])?($tag['field']):'';
		$pic    = isset($tag['pic'])?($tag['pic']):'';
		if(empty($table)||empty($id)||empty($field))return '';
		$parse  = '<?php ';
		$parse .='$skey=md5("'.$table.$id.$field.$pic.'");';
		$parse .='$__GET_LIST__=S($skey);';
		$parse .='if(empty($__GET_LIST__)||APP_DEBUG):';
		$parse .= '$__GET_LIST__ = M(\''.$table.'\')->field(\''.$field.'\')->find('.$id.');';
		$parse .='S($skey,$__GET_LIST__);';
		$parse .='endif;';
		$parse .='if(\''.$pic.'\'==\'true\'):';
		$parse .='echo getPicture($__GET_LIST__[\''.$field.'\']);';
		$parse .='else:';
		$parse .='if(empty($__GET_LIST__[\''.$field.'\'])):';
		$parse .='echo "'.$empty.'";';
		$parse .='else:';
		$parse .= 'echo $__GET_LIST__[\''.$field.'\'];';
		$parse .='endif;';
		$parse .='endif;';
		$parse .= ' ?>';
		return $parse;  
		 }
    /**
     * query标签解析
     * @access public
     * @param array $tag 标签属性
     * @param string $content  标签内容
     * @return string
     */
	 public function _query($tag,$content){
        $name  = isset($tag['name'])?$tag['name']:'vo';
        $sql    = isset($tag['sql'])?($tag['sql']):'';
		$sql   =str_replace('__DB_PREFIX__',C('DB_PREFIX'),$sql);
		$sql =str_replace('neq','<>',$sql);
		if(empty($sql))return '';
		$parse  = '<?php ';
		$parse  .='$__QUERY_LIST__=S(md5("'.md5($sql).'"));';
		$parse .='if(empty($__NAV_LIST__)  || APP_DEBUG):';
		$parse .= '$__QUERY_LIST__ = M(\'\')->query("'.$sql.'");';
		$parse .='S(md5(\''.md5($sql).'\'),$__QUERY_LIST__);';
		$parse .='endif;';
		$parse .= ' ?>';
		$parse .= '<volist name="__QUERY_LIST__" id="'. $name .'">';
		$parse .= $content;
		$parse .= '</volist>';	
		return $parse;  
		 }
    /**
     * nav标签解析
     * @access public
     * @param array $tag 标签属性
     * @param string $content  标签内容
     * @return string
     */
	 public function _nav($tag,$content){
        $name  = isset($tag['name'])?$tag['name']:'vo';
        $order    = isset($tag['order'])?($tag['order'].','):'';
		$parse  = '<?php ';
		$parse .='$__NAV_LIST__=F("sys_navhome_list");';
		$parse .='if(empty($__NAV_LIST__)  || APP_DEBUG):';
		$parse .= '$__NAV_LIST__ = M(\'nav\')->where(\'status>0 and pid=0\')->order(\''.$order.' sort asc,nav_id desc\')->select();';
		$parse .='F(\'sys_navhome_list\',$__NAV_LIST__);';
		$parse .='endif;';
		$parse .= ' ?>';
		$parse .= '<volist name="__NAV_LIST__" id="'. $name .'">';
		$parse .= $content;
		$parse .= '</volist>';
		return $parse; 
		 } 
    /**
     * link标签解析
     * @access public
     * @param array $tag 标签属性
     * @param string $content  标签内容
     * @return string
     */
	 public function _link($tag,$content){
        $name  =    isset($tag['name'])?$tag['name']:'vo';
		$parse  = '<?php ';
		$parse .='$__LINK_LIST__=F("sys_link_tree");';
		$parse .='if(empty($__LINK_LIST__) || APP_DEBUG):';
		$parse .= '$__LINK_LIST__ = M(\'link\')->where(\'status>0\')->order(\' sort asc,link_id desc\')->select();';
		$parse .='F(\'sys_link_tree\',$__LINK_LIST__);';
		$parse .='endif;';
		$parse .= ' ?>';
		$parse .= '<volist name="__LINK_LIST__" id="'. $name .'">';
		$parse .= $content;
		$parse .= '</volist>';
		return $parse; 
		 } 
    /**
     * module标签解析
     * @access public
     * @param array $tag 标签属性
     * @param string $content  标签内容
     * @return string
     */
	 public function _module($tag,$content){
        $name  =    isset($tag['name'])?$tag['name']:'vo';
		$posid  =    isset($tag['modulepos_id'])?$tag['modulepos_id']:'';
		$module_id  =    isset($tag['module_id'])?$tag['module_id']:'';
		if(empty($posid) && empty($module_id))return false;
		$parse  = '<?php ';

		$parse .='$mapmodule=array();';
		$parse .='$mapmodule[\'status\']=1;';
		if(!empty($posid))$parse .='$mapmodule[\''.__DB_PREFIX__.'module.modulepos_id\']='.$posid.';';
		if(!empty($module_id))$parse .='$mapmodule[\''.__DB_PREFIX__.'module.module_id\']='.$module_id.';';	
		$parse .='$__MODULE_LIST__=S(json_encode($mapmodule));';
		$parse .='if(empty($__MODULE_LIST__) || APP_DEBUG):';			
		$parse .= '$__MODULE_LIST__ = M(\'module\')->field(\'*,'.__DB_PREFIX__.'module.title as title,'.__DB_PREFIX__.'module.pic as pic,b.title as postitle,'.__DB_PREFIX__.'module.sort as sort\')->join(\''.__DB_PREFIX__.'modulepos as b on b.modulepos_id='.__DB_PREFIX__.'module.modulepos_id\')->where($mapmodule)->order(\' '.__DB_PREFIX__.'module.sort asc,'.__DB_PREFIX__.'module.module_id desc\')->select();';
		$parse .='S(json_encode($mapmodule),$__MODULE_LIST__);';
		$parse .='endif;';
		$parse .= ' ?>';
		$parse .= '<volist name="__MODULE_LIST__"  id="'. $name .'">';
		$parse .= $content;
		$parse .= '</volist>';
		return $parse; 
		 } 
}
