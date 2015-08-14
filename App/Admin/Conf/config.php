<?php
if(!defined("ACCESS_ROOT"))die("Invalid access");
return array(
	//配置分组
	'DEFAULT_FILTER'        =>  '',
	'CONFIG_GROUP'=>array(
				1=>'基本',
				4=>'网站',
				3=>'邮件',
				2=>'系统',
				5=>'支付'
		),
	'URL_ROUTER_ON' 	=> true, //开启路由
	'URL_ROUTE_RULES'	=> array( //定义路由规则
		//后台多级目录映射
//		//文档文件夹映射
//		'Article/:data'=>'Document\Article/:1',
//		'Single/:data'=>'Document\Single/:1',
//		'Module/:data'=>'Document\Module/:1',
//		'Modulepos/:data'=>'Document\Modulepos/:1',
//		
//		//系统文件夹映射
//		//'Index/:data'=>'System/Index/:1',
//		
//		'Index/:data'=>'System\Index/:1',
//		'Menu/:data'=>'System\Menu/:1',
//		'Model/:data'=>'System\Model/:1',
//		'Modelattr/:data'=>'System\Modelattr/:1',
//		'Config/:data'=>'System\Config/:1',
//		'Hook/:data'=>'System\Hook/:1',
//		'Node/:data'=>'System\Node/:1',
//		'File/:data'=>'System\File/:1',
//		'Database/:data'=>'System\Database/:1',
//		'Addons/:data'=>'System\Addons/:1',
//		'Category/:data'=>'System\Category/:1',
//		'Notice/:data'=>'System\Notice/:1',
//		
//		//Other文件夹映射
//		'Comments/:data'=>'Other\Comments/:1',
//		'Nav/:data'=>'Other\Nav/:1',
//		'Other/:data'=>'Other\Other/:1',
//		'Region/:data'=>'Other\Region/:1',
//		'Link/:data'=>'Other\Link/:1',
//		
//		//Product文件夹映射
//		'Goods/:data'=>'Product\Goods/:1',
//		'Goodsattribute/:data'=>'Product\Goodsattribute/:1',
//		'Goodstypeattribute/:data'=>'Product\Goodstypeattribute/:1',
//		'Goodstype/:data'=>'Product\Goodstype/:1',
//		
//		//User文件夹映射
//		'Member/:data'=>'User\Member/:1',
//		'Membergroup/:data'=>'User\Membergroup/:1',
//		
//		//Common文件夹映射
//		'Public/:data'=>'Common\Public/:1',
		 ),
//	'CONTROLLER_LEVEL'      =>  2,
	'IS_DEV'=>false,//开发模式
	'DEFAULT_FILTER'        =>  '',
	//'配置项'=>'配置值'
	"LOAD_EXT_FILE"=>"member",//扩展函数库
	'URL_MODEL' => 0, //URL模式
    'FILE_UPLOAD' => array(
//		'thumb_width'=>100,这里的参数直接配置在后台啦
//		'thumb_height'=>100,
		'maxSize'  => 2*1024*1024, //上传的文件大小限制 (0-不做限制)
		'exts'     => 'jpg,gif,png,jpeg', //允许上传的文件后缀
		'rootPath' => '/Uploads', //保存图片根路径
    ),
    /* 文件上传相关配置 */
    'DOWNLOAD_UPLOAD' => array(
        'mimes'    => '', //允许上传的文件MiMe类型
        'maxSize'  => 50*1024*1024, //上传的文件大小限制 (0-不做限制)
        'exts'     => 'jpg,gif,png,jpeg,bmp,zip,rar,tar,gz,7z,doc,docx,txt,xml', //允许上传的文件后缀
        'autoSub'  => true, //自动子目录保存文件
        'subName'  => array('date', 'Ymd'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath' => './Uploads/file/', //保存根路径
        'savePath' => '', //保存路径
        'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt'  => '', //文件保存后缀，空则使用原后缀
        'replace'  => false, //存在同名是否覆盖
        'hash'     => true, //是否生成hash编码
        'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
    ),	
	//数据库备份根路径
	'DATA_BACKUP_PATH'=>'./DataBak/',
	//网站打包数据备份位置
	'WEBZIPDATA_BACKUP_PATH'=>'./DataBak/',
	//数据库备份卷大小
	'DATA_BACKUP_PART_SIZE'=>20971520,
	//数据库备份文件是否启用压缩0:不压缩1:启用压缩(压缩备份文件需要PHP环境支持gzopen,gzwrite函数)
	'DATA_BACKUP_COMPRESS'=>1,
	//数据库备份文件压缩级别(1:普通4:一般9:最高)(数据库备份文件的压缩级别，该配置在开启压缩时生效)
	'DATA_BACKUP_COMPRESS_LEVEL'=>9,
    'VAR_SESSION_ID' => 'session_id',	//修复uploadify插件无法传递session_id的bug
	    /* 模板相关配置 */
	'TMPL_PARSE_STRING' => array(
		'__STATIC__' => __ROOT__ . '/Public/Static',
		'__IMG__'    => __ROOT__ . '/Public/' . MODULE_NAME . '/images',
		'__CSS__'    => __ROOT__ . '/Public/' . MODULE_NAME . '/css',
		'__JS__'     => __ROOT__ . '/Public/' . MODULE_NAME . '/js',
	),
	    'SESSION_PREFIX' => 'anks_admin', //session前缀
    	'COOKIE_PREFIX'  => 'ankc_admin', // Cookie前缀 避免冲突
);
