<?php
namespace Home\Controller;
use Think\Controller;
class LoginController extends HomeController {
	protected function _empty(){
		//前台统一的404页面
    	header('HTTP/1.1 404 Not Found');
		header("status: 404 Not Found");
    	$this->display('Public:404');
		die();
		}
		 /**
     * 前台台控制器初始化
     */
    protected function _initialize(){
       /* 读取数据库中的配置 */
        $config =   S('DB_CONFIG_DATA');
        if(!$config || APP_DEBUG){
            $config =   api('Config/lists');
            S('DB_CONFIG_DATA',$config);
        }
		//trace($config);
        C($config); //添加配置
						 //定义数据表前缀
		defined('DBPREFIX') or define('DBPREFIX',C('DB_PREFIX'));
		defined('__DB_PREFIX__')  or  define('__DB_PREFIX__',C('DB_PREFIX'));
		C('TMPL_PARSE_STRING',array(
	  	'__STATIC__' => __ROOT__ . '/Public/Static',
        '__IMG__'=> __ROOT__ .'/Public/'.MODULE_NAME.'/'.C('DEFAULT_THEME').'/images',
        '__CSS__'=> __ROOT__ .'/Public/'.MODULE_NAME.'/'.C('DEFAULT_THEME').'/css',
        '__JS__' => __ROOT__ .'/Public/'.MODULE_NAME.'/'.C('DEFAULT_THEME').'/js',
    	));
		defined('UID') or define('UID',auto_login());
//		if(!UID){
//			//没有登陆的情况
//			if(IS_AJAX){
//				$this->error($this->fetch('Public/ajaxlogin'));
//			}else{
//				redirect(U('Public/login'));
//				}
//			 
//		 }else{
//			//赋值当前登陆用户信息
//			$uinfo=session('uinfo');
//			$map[getAccountType($uinfo['username'])]=$uinfo['username'];
//			$jin=__DB_PREFIX__."member_group as a on ".__DB_PREFIX__."member.member_group_id=a.member_group_id";
//			$field="*,".__DB_PREFIX__."member.status as status";
//			$user = D('Member')->field($field)->where($map)->join($jin)->find();
//			$this->uinfo=$user;
//			$this->member_group_id=$user['member_group_id'];
//			session('uinfo',$user);
//			$this->assign('uinfo',$user);			 
//			 }
	}
	/**
      * 通用分页列表数据集获取方法
      *
      *  可以通过url参数传递where条件,例如:  index.html?name=asdfasdfasdfddds
      *  可以通过url空值排序字段和方式,例如: index.html?_field=id&_order=asc
      */
     protected function Pages ($conf){
		$model=@$conf['model'];
		$where=isset($conf['where'])?$conf['where']:null;
		if(is_string($where)) $where=str_replace('__DB_PREFIX__',DBPREFIX,$where);
		$join=isset($conf['join'])?$conf['join']:null;
		$field=isset($conf['field'])?$conf['field']:null;
		$order=isset($conf['order'])?$conf['order']:null;
		$rows=isset($conf['rows'])?$conf['rows']:10;
       $User = M($model); // 实例化User对象
       $count=0;
	    $field=str_replace('__DB_PREFIX__',DBPREFIX,$field);
		 $order=str_replace('__DB_PREFIX__',DBPREFIX,$order);
       if(is_array($join)){
		   $join[0]=str_replace('__DB_PREFIX__',DBPREFIX,$join[0]);
		   $join[1]=str_replace('__DB_PREFIX__',DBPREFIX,$join[1]);
         $count      = $User->where($where)->field($field)->order($order)->join($join[0])->join($join[1])->count();// 查询满足要求的总记录数
       }else{
		   $join=str_replace('__DB_PREFIX__',DBPREFIX,$join);
         $count      = $User->where($where)->field($field)->order($order)->join($join)->count();// 查询满足要求的总记录数
       }
     
       $Page       = new \Think\Page($count,$rows);// 实例化分页类 传入总记录数和每页显示的记录数(25)
       //$Page->url=$pageurl;
       $Page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
      
			  //分页跳转的时候保证查询条件
		$mp=@array_merge($where,$_POST,$_GET);
		foreach($mp as $key=>$val) {
			if(!is_array($val)){
			$Page->parameter[$key]   =   urlencode($val);
			}
		}
	  
	   $show       = $Page->show();// 分页显示输出
       // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
       if(is_array($join)){
         $list = $User->where($where)->field($field)->order($order)->join($join[0])->join($join[1])->limit($Page->firstRow.','.$Page->listRows)->select();
       }else{
         $list = $User->where($where)->field($field)->join($join)->order($order)->limit($Page->firstRow.','.$Page->listRows)->select();
       }
     
       $this->assign('_total',$count);
       $this->assign('_page',$show);// 赋值分页输出
       $this->assign('_list', $list);
	   return $list;
     }
	 //重写输出模板
	protected function display($templateFile='',$charset='',$contentType='',$content='',$prefix=''){
				
		 $str=$this->fetch($templateFile);
		 $patterns[]='/\n\s*\r/';
		 $replacements[]='';
		 $regstr=C('TPL_REG');
		 
		$tema=explode('\n',$regstr);
		foreach($tema as $val){
				if(strpos($regstr,'#')!==false){
					$temb=explode('#',$val);
					if(count($temb)===2){
						$patterns[]=$temb[0];
						$replacements[]=$temb[1];
					}
				}
			}	 
//		 $patterns[]='/<img(.*?)\s{1}src=["|\']([^\'|\"]+?)["|\'](.*?)>/';
//		 $replacements[]='<img$1 data-original="$2" src="/Public/Static/images/preload.png"$3>';
		 
		 $patterns[]='/<img(.*?)src=["|\']["|\'](.*?)>/';
		 $replacements[]='<img$1src="'.C('DEFAULT_IMG').'"$2>';
		 $str=preg_replace($patterns,$replacements,$str);
		 echo $str;
		 }
}