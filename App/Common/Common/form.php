<?php
//表单列表缓存函数库

/**
 *取模型列表
 */
function get_model_list() {
	$rearr = array();
	$list  = M('Model')->where('status=1')->select();
	foreach ($list as $val) {
		$rearr[$val['model_id']] = $val['title'];
	}
	return $rearr;
}
/**返回几个空白字符串***/
function get_space($num) {
	$str = '';
	for ($i = 0; $i < $num; $i++) {
		$str .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	}
	$str .= '┗━━━';
	return $str;
}
/**
 *取后台菜单列表缓存
 */
function F_get_menu_list($first = true) {
	$menulist = F('sys_menu_tree');
	if (empty($menulist)) {
		$menulist = get_menu_list();
		F('sys_menu_tree', $menulist);
	}
	if (!$first) {
		unset($menulist[0]);
	}

	return $menulist;
}
/**
 *取后台菜单列表
 */
$menu_lev = 0;
function get_menu_list($pid = 0, $child = false) {
	global $menu_lev;
	$menu_lev++;
	$rearr = array();
	if (!$child and empty($rearr)) {
		$rearr[0] = '顶级菜单';
	}

	$where['status'] = 1;
	$where['pid']    = $pid;
	//if(!APP_DEBUG)$where['no_del']=0;
	$list = M('menu')->where($where)->order('sort asc,`group` desc')->select();
	if ($list) {
		foreach ($list as $val) {
			if ($child) {

				$rearr[$val['id']] = get_space($menu_lev) . $val['title'] . "->({$val['url']})";
			} else {
				$rearr[$val['id']] = $val['title'] . "->({$val['url']})";
			}
			$temarr = get_menu_list($val['id'], true);
			foreach ($temarr as $key => $v) {$rearr[$key] = $v;}
		}
	}
	$menu_lev--;
	return $rearr;
}
/**
 *取分类导航树缓存
 */
function F_get_nav_list() {
	$navlist = F('sys_nav_tree');
	if (empty($catelist)) {
		$navlist = get_nav_list();
		F('sys_nav_tree', $navlist);
	}
	return $navlist;
}
/**
 *取分类导航树
 */
function get_nav_list($pid = 0, $child = false) {
	global $menu_lev;
	$menu_lev++;
	$rearr = array();
	if (!$child and empty($rearr)) {
		$rearr[0] = '顶级导航';
	}

	$where['status'] = 1;
	$where['pid']    = $pid;
	$list            = M('Nav')->where($where)->order('sort asc')->select();
	if ($list) {
		foreach ($list as $val) {
			if ($child) {
				$rearr[$val['nav_id']] = get_space($menu_lev) . $val['title'];
			} else {
				$rearr[$val['nav_id']] = $val['title'];
			}
			$temarr = get_nav_list($val['nav_id'], true);
			foreach ($temarr as $key => $v) {$rearr[$key] = $v;}
		}
	}
	$menu_lev--;
	return $rearr;
}
/**
 *取模块位置置列表带缓存
 */
function F_get_modulepos_list() {
	$menulist = F('sys_modulepos_tree');
	if (empty($menulist) || APP_DEBUG) {
		$menulist = get_modulepos_list();
		F('sys_modulepos_tree', $menulist);
	}
	return $menulist;
}
/**
 *取模块位置置列表
 */
function get_modulepos_list() {
	$rows     = M('modulepos')->select();
	$rearr[1] = '默认位置';
	foreach ($rows as $val) {
		$rearr[$val['modulepos_id']] = $val['title'];
	}
	return $rearr;
}
/**
 *取模块位置置标题
 */
function get_modulepos_title($posid = null) {
	if (empty($posid)) {
		return '';
	}

	$rows = M('modulepos')->find($posid);
	return $rows['title'];
}
function F_get_goods_catelist($first = true) {
//	$catelist=F(md5('sys_category_goods_tree'));
	//	if(empty($catelist)){
	//		$catelist=get_cate_list(0,false,'goods');
	//		F(md5('sys_category_goods_tree'),$catelist);
	//	}
	//	if(!$first)unset($catelist[0]);
	//	return $catelist;

	return F_get_cate_list(true, 'goods');
}

function F_get_cate_list($first = true, $type = null) {
	$catetype      = I('category_type');
	$category_type = empty($type) ? (empty($catetype) ? 'article' : $catetype) : $type;
	$catelist      = F('sys_category_' . $category_type . '_tree');
	if (empty($catelist) || APP_DEBUG) {
		$catelist = get_cate_list(0, false, $category_type);
		F('sys_category_' . $category_type . '_tree', $catelist);
	}
	if (!$first) {
		unset($catelist[0]);
	}

	return $catelist;
}
function get_cate_list($pid = 0, $child = false, $type = 'article') {
	global $menu_lev;
	$menu_lev++;
	$rearr = array();
	if (!$child and empty($rearr)) {
		$rearr[0] = '请选择----';
	}

	$where['status']        = 1;
	$where['pid']           = $pid;
	$where['category_type'] = $type;
	//if(!APP_DEBUG)$where['dev_show']=0;
	$list = M('category')->where($where)->order('sort asc')->select();
	if ($list) {
		foreach ($list as $val) {
			if ($child) {
				$rearr[$val['category_id']] = get_space($menu_lev) . $val['title'];
			} else {
				$rearr[$val['category_id']] = $val['title'];
			}
			$temarr = get_cate_list($val['category_id'], true, $type);
			foreach ($temarr as $key => $v) {$rearr[$key] = $v;}
		}
	}
	$menu_lev--;
	return $rearr;
}