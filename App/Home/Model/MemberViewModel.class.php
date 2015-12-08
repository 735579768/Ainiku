<?php
namespace Home\Model;
use Think\Model\ViewModel;

class MemberViewModel extends ViewModel {
	public $viewFields = array(
		'Member'      => array('_as' => 'a', '*'),
		'MemberGroup' => array('_as' => 'b', '_on' => 'a.member_group_id=b.member_group_id', 'title', 'auth', 'admin_index', 'noaccessurl', 'is_adminlogin'),
	);
}