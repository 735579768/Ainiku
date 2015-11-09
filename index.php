<?php
// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');
//ini_set('session.cookie_domain', ".ainiku.com");//跨域访问Session
// 定义应用目录
define('APP_PATH','./App/');
//常量定义
define('ACCESS_ROOT',true);
define('APP_DEBUG',false);
APP_DEBUG or define('BUILD_LITE_FILE',true);
// 绑定访问Admin模块
//define('BIND_MODULE','Daili');
define('__SITE_ROOT__',str_replace('\\','/',$_SERVER['DOCUMENT_ROOT']));//站点根目录
define('__ROOT__','');//站点目录,没有子目录的话就留空,子目录以'/子目录'形式

define('DATA_DIR_PATH','./Data/');//系统自动生成的数据缓存目录
define('DATA_DIR_NAME','Data');
define('__STATIC__','/Public/Static');//定义静态文件目录
define('IMAGE_CACHE_DIR',DATA_DIR_PATH.'cache/imgcache/');//图片缓存目录
define('STYLE_CACHE_DIR',DATA_DIR_PATH.'cache/scache/');//样式图片缓存
define('DATA_PATH',DATA_DIR_PATH.'cache/Runtime/Data/');//缓存数据的路径
defined('RUNTIME_PATH') or define ( 'RUNTIME_PATH', DATA_DIR_PATH.'cache/Runtime/');
//定义cookies域
preg_match('/(.*\.)?(.*\..*)/',$_SERVER['HTTP_HOST'],$mat);
define('COOKIES_DOMIN',$mat[2]);
if(file_exists(realpath(RUNTIME_PATH.'lite.php')) && !APP_DEBUG){
	require RUNTIME_PATH.'lite.php';
	}else{
	require './TP/ThinkPHP.php';	
		}
