<?php
defined("ACCESS_ROOT") || die("Invalid access");
return array(
	//'DEFAULT_MODULE'=>'Admin',
	//'配置项'=>'配置值'
	'LOAD_EXT_CONFIG'      => 'db_config,reg_config,status',
	'TAGLIB_PRE_LOAD'      => 'Ainiku\\TagLib\\Article,Ainiku\\TagLib\\Ank',
	'AUTOLOAD_NAMESPACE'   => array(
		//自动加载扩展命名空间
		'Plugins' => ADDONS_PATH,
	),
	'URL_CASE_INSENSITIVE' => false, // 默认false
	'URL_MODEL'            => 0, // URL访问模式
	'URL_HTML_SUFFIX'      => 'html', // URL伪静态后缀设置
	//'URL_MODULE_MAP'    =>    array('kladmin'=>'admin'),//隐藏后台模块
	'MODULE_ALLOW_LIST'    => array('Home', 'Admin', 'Daili', 'User'),
	'DEFAULT_MODULE'       => '',
	'MODULE_DENY_LIST'     => array('Common'),
	"LOAD_EXT_FILE"        => "image,getinfo,addons,goods,form,createform", //扩展函数库
	'SHOW_PAGE_TRACE'      => true,
	/* 系统数据加密设置 */
	'DATA_AUTH_KEY'        => 'e_~FA3XBEu,ha[<WO7gqljG0/@z)!"*1T:JM#>^+', //默认数据加密KEY

	//'VAR_ADDON'    =>    'Plugin',
	//	'TMPL_EXCEPTION_FILE'   =>'./Public/error.tpl',// 异常页面的模板文件

	//缓存设置
	'DATA_CACHE_TIME'      => 3600, // 数据缓存有效期 0表示永久缓存
	'DATA_CACHE_COMPRESS'  => true, // 数据缓存是否压缩缓存
	'DATA_CACHE_CHECK'     => false, // 数据缓存是否校验缓存
	'DATA_CACHE_PREFIX'    => 'ank_', // 缓存前缀
	'DATA_CACHE_TYPE'      => 'File', // 数据缓存类型,支持:File|Db|Apc|Memcache|Shmop|Sqlite|Xcache|Apachenote|Eaccelerator
	'DATA_CACHE_PATH'      => TEMP_PATH, // 缓存路径设置 (仅对File方式缓存有效)
	'DATA_CACHE_SUBDIR'    => true, // 使用子目录缓存 (自动根据缓存标识的哈希创建子目录)
	'DATA_PATH_LEVEL'      => 5, // 子目录缓存级别

	//日志设置
	'LOG_RECORD'           => true, // 开启记录日志
	'LOG_TYPE'             => 'File', // 日志记录类型 默认为文件方式
	'LOG_LEVEL'            => 'EMERG,ALERT,CRIT,ERR', // 只记录EMERG ALERT CRIT ERR 错误
	'LOG_EXCEPTION_RECORD' => true, // 是否记录异常信息日志

	'SESSION_PREFIX'       => 'ank_', // session 前缀

	/* SESSION 和 COOKIE 配置 */
	'SESSION_PREFIX'       => 'anks_', //session前缀
	'COOKIE_PREFIX'        => 'ankc_', // Cookie前缀 避免冲突
	'TMPL_EXCEPTION_FILE'  => APP_PATH . 'sys_exception.tpl', // 异常页面的模板文件
	'TMPL_ACTION_ERROR'    => APP_PATH . 'sys_jump.tpl', // 默认错误跳转对应的模板文件
	'TMPL_ACTION_SUCCESS'  => APP_PATH . 'sys_jump.tpl', // 默认成功跳转对应的模板文件板文件
	/* 文件上传相关配置 */
	'FILE_UPLOAD'          => array(
		'maxSize'  => 2 * 1024 * 1024, //上传的文件大小限制 (0-不做限制)
		'exts'     => 'jpg,gif,png,jpeg', //允许上传的文件后缀
		'rootPath' => __ROOT__ . '/Uploads', //保存图片根路径
	),

	// 'DOWNLOAD_UPLOAD'      => array(
	// 	'mimes'    => '', //允许上传的文件MiMe类型
	// 	'maxSize'  => 50 * 1024 * 1024, //上传的文件大小限制 (0-不做限制)
	// 	'exts'     => 'jpg,gif,png,jpeg,bmp,zip,rar,tar,gz,7z,doc,docx,txt,xml', //允许上传的文件后缀
	// 	'autoSub'  => true, //自动子目录保存文件
	// 	'subName'  => array('date', 'Ymd'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
	// 	'rootPath' => __ROOT__ . '/Uploads/file/', //保存根路径
	// 	'savePath' => '', //保存路径
	// 	'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
	// 	'saveExt'  => '', //文件保存后缀，空则使用原后缀
	// 	'replace'  => false, //存在同名是否覆盖
	// 	'hash'     => true, //是否生成hash编码
	// 	'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
	// ),
	'TOKEN_ON'             => false, // 是否开启令牌验证 默认关闭
	'TOKEN_NAME'           => '__hash_vertify__', // 令牌验证的表单隐藏字段名称，默认为__hash__
	'TOKEN_TYPE'           => 'md5', //令牌哈希验证规则 默认为MD5
	'TOKEN_RESET'          => true, //令牌验证出错后是否重置令牌 默认为true
);
