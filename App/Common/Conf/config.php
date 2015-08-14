<?php
if(!defined("ACCESS_ROOT"))die("Invalid access");
return array(
	//'DEFAULT_MODULE'=>'Admin',
	//'配置项'=>'配置值'
	'LOAD_EXT_CONFIG' => 'db_config',
	'TAGLIB_PRE_LOAD'     =>    'Ainiku\\TagLib\\Article,Ainiku\\TagLib\\Ank',
    'AUTOLOAD_NAMESPACE'=>array(
           'Plugins'=>'./Plugins',
            ),
	'URL_CASE_INSENSITIVE'  => false,   // 默认false 
	'URL_MODEL'=>  0,  // URL访问模式
	'URL_HTML_SUFFIX'       =>  'html',  // URL伪静态后缀设置
	//'URL_MODULE_MAP'    =>    array('kladmin'=>'admin'),//隐藏后台模块
    'MODULE_ALLOW_LIST'    => array('Home','Admin','Install','User'),
    'DEFAULT_MODULE'     => '',
    'MODULE_DENY_LIST'   => array('Common'),
	"LOAD_EXT_FILE"=>"image,getinfo,addons,goods,form",//扩展函数库
	'SHOW_PAGE_TRACE' =>true,	
    /* 系统数据加密设置 */
    'DATA_AUTH_KEY' => 'e_~FA3XBEu,ha[<WO7gqljG0/@z)!"*1T:JM#>^+', //默认数据加密KEY
	
	//'VAR_ADDON'    =>    'Plugin',
//	'TMPL_EXCEPTION_FILE'   =>'./Public/error.tpl',// 异常页面的模板文件
	
	//缓存设置
	'DATA_CACHE_TIME'       =>  3600,      // 数据缓存有效期 0表示永久缓存
	'DATA_CACHE_COMPRESS'   =>  true,   // 数据缓存是否压缩缓存
	'DATA_CACHE_CHECK'      =>  false,   // 数据缓存是否校验缓存
	'DATA_CACHE_PREFIX'     =>  'ank_',     // 缓存前缀
	'DATA_CACHE_TYPE'       =>  'File',  // 数据缓存类型,支持:File|Db|Apc|Memcache|Shmop|Sqlite|Xcache|Apachenote|Eaccelerator
	'DATA_CACHE_PATH'       =>  TEMP_PATH,// 缓存路径设置 (仅对File方式缓存有效)
	'DATA_CACHE_SUBDIR'     =>  true,    // 使用子目录缓存 (自动根据缓存标识的哈希创建子目录)
	'DATA_PATH_LEVEL'       =>  5,        // 子目录缓存级别
	
	//日志设置
	'LOG_RECORD'            =>  true,   // 默认记录日志
	'LOG_TYPE'              =>  'File', // 日志记录类型 默认为文件方式
	'LOG_LEVEL'             =>  'EMERG,ALERT,CRIT,ERR',// 允许记录的日志级别
	'LOG_EXCEPTION_RECORD'  =>  true,    // 是否记录异常信息日志
	
	'SESSION_PREFIX'        =>  'ank_', // session 前缀
	
	 /* SESSION 和 COOKIE 配置 */
    'SESSION_PREFIX' => 'anks_', //session前缀
    'COOKIE_PREFIX'  => 'ankc_', // Cookie前缀 避免冲突
	'TMPL_EXCEPTION_FILE'   =>APP_PATH.'sys_exception.tpl',// 异常页面的模板文件
	'TMPL_ACTION_ERROR'     =>APP_PATH.'sys_jump.tpl', // 默认错误跳转对应的模板文件
	'TMPL_ACTION_SUCCESS'   =>APP_PATH.'sys_jump.tpl', // 默认成功跳转对应的模板文件板文件

);
