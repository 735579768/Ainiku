<?php
if($type==='add'){
return array(
	array(
		'field'=>'member_group_id',
		'name'=>'member_group_id',
		'type'=>'select',
		'title'=>'所属用户组',
		'note'=>'用户角色',
		'extra'=>getMemberGroupList(),
		'value'=>24,
		'is_show'=>1
	),
	array(
		'field'=>'username',
		'name'=>'username',
		'type'=>'string',
		'title'=>'用户名',
		'note'=>'',
		'extra'=>null,
		'is_show'=>1
	),
	array(
		'field'=>'password',
		'name'=>'password',
		'type'=>'password',
		'title'=>'密码',
		'note'=>'',
		'extra'=>null,
		'is_show'=>1
	),
	array(
		'field'=>'repassword',
		'name'=>'repassword',
		'type'=>'password',
		'title'=>'确认密码',
		'note'=>'',
		'extra'=>null,
		'is_show'=>1
	),
	array(
		'field'=>'nickname',
		'name'=>'nickname',
		'type'=>'string',
		'title'=>'昵称',
		'note'=>'',
		'extra'=>null,
		'is_show'=>1
	),
	array(
		'field'=>'email',
		'name'=>'email',
		'type'=>'string',
		'title'=>'邮箱',
		'note'=>'',
		'extra'=>null,
		'is_show'=>1
	),
	array(
		'field'=>'mobile',
		'name'=>'mobile',
		'type'=>'string',
		'title'=>'手机号',
		'note'=>'',
		'extra'=>null,
		'is_show'=>1
	),
	array(
		'field'=>'status',
		'name'=>'status',
		'type'=>'radio',
		'title'=>'会员状态',
		'note'=>'(禁用/启用)',
		'extra'=>array(
			0=>'禁用',
			1=>'正常'
			),
		'value'=>1,
		'is_show'=>1
		)
	);
}else{
return array(
	array(
		'field'=>'member_group_id',
		'name'=>'member_group_id',
		'type'=>'select',
		'title'=>'所属用户组',
		'note'=>'用户角色',
		'extra'=>getMemberGroupList(),
		'value'=>24,
		'is_show'=>1
	),
array(
		'field'=>'diqu',
		'name'=>'diqu',
		'type'=>'liandong',
		'title'=>'地区',
		'note'=>'',
		'extra'=>null,
		'is_show'=>1
),
array(
		'field'=>'nickname',
		'name'=>'nickname',
		'type'=>'string',
		'title'=>'昵称',
		'note'=>'',
		'extra'=>null,
		'is_show'=>1
	),
	array(
		'field'=>'email',
		'name'=>'email',
		'type'=>'string',
		'title'=>'邮箱',
		'note'=>'',
		'extra'=>null,
		'is_show'=>1
	),
	array(
		'field'=>'mobile',
		'name'=>'mobile',
		'type'=>'string',
		'title'=>'手机号',
		'note'=>'',
		'extra'=>null,
		'is_show'=>1
	),
	array(
		'field'=>'status',
		'name'=>'status',
		'type'=>'radio',
		'title'=>'会员状态',
		'note'=>'(禁用/启用)',
		'extra'=>array(
			0=>'禁用',
			1=>'正常'
			),
		'value'=>1,
		'is_show'=>1
		)
	);	
	}