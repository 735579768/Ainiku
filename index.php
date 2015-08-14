<?php
// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');
ini_set('session.cookie_domain', ".ainiku.com");//跨域访问Session
// 定义应用目录
define('APP_PATH','./App/');
if(!file_exists(APP_PATH . 'Install/Data/install.lock')){
	header('Location: ./index.php?m=Install');
	exit;
}
define('ACCESS_ROOT',true);
define('APP_DEBUG',true);
if(!APP_DEBUG)define('BUILD_LITE_FILE',true);

////定义默认后台地址用小字字母
//define('ADMIN_INDEX','kladmin');
//$pathinfo=isset($_SERVER['PATH_INFO'])?$_SERVER['PATH_INFO']:'';
//require_once './Plugins/MobileCheck.php';
//$detect = new Mobile_Detect();
//
//
//
//$dominhost=$_SERVER['HTTP_HOST'];
//if($dominhost=='app.xiadan.loc' || $dominhost=='app.0yuanwang.com'){
//	    define('DEFAULTMODULE','Android');
//		//define('BIND_MODULE','Android');	
//	}
//if(!defined('BIND_MODULE')){
//if(preg_match('/^\/'.ADMIN_INDEX.'\/(.*?)$/',$pathinfo)){
//		//转向到后台首页
////		if('/'.ADMIN_INDEX.'/'==$pathinfo){
////			header('location:/'.ADMIN_INDEX.'/Index/index');
////			die();
////			}
//		define('DEFAULTMODULE',ADMIN_INDEX);
//		//define ( 'RUNTIME_PATH', './Uploads/Runtime/Admin/' );
//}else if($detect->isMobile()){
//	    define('DEFAULTMODULE','Mobile');
//		//define('DEFAULTMODULE','Mobile');
//}else{
//		define('DEFAULTMODULE','Home');
//		//define('BIND_MODULE','Home');
//		}
//}

define('DATA_PATH','./Uploads/Runtime/Data/');
//defined( 'RUNTIME_PATH') or define ( 'RUNTIME_PATH', './Uploads/Runtime/'.DEFAULTMODULE.'/' );
defined( 'RUNTIME_PATH') or define ( 'RUNTIME_PATH', './Uploads/Runtime/');	
if(file_exists(RUNTIME_PATH.'lite.php' && !APP_DEBUG)){
	require RUNTIME_PATH.'lite.php';
	}else{
	require './TP/ThinkPHP.php';	
		}
