<?php
if(!defined("ACCESS_ROOT"))die("Invalid access");
return array(
	    /* 数据库配置 */
    /* 数据库配置 */
    'DB_TYPE'   => 'mysqli', // 数据库类型
    'DB_HOST'   => '116.255.214.72', // 服务器地址
    'DB_NAME'   => 'ainikuqq', // 数据库名
    'DB_USER'   => 'root', // 用户名
    'DB_PWD'    => 'adminrootkl',  // 密码
    'DB_PORT'   => '3306', // 端口
    'DB_PREFIX' => 'kl_', // 数据库表前缀
	'COOKIE_DOMAIN'=>  '.ainikuqq.loc',      // Cookie有效域名
	'APP_SUB_DOMAIN_DEPLOY'   =>    1, // 开启子域名或者IP配置
    'APP_SUB_DOMAIN_RULES'    =>    array( 
       'ainikuqq.loc'  => 'Home',
	   'ainiku.com'  => 'Home', 
	   'www.ainiku.com'  => 'Home', 
	   'm.ainikuqq.loc'  => 'Mobile',
	   'm.ainiku.com'  => 'Mobile', 
	   'user.ainikuqq.loc'  => 'Admin',
	   'user.ainiku.com'  => 'Admin', 
    ),
);
