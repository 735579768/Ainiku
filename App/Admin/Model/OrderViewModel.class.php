<?php
namespace Admin\Model;
use Think\Model\ViewModel;

class OrderViewModel extends ViewModel {
	public $viewFields = array(
		'Order'  => array('_as' => 'a', '*'),
		'Member' => array('_as' => 'b', '_on' => 'a.uid=b.member_id', 'username', 'account'),
		//'MemberGroup'=>array('_as'=>'b','title','admin_index', 'auth','_on'=>'a.member_group_id=b.member_group_id'),
	);
}