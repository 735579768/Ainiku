<?php
return array(
	//'配置项'=>'配置值'
	    /* 模板相关配置 */
	'TMPL_PARSE_STRING' => array(
		'__STATIC__' => __ROOT__ . '/Public/Static',
		'__IMG__'    => __ROOT__ . '/Public/' . MODULE_NAME . '/images',
		'__CSS__'    => __ROOT__ . '/Public/' . MODULE_NAME . '/css',
		'__JS__'     => __ROOT__ . '/Public/' . MODULE_NAME . '/js',
	),
	'SESSION_PREFIX' => 'anks_daili', //session前缀
	'COOKIE_PREFIX'  => 'ankc_daili', // Cookie前缀 避免冲突
		
	'LANG_SWITCH_ON'     =>  true,    //开启语言包功能        
	'LANG_AUTO_DETECT'   =>  true, // 自动侦测语言
	'DEFAULT_LANG'       => 'zh-cn', // 默认语言        
	'LANG_LIST'          => 'zh-cn', //必须写可允许的语言列表用','分隔
	'VAR_LANGUAGE'       => 'l', // 默认语言切换变量
	'ALLOW_GROUP'		=>2,//允许登陆的会员组
	'ADMIN_LOGIN_TITLE'		=>'会员管理',
	//后台菜单
	'ADMIN_MENU'=>array(
				'默认'=>array(
					array('title'=>'会员列表','url'=>'Index/index'),
					array('title'=>'产品列表','url'=>'Index/index'),						
				),
				'会员列表'=>array(
					array('title'=>'会员列表','url'=>'Index/index'),
					array('title'=>'产品列表','url'=>'Index/index'),						
				),
				'产品列表'=>array(
					array('title'=>'会员列表','url'=>'Index/index'),
					array('title'=>'产品列表','url'=>'Index/index'),						
				),
				
	),
);