<?php
if(!defined("ACCESS_ROOT"))die("Invalid access");
return array(
	    /* 数据库配置 */
    /* 数据库配置 */
    'DB_TYPE'   => 'mysqli', // 数据库类型
    'DB_HOST'   => 'localhost', // 服务器地址
    'DB_NAME'   => 'ainiku', // 数据库名
    'DB_USER'   => 'root', // 用户名
    'DB_PWD'    => 'adminrootkl',  // 密码
    'DB_PORT'   => '3306', // 端口
    'DB_PREFIX' => 'kl_', // 数据库表前缀
	//'COOKIE_DOMAIN'=>  '.ainiku.loc',      // Cookie有效域名
	'APP_SUB_DOMAIN_DEPLOY'   =>    1, // 开启子域名或者IP配置
    'APP_SUB_DOMAIN_RULES'    =>    array( 
       'ainiku.loc'  => 'Home',
	   'ainiku.com'  => 'Home', 
	   'www.ainiku.com'  => 'Home', 
	   'm.ainiku.loc'  => 'Mobile',
	   'm.ainiku.com'  => 'Mobile', 
	   'user.ainiku.loc'  => 'Admin',
	   'user.ainiku.com'  => 'Admin', 
    ),
);
