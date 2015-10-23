<?php
/**
 *取分类文档数
 **/
 function getCategoryDocument($category_id=null,$category_type='article'){
	 if(empty($category_id))return 0;
	 $map['category_id']=$category_id;
	 return M($category_type)->where($map)->count();
	 }
//把标记转成对应的文字
function tomark($value,$model,$field,$html=false){
	$arr=getModelAttr($model,$field);
	$arr=$arr['extra'];
	$restr='';
	$temarr=explode(',',$value);
	if($html){
			$restr='<ul class="cl">';
			foreach($arr as $key=>$val){
				$temstr='<li class="cl">';
				if(in_array($key,$temarr)){
					$temstr.='<span>'.$val.'</span><input class="markinput" type="checkbox" checked name="position[]" value="'.$key.'" />';
					}else{
					$temstr.='<span>'.$val.'</span><input class="markinput"  type="checkbox"  name="position[]" value="'.$key.'" />';	
					}
				$temstr.='</li>';
				$restr.=$temstr;	
				}
				$restr.='</ul>';
		}else{
			foreach($temarr as $val){
					if($val!='0')$restr.=trim(empty($restr)?$arr[$val]:','.$arr[$val]);		
				}			
			}

	return empty($restr)?'无':$restr;
	}
function totext($a=1){
	switch($a){
		case -1: return '<span class="red">已删除</span>';break;
		case 0: return '禁用';break;
		case 1: return '正常';break;
		case 2: return '草稿';break;
		}
	}
/**
 *取配置分组列表
 */
function getGroupName($id){
	$config=extratoarray(C('CONFIG_GROUP'));
	return empty($config[$id])?'默认':$config[$id];
	}
/**
 *表单类型
 */	
 function getFormType($key=null,$datatype=false){
    // TODO 可以加入系统配置
	$formtype=array(
		'string'    => '字符串',
        'select'    =>  '枚举',
        'radio'     =>  '单选',
        'checkbox'  =>  '多选',
        'number'       => '数字',
		'double'		=>'双精度数字',
		'password'       => '密码',
        'datetime'  =>  '时间',
        'editor'    =>  '编辑器',
		'textarea'  =>  '文本框', 
		'bigtextarea'  =>  '超大文本框', 
		'picture'   =>  '上传图片',
		'cutpicture'   =>  '剪切图片',
        'file'      =>  '上传附件',
		'bool'      =>  '布尔',
		'color'      =>  '颜色选择器',
		'umeditor'    =>'UM简化编辑器',
		'batchpicture'   => '批量上传图片',
		'liandong'    => '城市联动表单',
		'custom'    => '自定义表单',
		'attribute'=>'内容属性',
    );
	$mysqltype=array(
		'string'    => '  varchar(50) NULL ',
        'select'    =>  '  varchar(50) NULL ',
        'radio'     =>  '  tinyint(1) NULL DEFAULT 0 ',
        'checkbox'  =>  '  varchar(50) NULL ',
        'number'       => 'int(10) NULL  DEFAULT 0 ',
		'double'		=>'double(10,2)  NOT NULL DEFAULT 0',
		'password'       => 'varchar(50) NULL',
         'datetime'  =>  ' varchar(20) NULL ',
        'editor'    =>  'longtext NULL',
		'textarea'  =>  ' text NULL ', 
		'bigtextarea'  =>  ' text NULL ', 
		'picture'   =>  'int(10) NULL DEFAULT 0',
		'cutpicture'   =>  'int(10) NULL DEFAULT 0',
        'file'      =>  'int(10) NULL DEFAULT 0',
		'bool'      =>  '  tinyint(1) NULL DEFAULT 0',
		'color'		=>"varchar(8) NOT NULL DEFAULT '#000'",
		'umeditor'    =>'longtext NULL',
		'batchpicture'   => '  varchar(50) NULL ',
		'liandong'   => '  varchar(20) NULL ',
		'custom'    => '自定义表单',
		'attribute'=>'int(10) NULL  DEFAULT 0',
    );
	if($datatype && !empty($key))return $mysqltype[$key];
		
	if(empty($key) && $datatype===false){
    return $formtype;
	}else{
	return $formtype[$key];	
		}
	 }
/**
 *取表单模型数据数组
 */	
 function getModelTitle($model_id=null){
	 if(empty($model_id))return '未知';
	 return M('Model')->getFieldByModel_id($model_id,'title');
	 }

/**
 *取产品类型表单模型数据数组
 */	
function getGoodsTypeModel($goods_type_id=null){
	if(empty($goods_type_id))return null;
	$map['status']=1;
	if(is_numeric($goods_type_id)){
		$map['goods_type_id']=$goods_type_id;	
			}
	$list=M('GoodsTypeAttribute')->where($map)->order('sort asc')->select();
	$refield=null;
	foreach($list as $key=>$val){
		$namef=$val['name'].'____'.$val['goods_type_attribute_id'];
		$list[$key]['name']=$namef;
		$list[$key]['field']=$namef;
		if(!empty($val['extra'])){
			if($val['extranote']==='1' || $val['extranote']=='func'){
				$func=$val['extra'];
				$list[$key]['extra']=$func();
				}else{
				$list[$key]['extra']=extraToArray($val['extra']);	
					}
		
		}
		}
	return $list;
	}


