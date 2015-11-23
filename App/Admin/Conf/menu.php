<?php
if(!defined("ACCESS_ROOT"))die("Invalid access");
return array(
	//后台菜单
	'ADMIN_CUSTOM_MENU'=>array(
				'默认'=>array(
					array('title'=>'系统首页','url'=>'Index/index'),
					array('title'=>'导航列表','url'=>'Nav/index'),
					array('title'=>'文章列表','url'=>'Article/index'),
					array('title'=>'产品列表','url'=>'Goods/index'),		
					array('title'=>'广告位列表','url'=>'Modulepos/index'),		
					array('title'=>'用户列表','url'=>'Member/index'),		
					array('title'=>'用户组列表','url'=>'Membergroup/index'),						
				),
				'系统分类'=>array(
					array('title'=>'文章分类','url'=>'Category/index?category_type=article'),
					array('title'=>'产品分类','url'=>'Category/index?category_type=goods'),							
				),
				
	),
);
