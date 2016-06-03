<?php
defined("ACCESS_ROOT") || die("Invalid access");
// array(
// 	'field'   => 'nickname', //字段名
// 	'type'    => 'text',//表单类型(string,datetime,double,number,password,textarea,bigtextarea,color,bool,radio,select,checkbox,editor,batchpicture,picture,file,liandong,attribute,cutpicture)
// 	'name'    => 'nickname',//表单name值
// 	'title'   => '名字',//表单标题
// 	'note'    => '',//备注说明
// 	'extra'   => array(//表单输出(select radio等时当数组输出)
// 		1 => '男',
// 		2 => '女',
// 	),
// 	'value'   => '',//表单默认值
// 	'is_show' => 3,//1添加时显示 2编辑时显示 3任何时刻都显示
// ),
return array(
	'addmem' => array(
		array(
			'field'   => 'nickname',
			'type'    => 'text',
			'name'    => 'nickname',
			'title'   => '名字',
			'note'    => '',
			'extra'   => null,
			'value'   => '',
			'is_show' => 3,
		),
		array(
			'field'   => 'sex',
			'type'    => 'radio',
			'name'    => 'sex',
			'title'   => '性别',
			'note'    => '',
			'extra'   => array(
				1 => '男',
				2 => '女',
			),
			'value'   => 1,
			'is_show' => 3,
		),
		array(
			'field'   => 'mobile',
			'type'    => 'text',
			'name'    => 'mobile',
			'title'   => '手机号',
			'note'    => '',
			'extra'   => null,
			'is_show' => 3,
		),
		array(
			'field'   => 'email',
			'type'    => 'text',
			'name'    => 'email',
			'title'   => '邮箱',
			'note'    => '',
			'extra'   => null,
			'is_show' => 3,
		),
	),
);