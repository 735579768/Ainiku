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
//站点入口根路径
define('SITE_PATH', str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']));

//项目相对站点的子目录,子目录以 '/子目录' 形式,没有子目录的话就留空,
//默认项目目录结构跟入口文件在一个目录,如果项目结构被放在一个子目录里,这个地方需要设置
define('__ROOT__', '');

// 定义应用目录
define('APP_PATH', SITE_PATH . '/App/');
//插件路径
define('ADDONS_PATH', SITE_PATH . '/Plugins/');
$entername = str_replace('.php', '', strtolower(basename(__FILE__)));
define('DATA_DIR_PATH', SITE_PATH . '/Data/cache/' . $entername . '/'); //系统自动生成的数据缓存目录
define('__STATIC__', __ROOT__ . '/Public/Static'); //定义静态文件目录
define('IMAGE_CACHE_DIR', DATA_DIR_PATH . 'imgcache/'); //图片缓存目录
define('STYLE_CACHE_DIR', DATA_DIR_PATH . 'scache/'); //样式图片缓存
define('DATA_PATH', DATA_DIR_PATH . 'Runtime/Data/'); //缓存数据的路径
defined('RUNTIME_PATH') or define('RUNTIME_PATH', DATA_DIR_PATH . 'Runtime/');
if (file_exists(realpath(RUNTIME_PATH . 'lite.php')) && !APP_DEBUG) {
	require RUNTIME_PATH . 'lite.php';
} else {
	require SITE_PATH . '/TP/ThinkPHP.php';
}
