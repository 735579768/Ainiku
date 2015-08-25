<?php
//所有模块共用的一个类
namespace Common\Controller;
use Think\Controller;
class CommonController extends Controller {
	public function __construct(){
		parent::__construct();
		//查询黑名单
		$ip=get_client_ip();
		$iplist=C('IP_BLACKLIST');
		$iplist=extratoarray($iplist);
		if(!empty($iplist)){
			if(in_array($ip,$iplist))die('ip is no access!');
			}
		
		
		}
	protected function _empty(){
		if(!APP_DEBUG){
			//前台统一的404页面
			header('HTTP/1.1 404 Not Found');
			header("status: 404 Not Found");
			$this->display('Public:404');				
				}

		die();
		}
		 /**
     * 前台台控制器初始化
     */
    protected function _initialize(){
       /* 读取数据库中的配置 */
        $config =   F('DB_CONFIG_DATA');
		
        if(!$config || APP_DEBUG){
            $config =   api('Config/lists');
            F('DB_CONFIG_DATA',$config);
        }
        C($config); //添加配置
		C('TMPL_PARSE_STRING',array(
	  	'__STATIC__' => __ROOT__ . '/Public/Static',
        '__IMG__'=> __ROOT__ .'/Public/'.MODULE_NAME.'/'.C('DEFAULT_THEME').'/images',
        '__CSS__'=> __ROOT__ .'/Public/'.MODULE_NAME.'/'.C('DEFAULT_THEME').'/css',
        '__JS__' => __ROOT__ .'/Public/'.MODULE_NAME.'/'.C('DEFAULT_THEME').'/js',
    	));
		defined('__DB_PREFIX__')  or  define('__DB_PREFIX__',C('DB_PREFIX'));
		defined('UID') or define('UID',is_login());
		if(C('WEB_SITE_CLOSE') &&  UID!=1){$this->show('网站维护中请稍后访问');die();}
		//var_dump($config);
//		if(!UID){
//			//没有登陆的情况
//			 $this->redirect(U('Member/login'));
//		 }
	}
	/**
      * 通用分页列表数据集获取方法
      *
      *  可以通过url参数传递where条件,例如:  index.html?name=asdfasdfasdfddds
      *  可以通过url空值排序字段和方式,例如: index.html?_field=id&_order=asc
      */
     protected function Pages ($conf){
		$model=@$conf['model'];
		$whe=isset($conf['where'])?$conf['where']:null;
		$join=isset($conf['join'])?$conf['join']:null;
		$field=isset($conf['field'])?$conf['field']:null;
		$order=isset($conf['order'])?$conf['order']:null;
		$rows=isset($conf['rows'])?$conf['rows']:10;
		$url=isset($conf['url'])?$conf['url']:null;
       $User = M($model); // 实例化User对象
       $count=0;
	   if(is_string($whe)){
		   	 $whe=str_replace('__DB_PREFIX__',C('DB_PREFIX'),$whe);
		   }else{
			   $temarr=array();
			   foreach($whe as $key=>$val){
				   $temarr[str_replace('__DB_PREFIX__',C('DB_PREFIX'),$key)]=$val;
				   }
				$whe=$temarr;
			   }
	   	 $field=str_replace('__DB_PREFIX__',C('DB_PREFIX'),$field);
		 $order=str_replace('__DB_PREFIX__',C('DB_PREFIX'),$order);
       if(is_array($join)){
		    $join[0]=str_replace('__DB_PREFIX__',C('DB_PREFIX'),$join[0]);
		   $join[1]=str_replace('__DB_PREFIX__',C('DB_PREFIX'),$join[1]);
         $count      = $User->where($whe)->field($field)->order($order)->join($join[0])->join($join[1])->count();// 查询满足要求的总记录数
       }else{
		   $join=str_replace('__DB_PREFIX__',C('DB_PREFIX'),$join);
         $count      = $User->where($whe)->field($field)->order($order)->join($join)->count();// 查询满足要求的总记录数
       }
     
       $Page       = new \Think\Page($count,$rows);// 实例化分页类 传入总记录数和每页显示的记录数(25)
	     if(!empty($url))$Page->url=$url;
       //$Page->url=$pageurl;
       $Page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
      
			  //分页跳转的时候保证查询条件
		$mp=array_merge(I('post.'),I('get.'));
		if(is_array($mp)){
			foreach($mp as $key=>$val) {
				if(!is_array($val)){
				$Page->parameter[$key]   = toUtf8($val);
				}
		}
		}
	  
	   $show       = $Page->show();// 分页显示输出
       // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
       if(is_array($join)){
         $list = $User->where($whe)->field($field)->order($order)->join($join[0])->join($join[1])->limit($Page->firstRow.','.$Page->listRows)->select();
       }else{
         $list = $User->where($whe)->field($field)->join($join)->order($order)->limit($Page->firstRow.','.$Page->listRows)->select();
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
		if(C('SITE_PRELOAD')){
		 $patterns[]='/<img(.*?)\s{1}src=["|\']([^\'|\"]+?)["|\'](.*?)>/';
		 $replacements[]='<img$1 data-original="$2" src="'.__STATIC__.'/images/preload.png"$3>';
			}
		 $patterns[]='/<img(.*?)src=["|\']["|\'](.*?)>/';
		 $replacements[]='<img$1src="'.C('DEFAULT_IMG').'"$2>';
		 
		 $str=preg_replace($patterns,$replacements,$str);
		 echo $str;
		 }
}
