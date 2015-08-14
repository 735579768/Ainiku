<?php
if(!defined("ACCESS_ROOT"))die("Invalid access");
return array(
	//'DEFAULT_MODULE'=>'Admin',
	//'配置项'=>'配置值'
    'AUTOLOAD_NAMESPACE'=>array(
           'Plugins'=> __ROOT__.'/Plugins',
            ),
	'URL_CASE_INSENSITIVE'  => false,   // 默认false 
	'URL_MODEL'=>  0,  // URL访问模式
	'URL_HTML_SUFFIX'       =>  'html',  // URL伪静态后缀设置
    'MODULE_ALLOW_LIST'    => array('Home','Admin'),
    'DEFAULT_MODULE'     => 'Home',
    'MODULE_DENY_LIST'   => array('Common','User','Install'),
	"LOAD_EXT_FILE"=>"member,image,getinfo,addons",//扩展函数库
	'SHOW_PAGE_TRACE' =>true,
	    /* 数据库配置 */
    /* 数据库配置 */
    'DB_TYPE'   => '[DB_TYPE]', // 数据库类型
    'DB_HOST'   => '[DB_HOST]', // 服务器地址
    'DB_NAME'   => '[DB_NAME]', // 数据库名
    'DB_USER'   => '[DB_USER]', // 用户名
    'DB_PWD'    => '[DB_PWD]',  // 密码
    'DB_PORT'   => '[DB_PORT]', // 端口
    'DB_PREFIX' => '[DB_PREFIX]', // 数据库表前缀
	
    /* 系统数据加密设置 */
    'DATA_AUTH_KEY' => '[AUTH_KEY]', //默认数据加密KEY
	
	//'VAR_ADDON'    =>    'Plugin',
	'TMPL_EXCEPTION_FILE'   =>__ROOT__.'./Public/error.tpl',// 异常页面的模板文件
	
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

);
