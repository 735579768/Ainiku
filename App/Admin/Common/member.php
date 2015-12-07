<?php
/**
 *取节点数组
 */
function getNodeList() {
	$list = M('Node')->where('status=1 and pid=0')->order('name asc,node_id desc')->select();
	if (!empty($list)) {
		foreach ($list as $key => $val) {
			$list[$key]['_'] = M('Node')->where('status=1 and pid=' . $val['node_id'])->select();
		}
	}
	return $list;
}
/**
 *取节点数组
 */
function getAllMenuList() {
	$list = M('Menu')->where('status=1 and pid=0')->order('url asc,id desc')->select();
	if (!empty($list)) {
		foreach ($list as $key => $val) {
			$list[$key]['_'] = M('Menu')->where('status=1 and pid=' . $val['id'])->select();
		}
	}
	return $list;
}
function getNodeTree() {
	$rearr = array();
	$rearr[0] = '顶级节点';
	$map['status'] = 1;
	$map['pid'] = 0;
	$list = M('Node')->where($map)->select();
	if (!empty($list)) {
		foreach ($list as $val) {
			$rearr[$val['node_id']] = $val['title'];
			$map['pid'] = $val['node_id'];
			$list1 = M('Node')->where($map)->select();
			if (!empty($list1)) {
				foreach ($list1 as $v) {
					$rearr[$v['node_id']] = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;┗━━━" . $v['title'];
				}
			}
		}
	}
	return $rearr;
}
