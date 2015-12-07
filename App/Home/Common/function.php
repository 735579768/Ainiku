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
	if (empty($navtree)) {
		$navtree = getNavTree($pid, $child);
		F($cachekey, $navtree);
	}
	return $navtree;
}