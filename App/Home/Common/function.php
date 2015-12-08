<?php
/**
 *取分类导航树
 */
function getNavTree($pid = 0, $child = false) {
	$rearr           = array();
	$where['status'] = 1;
	$where['pid']    = $pid;
	$list            = M('Nav')->where($where)->order('sort asc')->select();
	if ($list) {
		foreach ($list as $key => $val) {
			$child               = getNavTree($val['nav_id']);
			$list[$key]['child'] = $child;
		}
	}
	return $list;
}
/**
 *取系统缓存导航
 */
function F_getNavTree($pid = 0, $child = false) {
	$cachekey = md5('homenavtree');
	$navtree  = F($cachekey);
	if (empty($navtree) || APP_DEBUG) {
		$navtree = getNavTree($pid, $child);
		F($cachekey, $navtree);
	}
	return $navtree;
}
/**
 *取订单产品列表
 */
function F_getOrderGoodsList($order_id = '') {
	if (empty($order_id)) {
		return '';
	}

	$cachekey = 'getOrderGoodsList' . $order_id;
	$data     = F($cachekey);
	if (empty($data) || APP_DEBUG) {
		$data = D('OrderGoodsView')->where("order_id=$order_id")->select();
		F($cachekey, $data);
	}
	return $data;
}