/**
 * 把返回的数据集转换成Tree
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 * @author 枫叶 <735579768@qq.com>
 */
function list_to_tree($list, $pk='id', $pid = 'pid', $child = '_child', $root = 0) {
    // 创建Tree
    $tree = array();
    if(is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId =  $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            }else{
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}
/*
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 * @author 枫叶 <735579768@qq.com>
 */
function format_bytes($size, $delimiter = '') {
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}
/**此方法用来删除某个文件夹下的所有文件
  *@param string $path为文件夹的绝对路径如d:/tem/
  *@param string $delself 是否把自己也删除,默认不删除
  *@param string $delfolder 删除所有文件夹默认为true,
  *                           如果为false,则只删除所有目录中的文件
  *@返回值为 删除的文件数量(路径和大小)
  *清理缓存很实用,哈哈
  *@author qiaokeli <735579768@qq.com>  www.zhaokeli.com
  */
function delAllFile($fpath,$delself=false,$delfolder=true) { 
    defined('YPATH') OR define('YPATH', $fpath);
    $files= array(); 
    $filepath = iconv('gb2312', 'utf-8', $fpath); 
    if (is_dir($fpath)) { 
        if ($dh = opendir($fpath)) { 
          while (($file = readdir($dh)) !== false) { 
            if($file != '.' && $file != '..') { 
            $temarr= delAllFile($fpath.'/'.$file);
            $files=array_merge($files,$temarr); 
            } 
          } 
        closedir($dh); 
        }
        if($delfolder){
          //过虑删除自己的情况
          if($fpath===YPATH){
            if($delself){
              $files[]=array('path'=>$fpath,'size'=>filesize($fpath));  
              @rmdir($fpath);
            }
          }else{
            $files[]=array('path'=>$fpath,'size'=>filesize($fpath));  
            @rmdir($fpath);            
          }
         }
    } else { 
        if(is_file($fpath)) { 
           $files[]=array('path'=>$fpath,'size'=>filesize($fpath)); 
            @unlink($fpath);
        } 
    } 
  return $files; 
} 
/**
 *检测目录大小
 */
function getDirSize($dir)
     {
		 $sizeResult=0;
         $handle = opendir($dir);//打开文件流
         while (false!==($FolderOrFile = readdir($handle)))//循环判断文件是否可读
         {
             if($FolderOrFile != "." && $FolderOrFile != "..")
             {
                 if(is_dir("$dir/$FolderOrFile"))//判断是否是目录
                 {
                     $sizeResult += getDirSize("$dir/$FolderOrFile");//递归调用
                 }
                 else
                 {
                     $sizeResult += filesize("$dir/$FolderOrFile");
                 }
             }
         }
         closedir($handle);//关闭文件流
         return $sizeResult;//返回大小
}
/* 解析列表定义规则*/
function get_list_field($data, $grid,$model){

	// 获取当前字段数据
    foreach($grid['field'] as $field){
        $array  =   explode('|',$field);//把字段值和函数分开
        $temp  =	$data[$array[0]];
        // 函数支持
        if(isset($array[1])){
            $temp = call_user_func($array[1], $temp);
        }
        $data2[$array[0]]    =   $temp;
    }
    if(!empty($grid['format'])){
        $value  =   preg_replace_callback('/\[([a-z_]+)\]/', function($match) use($data2){return $data2[$match[1]];}, $grid['format']);
    }else{
        $value  =   implode(' ',$data2);
    }

	// 链接支持
	if(!empty($grid['href'])){
		$links  =   explode(',',$grid['href']);
        foreach($links as $link){
            $array  =   explode('|',$link);
            $href   =   $array[0];
            if(preg_match('/^\[([a-z_]+)\]$/',$href,$matches)){
                $val[]  =   $data2[$matches[1]];
            }else{
                $show   =   isset($array[1])?$array[1]:$value;
                // 替换系统特殊字符串
                $href	=	str_replace(
                    array('[DEL]','[DELE]','[HUIFU]','[EDIT]','[MODEL]'),
                    array('del?id=[id]&model_id=[MODEL]','dele?id=[id]&model_id=[MODEL]','huifu?id=[id]&model_id=[MODEL]','edit?id=[id]&model_id=[MODEL]',$model['model_id']),
                    $href);

                // 替换数据变量
                $href	=	preg_replace_callback('/\[([a-z_]+)\]/', function($match) use($data){return $data[$match[1]];}, $href);
				//链接第三个数组为样式类
				$cln=$array[2];
                $val[]	=	'<a class="'.$cln.'" href="'.U($href).'">'.$show.'</a>';
            }
        }
        $value  =   implode(' ',$val);
	}
    return $value;
}
/**
 *返回一个完整的表名字
 */
 function getTable($tablename,$prefix=true){
	 $tablename=lcfirst($tablename);
	 	$tablename=preg_replace('/([A-Z]{1})/','_$1',$tablename);
		return $prefix?strtolower(C('DB_PREFIX').$tablename):strtolower($tablename);
	 }

