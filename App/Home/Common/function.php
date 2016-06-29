<?php
/**
 *取分类导航树
 */
function get_nav_tree($pid = 0, $child = false) {
	$rearr           = array();
	$where['status'] = 1;
	$where['pid']    = $pid;
	$list            = M('Nav')->where($where)->order('sort asc')->select();
	if ($list) {
		foreach ($list as $key => $val) {
			$child               = get_nav_tree($val['nav_id']);
			$list[$key]['child'] = $child;
		}
	}
	return $list;
}
/**
 *取系统缓存导航
 */
function F_get_nav_tree($pid = 0, $child = false) {
	$cachekey = md5('homenavtree');
	$navtree  = F($cachekey);
	if (empty($navtree) || APP_DEBUG) {
		$navtree = get_nav_tree($pid, $child);
		F($cachekey, $navtree);
	}
	return $navtree;
}
/**
 *取订单产品列表
 */
function F_get_order_goods_list($order_id = '') {
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
function get_category_list($category_id = 0) {
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
function get_category_article($category_id = '', $rows = '8') {
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

/**
 * 生成seo标题
 */
function get_seo_title($category, $single, $article) {
	$title          = C('WEB_SITE_TITLE');
	$category_title = '';
	$single_title   = '';
	$article_title  = '';
	if ($category) {
		if (empty($category['meta_title'])) {
			$category_title = $category['title'];
		} else {
			$category_title = $category['meta_title'];

		}
		$category_title .= '_';
	}
	if ($single) {
		if (empty($single['meta_title'])) {
			$single_title = $single['title'];
		} else {
			$single_title = $single['meta_title'];

		}
		$single_title .= '_';
	}
	if ($article) {
		if (empty($article['meta_title'])) {
			$article_title = $article['title'];
		} else {
			$article_title = $article['meta_title'];

		}
		$article_title .= '_';
	}
	return $single_title . $category_title . $article_title . $title;
}

/**
 * 生成seo关键字
 */
function get_seo_keywords($category, $single, $article) {
	$keywords          = C('WEB_SITE_KEYWORD');
	$category_keywords = '';
	$single_keywords   = '';
	$article_keywords  = '';
	if ($category) {
		if (empty($category['meta_keywords'])) {
			$category_keywords = $category['title'];
		} else {
			$category_keywords = $category['meta_keywords'];

		}
	}
	if ($single) {
		if (empty($single['meta_keywords'])) {
			$single_keywords = $single['title'];
		} else {
			$single_keywords = $single['meta_keywords'];

		}
	}
	if ($article) {
		if (empty($article['meta_keywords'])) {
			$article_keywords = $article['title'];
		} else {
			$article_keywords = $article['meta_keywords'];

		}
	}
	if (!empty($single_keywords) || !empty($category_keywords) || !empty($article_keywords)) {
		return $single_keywords . $category_keywords . $article_keywords;
	} else {
		return $keywords;
	}

}
/**
 * 生成seo描述
 */
function get_seo_descr($category, $single, $article) {
	$descr          = C('WEB_SITE_DESCRIPTION');
	$category_descr = '';
	$single_descr   = '';
	$article_descr  = '';
	if ($category) {
		if (empty($category['meta_descr'])) {
			$category_descr = $category['meta_descr'];

		}
	}
	if ($single) {
		if (empty($single['meta_descr'])) {
			$single_descr = $single['meta_descr'];

		}
	}
	if ($article) {
		if (empty($article['meta_descr'])) {
			$article_descr = removehtml($article['content'], 0, 100);
		} else {
			$article_descr = $article['meta_descr'];

		}
	}
	if (!empty($single_descr) || !empty($category_descr) || !empty($article_descr)) {
		return $single_descr . $category_descr . $article_descr;
	} else {
		return $descr;
	}

}