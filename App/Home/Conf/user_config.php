<?php
defined("ACCESS_ROOT") || die("Invalid access");
return array(
	//'VIEW_PATH'=>'View/',
	'DEFAULT_FILTER'  => 'htmlspecialchars',
	'DEFAULT_THEME'   => 'default',
	'URL_ROUTER_ON'   => true, //开启路由
	'URL_ROUTE_RULES' => array( //定义路由规则
		//'Article/:category_id'=>'Article/index',
		'cate/:cate'                => 'Article/index', //文章列表页
		//'cate/:cate'=>'Article/index',
		'/^cate\/(.+?)\/p\/(\d+)$/' => 'Article/index?cate=:1&p=:2',
		'Allcate'                   => array('Single/index', 'single_id=allcate'), //所有分类页
		'Sear/:position\d'          => 'Sear/index', //搜索列表页
		'detail/:article_id'        => 'Article/detail', //文章详情页

		'Goods/:goods_id\d'         => 'Goods/detail',
		'Single/:single_id'         => 'Single/index',
	),
);