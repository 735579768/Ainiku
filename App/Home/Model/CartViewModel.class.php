<?php
namespace Home\Model;
use Think\Model\ViewModel;

class CartViewModel extends ViewModel {
	public $viewFields = array(
		'Cart'  => array('_as' => 'a', '*'),
		// 'MemberGroup'=>array('_as'=>'b','_on'=>'a.member_group_id=b.member_group_id','title','auth','admin_index','noaccessurl','is_adminlogin'),
		'Goods' => array('_as' => 'b', '_on' => 'a.goods_id=b.goods_id', 'title', 'pic', 'price'),
	);
}