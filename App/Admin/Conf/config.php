<?php
defined("ACCESS_ROOT") || die("Invalid access");
return array(
	//配置分组
	'DEFAULT_FILTER' => '',
	// 加载扩展配置文件
	'LOAD_EXT_CONFIG' => 'menu',
	'CONFIG_GROUP' => array(
		1 => '基本',
		4 => '网站',
		3 => '邮件',
		2 => '系统',
		5 => '支付',
	),
	'URL_ROUTER_ON' => true, //开启路由
	'URL_ROUTE_RULES' => array( //定义路由规则
		//后台多级目录映射
		//		//文档文件夹映射
		//		'Article/:data'=>'Document\Article/:1',
		//		'Single/:data'=>'Document\Single/:1',
		//		'Module/:data'=>'Document\Module/:1',
		//		'Modulepos/:data'=>'Document\Modulepos/:1',
	),
//	'CONTROLLER_LEVEL'      =>  2,
	'IS_DEV' => false, //开发模式
	'DEFAULT_FILTER' => '',
	//'配置项'=>'配置值'
	"LOAD_EXT_FILE" => "member", //扩展函数库
	'URL_MODEL' => 0, //URL模式
	//数据库备份根路径
	'DATA_BACKUP_PATH' => DATA_DIR_PATH . 'DataBak',
	//网站打包数据备份位置
	'WEBZIPDATA_BACKUP_PATH' => DATA_DIR_PATH . 'DataBak',
	//数据库备份卷大小
	'DATA_BACKUP_PART_SIZE' => 20971520,
	//数据库备份文件是否启用压缩0:不压缩1:启用压缩(压缩备份文件需要PHP环境支持gzopen,gzwrite函数)
	'DATA_BACKUP_COMPRESS' => 1,
	//数据库备份文件压缩级别(1:普通4:一般9:最高)(数据库备份文件的压缩级别，该配置在开启压缩时生效)
	'DATA_BACKUP_COMPRESS_LEVEL' => 9,
	'VAR_SESSION_ID' => 'session_id', //修复uploadify插件无法传递session_id的bug
	/* 模板相关配置 */
	'TMPL_PARSE_STRING' => array(
		'__STATIC__' => __ROOT__ . '/Public/Static',
		'__IMG__' => __ROOT__ . '/Public/' . MODULE_NAME . '/images',
		'__CSS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/css',
		'__JS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/js',
	),
	'SESSION_PREFIX' => 'anks_admin', //session前缀
	'COOKIE_PREFIX' => 'ankc_admin', // Cookie前缀 避免冲突

	'LANG_SWITCH_ON' => true, //开启语言包功能
	'LANG_AUTO_DETECT' => true, // 自动侦测语言
	'DEFAULT_LANG' => 'zh-cn', // 默认语言
	'LANG_LIST' => 'zh-cn', //必须写可允许的语言列表用','分隔
	'VAR_LANGUAGE' => 'l', // 默认语言切换变量
);
