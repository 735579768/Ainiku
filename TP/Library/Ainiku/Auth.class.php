<?php
namespace Ainiku;
class Auth{
	//private $nodelist=null;
	//private $accessnodelist=null;
	private $noaccessnodelist=null;//不能被访问的节点
	private $uid=null;
 function __construct(){
		$this->uid=UID;
		$uinfo=session('uinfo');
		$this->noaccessnodelist=F('membegroupnodelist'.$uinfo['member_group_id']);
		if(empty($this->noaccessnodelist) || APP_DEBUG){
			$uinfoauth=M('MemberGroup')->field('auth')->find($uinfo['member_group_id']);
			$node_idlist=implode(',',json_decode($uinfoauth['auth'],true));
			$node_idlist=empty($node_idlist)?'*':$node_idlist;
			$this->noaccessnodelist=M('Node')->where(array(
												'pid'=>array('neq',0),
												'status'=>1,
												'node_id'=>array('not in',$node_idlist)
												))->select();
			F('membegroupnodelist'.$uinfo['member_group_id'],$this->noaccessnodelist);
		}

		}
//检测当前地址是否在允许访问的列表
 function check(){
	 $rebool=true;
	 if(IS_ADMIN){
		return $rebool; 	
		}else{
			//正则匹配
			$tembool=false;
			foreach($this->noaccessnodelist as $val){
				$pattern='';
				$url=str_replace('.'.C('URL_HTML_SUFFIX' ),'',U($val['name']));
				$url=preg_quote($url);
				$url=preg_replace('/\//i','\/',$url);
				if($val['is_all']==1){
				$pattern='/^'.$url.'$/i';				
				}else{
				$pattern='/(.*)'.$url.'(.*)/i';					
					}
				$tembool=preg_match($pattern,__SELF__);
				if($tembool){$rebool=false;break;}
			}
			return $rebool;
			}
	 }
	 //把没有权限的链接替换掉
	function replaceurl($str=null){
	 if(IS_ADMIN){
		return $str; 	
		}else{
			if(!empty($this->noaccessnodelist)){
			//正则替换链接
			$are='([^<|^>]*?)';
			foreach($this->noaccessnodelist as $val){
				$url=trim($val['name']);
				if(!empty($url)){
				$pattern='/';
				$url=U($url);
				$url=str_replace(array('.'.C('URL_HTML_SUFFIX' )),array(''),$url);
				$url=preg_quote($url);
				$url=str_replace('/','\/',$url);
				//把链接按钮(带有btn的操作)替换掉
				if($val['is_all']==1){
						$pattern.='<[tag]'.$url.'[tag]>[tag]<[tag]>';
						$pattern.='|<([^<|^>|^\/]*?)>[tag]<[tag]'.$url.'[tag]>[tag]<\/[tag]>[tag]<\/[tag]>';				
					}else{
						//$url=preg_replace('/(\?)|(\=)|(\/)|(\.)/i','\\\$1$2$3$4',$url);
						$pattern.='<[tag]'.$url.'[tag]>[tag]<[tag]>';
						$pattern.='|<([^<|^>|^\/]*?)>[tag]<[tag]'.$url.'[tag]>[tag]<\/[tag]>[tag]<\/[tag]>';					
						}
				$pattern.='/i';			
				$pattern=str_replace('[tag]','([^<|^>]*?)',$pattern);
				//echo $pattern.'--<br>';
				$str=preg_replace($pattern,'',$str);
				}	
			}
			//移除后台左边空的菜单项目
			$matchs=array();
			$mat=preg_match_all('/<dl[^<|^>]*?class\=\"menu\">[\s\S]*?<\/dl>/iu',$str,$matchs);	
			foreach($matchs[0] as $val){
				if(strpos($val,'mitem')===false){
					$str=str_replace($val,'',$str);
					}
				}
			//判断有没有按钮替换换操作标题
			$pattern='/<td([\s\S]*?)>([\s\S]*?)<a([\s\S]*?)btn([\s\S]*?)>([\s\S]*?)<\/a>([\s\S]*?)<\/td>/i';
			$resu=preg_match($pattern,$str,$matchs);
			if($resu===0){
				$str=preg_replace('/<th(.*?)>(.*?)操作(.*?)<\/th>/','',$str);
				}
			}	
			return $str;
		}
	}
}
?>