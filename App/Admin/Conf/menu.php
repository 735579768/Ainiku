<?php
if(!defined("ACCESS_ROOT"))die("Invalid access");
return array(
	//后台菜单
	'ADMIN_CUSTOM_MENU'=>array(
				'默认'=>array(
					array('title'=>'文章列表','url'=>'Article/index'),
					array('title'=>'产品列表','url'=>'Goods/index'),						
				),
				'会员列表'=>array(
					array('title'=>'文章列表','url'=>'Article/index'),
					array('title'=>'产品列表','url'=>'Goods/index'),							
				),
				'产品列表'=>array(
					array('title'=>'文章列表','url'=>'Article/index'),
					array('title'=>'产品列表','url'=>'Goods/index'),							
				),
				
	),
);
