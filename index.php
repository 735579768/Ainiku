<?php
header("Content-type:text/html;charset=utf-8");
// 检测PHP环境
if (version_compare(PHP_VERSION, '5.3.0', '<')) {
	die('require PHP > 5.3.0 !');
}

//ini_set('session.cookie_domain', ".ainiku.com");//跨域访问Session

//常量定义
define('ACCESS_ROOT', true);
define('APP_DEBUG', true);
APP_DEBUG or define('BUILD_LITE_FILE', true);
// 绑定访问Admin模块
//define('BIND_MODULE','Daili');
//站点根路径
define('__SITE_ROOT__', str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']));

//项目相对站点的子目录,子目录以 '/子目录' 形式,没有子目录的话就留空,
define('__ROOT__', '');

//项目绝对路径
define('__ROOT_PATH__', __SITE_ROOT__ . __ROOT__);

// 定义应用目录
define('APP_PATH', __ROOT_PATH__ . '/App/');
define('DATA_DIR_PATH', __ROOT_PATH__ . '/Data/'); //系统自动生成的数据缓存目录
//define('DATA_DIR_NAME', 'Data');
define('__STATIC__', __ROOT__ . '/Public/Static'); //定义静态文件目录
define('IMAGE_CACHE_DIR', DATA_DIR_PATH . 'cache/imgcache/'); //图片缓存目录
define('STYLE_CACHE_DIR', DATA_DIR_PATH . 'cache/scache/'); //样式图片缓存
define('DATA_PATH', DATA_DIR_PATH . 'cache/Runtime/Data/'); //缓存数据的路径
defined('RUNTIME_PATH') or define('RUNTIME_PATH', DATA_DIR_PATH . 'cache/Runtime/');
//定义cookies域
//preg_match('/(.*\.)?(.*\..*)/', $_SERVER['HTTP_HOST'], $mat);
$domain = explode('.', $_SERVER['HTTP_HOST']);
define('COOKIES_DOMIN', '.' . $domain[count($domain) - 1]);
if (file_exists(realpath(RUNTIME_PATH . 'lite.php')) && !APP_DEBUG) {
	require RUNTIME_PATH . 'lite.php';
} else {
	require __ROOT_PATH__ . '/TP/ThinkPHP.php';
}
