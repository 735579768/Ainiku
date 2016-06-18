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
/**
 * 取指定分类列表
 */
function getCategoryList($category_id = 0) {
	$map['pid']    = $category_id;
	$map['status'] = 1;
	$list          = M('Category')->where($map)->limit($rows)->select();
	return $list;
}
/**
 * 取分类的最上级父类
 */
function get_category_parentid($category_id = 0) {
	$skey   = 'categoryparentid_' . $category_id;
	$redata = F($skey);
	if (empty($redata) || APP_DEBUG) {
		if (is_array($category_id)) {
			if ($category_id['pid'] == 0) {
				$redata = $category_id['category_id'];
			} else {
				$map['category_id'] = $category_id['pid'];
				$info               = M('Category')->where($map)->find();
				$redata             = get_category_parentid($info);
			}
		} else {
			$map['category_id'] = $category_id;
			$info               = M('Category')->where($map)->find();
			$redata             = get_category_parentid($info);
		}
		F($skey, $redata);
	}
	return $redata;
}

/**
 * 取指定分类文章
 */
function getCatetoryArticle($category_id = '', $rows = '8') {
	if (empty($category_id)) {return '';}
	$rearr              = array();
	$category_id        = get_category_allchild($category_id);
	$map['category_id'] = array('in', "$category_id");
	$map['status']      = 1;
	$skey               = 'homearticlelist' . $category_id;
	$rearr              = S($skey);
	if (empty($rearr) || APP_DEBUG) {
		$rearr = M('Article')->where($map)->order('article_id desc')->limit($rows)->select();
		S($skey, $rearr);
	}
	return $rearr;
}
/**
 * 取单页列表
 */
function get_single_list($singlename = '') {
	if (empty($singlename)) {
		return "";
	}
	$map['ptitle'] = $singlename;
	$list          = M('Single')->where($map)->field('title,name,ptitle')->order('sort asc')->select();
	return $list;
}