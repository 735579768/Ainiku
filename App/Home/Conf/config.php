<?php
if(!defined("ACCESS_ROOT"))die("Invalid access");
return array(
		'DEFAULT_FILTER'        =>  '',
		'LOAD_EXT_CONFIG' => 'user_config',
		"LOAD_EXT_FILE"=>"lib.goods,lib.member",//扩展函数库
 		//加载自定义标签库
		'URL_MODEL' 		=> 2, //URL模式
		 'DEFAULT_THEME'    =>    'default',
	   // 'TMPL_DETECT_THEME'=>    true,
		'HTML_CACHE_ON'     =>    false, // 开启静态缓存
		//'HTML_PATH'			=>_ROOT_.'/html',
		'HTML_CACHE_TIME'   =>    1,   // 全局静态缓存有效期（秒）
		'HTML_FILE_SUFFIX'  =>    '.shtml', // 设置静态缓存文件后缀
		'HTML_CACHE_RULES'  =>     array(  // 定义静态缓存规则
			 // 定义格式2 字符串方式
			 'detail'=>array('{id}',60),
			 'blog:read'=>array('{id}',0),
			 '*'=>array('{$_SERVER.REQUEST_URI|md5}')
		),
	    'TMPL_PARSE_STRING' => array(
	  	'__STATIC__' => __ROOT__ . '/Public/Static',
//        '__IMG__'    => __ROOT__ . '/Public/' . MODULE_NAME . '/images',
//        '__CSS__'    => __ROOT__ . '/Public/' . MODULE_NAME . '/css',
//        '__JS__'     => __ROOT__ . '/Public/' . MODULE_NAME . '/js',
    ),
		'SESSION_PREFIX' => 'anks_home', //session前缀
    	'COOKIE_PREFIX'  => 'ankc_home', // Cookie前缀 避免冲突
);