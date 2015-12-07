<?php
namespace Admin\Model;
use Think\Model\ViewModel;

class OrderGoodsViewModel extends ViewModel {
	public $viewFields = array(
		'OrderGoods' => array('_as' => 'a', '*', 'a.goods_id' => 'goods_id'),
		'Goods'      => array('_as' => 'b', '_on' => 'a.goods_id=b.goods_id', 'title', 'pic'),
		//'MemberGroup'=>array('_as'=>'b','title','admin_index', 'auth','_on'=>'a.member_group_id=b.member_group_id'),
	);
}