<?php
// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');
//ini_set('session.cookie_domain', ".ainiku.com");//跨域访问Session
// 定义应用目录
define('APP_PATH','./App/');
if(!file_exists(APP_PATH . 'Install/Data/install.lock')){
	header('Location: ./index.php?m=Install');
	exit;
}
define('ACCESS_ROOT',true);
define('APP_DEBUG',true);
if(!APP_DEBUG)define('BUILD_LITE_FILE',true);

//自动生成的数据缓存目录
define('DATA_DIR_PATH','./Data/');
define('DATA_DIR_NAME','Data');

//定义静态文件目录
define('__STATIC__','/Public/Static');

//图片缓存目录
define('IMAGE_CACHE_DIR',DATA_DIR_PATH.'cache/imgcache/');
//样式图片缓存
define('STYLE_CACHE_DIR',DATA_DIR_PATH.'cache/scache/');

define('DATA_PATH',DATA_DIR_PATH.'cache/Runtime/Data/');
defined('RUNTIME_PATH') or define ( 'RUNTIME_PATH', DATA_DIR_PATH.'cache/Runtime/');	
if(file_exists(RUNTIME_PATH.'lite.php' && !APP_DEBUG)){
	require RUNTIME_PATH.'lite.php';
	}else{
	require './TP/ThinkPHP.php';	
		}
