<?php
/* 根据ID获取分类名称 */
/* 根据ID获取分类名称 */
function getCategoryTitle($id) {
	$title = getCategory($id, 'title');
	return empty($title) ? '默认分类' : $title;
}
/**
 *取当前分类的所有子类树
 */
function getCategoryAllChild($id) {
	$restr = F('cateallchild' . $id);
	if (empty($restr) || APP_DEBUG) {
		$restr      = $id;
		$map['pid'] = $id;
		$result     = M('Category')->where($map)->select();
		if (!empty($result)) {
			foreach ($result as $key => $val) {
				$temid   = $val['category_id'];
				$result1 = M('Category')->where("pid=" . $temid)->select();
				if (!empty($result1)) {
					$temid = getCategoryAllChild($temid);
				}
				$restr .= ',' . $temid;

			}
		}
		F('cateallchild' . $id, $restr);
	}
	return $restr . '';
}

/**
 *取分类的顶级父类
 ***/
function getCategoryParent($id = null, $top = true) {
	if (empty($id)) {
		return '';
	}

	$catkey = sha1(json_encode($id) . json_encode($top));
	$reid   = F($catkey);
	if (empty($reid) || APP_DEBUG) {
		$info = M('Category')->find($id);
		if ($top) {
			if ($info['pid'] != 0) {
				$reid = getCategoryParent($info['pid'], true);
			}
			$reid = $info['category_id'];

		} else {
			$reid = $info['pid'];
		}
		F($catkey, $reid);
	}
	return $reid;
}
/**
 *取分类树
 */
function getCategoryTree($pid = 0, $child = false) {
	$cachekey     = md5('homecategorytree');
	$categorytree = F($cachekey);
	if (empty($categorytree) || APP_DEBUG) {
		$rearr           = array();
		$where['status'] = 1;
		$where['pid']    = $pid;
		$categorytree    = M('Category')->where($where)->order('sort asc')->select();
		if ($categorytree) {
			foreach ($categorytree as $key => $val) {
				$child                       = getCategoryTree($val['category_id']);
				$categorytree[$key]['child'] = $child;
			}
		}
		F($cachekey, $categorytree);
	}
	return $categorytree;
}
/**
 *取系统缓存分类
 */
function F_getCategoryTree($pid = 0, $child = false) {
	return getCategoryTree($pid, $child);
}
/*
// * 获取分类信息并缓存分类
// * @param  integer $id    分类ID
// * @param  string  $field 要获取的字段名
// * @return string         分类信息
 */
function getCategory($id = null, $field = null) {
	$map = array();
	/* 非法分类ID */
	if (empty($id)) {
		return '';
	}
	if (!is_numeric($id)) {
		$map['name'] = $id;
	} else {
		$map['category_id'] = $id;
	}
	/* 读取缓存数据 */
	$cate = F('sys_category_list' . $id);
	if (empty($cate) || APP_DEBUG) {
		$cate = M('Category')->where($map)->find();
		if (!$cate || 1 != $cate['status']) {
			//不存在分类，或分类被禁用
			return '';
		}
		F('sys_category_list' . $id, $cate); //更新缓存
	}
	return is_null($field) ? $cate : $cate[$field];
}
/**
 * 获取图片
 * @param int $id
 * @param string $field
 * @return 完整的数据  或者  指定的$field字段值
 * @author huajie <banhuajie@163.com>
 */
function getPicture($id = null, $field = null, $wh = null) {
	$revalue = '';
	$id      = trim($id);
	if (empty($id)) {
		$revalue = false;
	}
	if (is_numeric($id)) {
		$cakey = md5($id . '_' . $field . '_' . $wh);
		//$revalue=F('_picture/'.$cakey);
		$pkey    = '_picture/' . ($id % 100);
		$picarr  = F($pkey);
		$revalue = $picarr[$cakey];
		if (empty($revalue) || APP_DEBUG) {

			$picture = M('Picture')->where(array('status' => 1))->getById($id);
			if (!empty($field) && !empty($wh)) {
				$wharr = explode('_', $wh);

				if (count($wharr == 2)) {
					$revalue = str_replace('/Uploads/image/', IMAGE_CACHE_DIR, $picture['path']);
					$revalue = substr($revalue, 0, strrpos($revalue, '.')) . '_' . $wh . substr($revalue, strrpos($revalue, '.'));
					//判断之前是不是已经生成
					if (!file_exists(pathA($revalue))) {
						$result = img2thumb(pathA($picture['path']), pathA($revalue), $wharr[0], $wharr[1], true, false);
						if ($result !== true) {
							$revalue = $picture['path'];
						}
					}
				}
			} else if (!empty($field)) {
				$revalue = $picture[$field];
				if ($field == 'thumbpath') {
					if (!file_exists(pathA($revalue))) {
						$result = img2thumb(pathA($picture['path']), pathA($revalue), C('THUMB_WIDTH'), C('THUMB_HEIGHT'), true, false);
						if ($result !== true) {
							$revalue = $picture['path'];
						}
					}
				}
			} else {
				$revalue = $picture['path'];
			}

			$picarr[$cakey] = $revalue;
			F($pkey, $picarr);
		}
	} else {
		$revalue = $id;
	}
	return empty($revalue) ? '' : pathR($revalue);
}
/**
 * 获取附件
 * @param int $id
 * @param string $field
 * @return 完整的数据  或者  指定的$field字段值
 * @author huajie <banhuajie@163.com>
 */
function getFile($id = null, $field = null) {
	$revalue = null;
	$id      = trim($id);
	if (empty($id)) {
		$revalue = false;
	}
	if (is_numeric($id)) {
		$cakey   = $id . '_' . $field;
		$revalue = F('_file/' . $cakey);
		if (empty($revalue)) {
			$picture = M('File')->where(array('status' => 1))->getById($id);
			$revalue = empty($field) ? $picture['path'] : $picture[$field];
			F('_file/' . $cakey, $revalue);
		}
	} else {
		$revalue = $id;
	}
	return $revalue;
}
/*
 *取文章信息
 *
 **/
function getArticle($id = null, $whe = array(), $field = null) {
	if (empty($id) || !is_numeric($id)) {
		return '';
	}

	$info = S('Article' . $id);
	if (empty($info) || APP_DEBUG) {
		$info = M('Article')->where($whe)->find($id);
		if (empty($info)) {
			return null;
		}

		S('Article' . $id, $info);
	}
	return is_null($field) ? $info : $info[$field];
}
/*
 *取产品信息
 *
 **/
function getGoods($id = null, $whe = null, $field = null) {
	if (empty($id) || !is_numeric($id)) {
		return '';
	}

	$info = S('goods' . $id);
	if (empty($info) || APP_DEBUG) {
		$info = M('goods')->where($whe)->find($id);
		if (empty($info)) {
			return null;
		}

		$info2 = getGoodsAttribute($id);
		$info  = array_merge($info, $info2);
		S('goods' . $id, $info);
	}
	return is_null($field) ? $info : $info[$field];
}
/*
 *取会员信息
 *
 **/
function getMember($id = null, $field = null) {
	if (empty($id) || !is_numeric($id)) {
		return '';
	}

	$info = S('member' . $id);
	if (empty($info) || APP_DEBUG) {
		$info = M('Member')->find($id);
		S('member' . $id, $info);
	}
	return is_null($field) ? $info : $info[$field];
}
/*
 * 取用户组信息
 * */
function getMemberGroup($id = null, $field = null) {
	if (empty($id) || !is_numeric($id)) {
		return '';
	}

	$info = S('membergroup' . $id);
	if (empty($info) || APP_DEBUG) {
		$info = M('MemberGroup')->find($id);
		S('membergroup' . $id, $info);
	}
	return is_null($field) ? $info : $info[$field];
}
/*
 *返回用户分组列表
 */
function getMemberGroupList() {
	$membergroup = F('sys_membergroup_list');
	if (empty($membergroup)) {
		$temlist = D('MemberGroup')->select();
		foreach ($temlist as $val) {
			$membergroup[$val['member_group_id']] = $val['title'];
		}
		F('sys_membergroup_list', $membergroup);
	}
	return $membergroup;
}
/**
 *输出指定的文章列表
 */
function getArticleList($whe = null, $order = null) {
	$skey = $whe . $order;
	$list = S($skey);
	if (empty($list) || APP_DEBUG) {
		$map['_string'] = $whe;
		$list           = M('Article')->where($map)->order($order)->select();
		S($skey, $list);
	}
	return $list;
}
/**
 *万能查询函数
 */
function getField($table = null, $id = null, $field = null) {
	if (empty($table) || empty($id) || empty($field)) {
		return '';
	}

	$info = S($table . $id . $field);
	if (empty($fie) || APP_DEBUG) {
		$info = M($table)->field($field)->find($id);
		S($table . $id . $field, $info);
	}
	return empty($info) ? '' : (empty($field) ? $info : $info[$field]);
}
/*
// * 获取分类信息并缓存分类
// * @param  integer $id    分类ID
// * @param  string  $field 要获取的字段名
// * @return string         分类信息
 */
function getSingle($id = null, $field = null) {
	$map = array();
	/* 非法分类ID */
	if (empty($id)) {
		return '';
	}
	if (!is_numeric($id)) {
		$map['name'] = $id;
	} else {
		$map['single_id'] = $id;
	}
	/* 读取缓存数据 */
	$cate = S('sys_single_list' . $id);
	if (empty($list) || APP_DEBUG) {
		$cate = M('Single')->where($map)->find();
		if (!$cate || 1 != $cate['status']) {
			//不存在分类，或分类被禁用
			return '';
		}
		S('sys_single_list' . $id, $cate); //更新缓存
	}
	return is_null($field) ? $cate : $cate[$field];
}

/*
 *取属性所属的类型
 */
function getGoodsType($id = '', $field = '') {
	$rows = M('GoodsType')->find($id);
	return empty($field) ? $rows : $rows[$field];
}
/**
 *取产品类型列表
 */
function getGoodsTypeList() {
	$list = M('GoodsType')->where('status=1')->select();
	return $list;
}
/**
 *取产品类型属性列表
 */
function getGoodsTypeAttributeList($id = null) {
	if (empty($id)) {
		return null;
	}

	$list = M('GoodsTypeAttribute')->where('status=1 and goods_type_id=' . $id)->select();
	return $list;
}
/**
 *取一个产品的属性
 **/
function getGoodsAttribute($goods_id = null, $field = null) {
	$map['goods_id'] = $goods_id;
	$jon             = __DB_PREFIX__ . "goods_type_attribute as a on a.goods_type_attribute_id=" . __DB_PREFIX__ . "goods_attribute.goods_type_attribute_id";
	$fie             = "*,a.name as attrname,a.title as attrtitle," . __DB_PREFIX__ . "goods_attribute.value as attrvalue";
	$list            = M('GoodsAttribute')->join($jon)->field($fie)->where($map)->select();
	$rearr           = array();
	foreach ($list as $key => $val) {
		$rearr[$val['attrname']] = $val['attrvalue'];
	}
	return empty($field) ? $rearr : $rearr[$field];
}
/**
 *取模型信息
 */
function getModel($model_id = '', $field = '') {
	$map = array();
	/* 非法分类ID */
	$model_id = strtolower($model_id);
	if (empty($model_id)) {
		return '';
	}
	if (!is_numeric($model_id)) {
		$map['name'] = $model_id;
	} else {
		$map['model_id'] = $model_id;
	}
	/* 读取缓存数据 */
	$data = F('sys_model_list' . $model_id);
	if (empty($data) || APP_DEBUG) {
		$data = M('Model')->where($map)->find();
		if (!$data || 1 != $data['status']) {
			return '';
		}
		F('sys_model_list' . $model_id, $data); //更新缓存
	}
	return empty($field) ? $data : $data[$field];
}
/**
 * 通过模型属性来返回一个状态文本
 * @return [type] [description]
 */
function getStatus($val = '', $model_id = '', $field = 'status') {
	if (empty($val)) {
		return '';
	} else {
		$arr = getModelAttr($model_id, $field, 'extra');
		//trace($arr);
		return $arr[$val];
	}

}
/**
 *取表单模型数据数组
 *@param $model_id 模型id或标识
 *@param $field   模型的字段值
 *@param $attr 字段的属性值 如  title   note  name field  extra type......等  常用的有extra文章属性标记返回的是一个数组
 *
 **/
function getModelAttr($model_id = null, $field = null, $attr = null) {
	$skey   = $model_id . '_' . $field . '_' . $attr;
	$relist = F('_modelform/' . $skey);
	if (empty($relist) || APP_DEBUG) {
		$list = array();
		if (empty($model_id)) {
			return null;
		}

		$data = getModel($model_id);
		if (empty($data)) {
			return null;
		}

		$model_id = $data['model_id'];
		$list     = M('ModelAttr')->where("model_id=$model_id")->order('sort asc')->select();
		$refield  = null;
		foreach ($list as $key => $val) {
			if (!empty($val['extra'])) {
				if ($val['extranote'] === '1' || $val['extranote'] == 'func') {
					//支持传参数
					preg_match('/([a-zA-Z0-9_]+)(\=(.+))?/i', $val['extra'], $out);
					$func = $out[1];
					$para = isset($out[3]) ? $out[3] : '';
					$para = str_replace("'", '', $para);
					if (empty($para)) {
						$list[$key]['extra'] = $func();
					} else {
						$list[$key]['extra'] = call_user_func_array($func, explode(',', $para));
					}

				} else {
					//如果是数组格式就转化成数组
					$list[$key]['extra'] = extraToArray($val['extra']);
				}

			}
			if (!empty($field) and $val['field'] === $field) {
				if (empty($attr)) {
					$relist = $list[$key];
					F('_modelform/' . $skey, $relist);
					return $list[$key];
				} else {
					$relist = $list[$key][$attr];
					F('_modelform/' . $skey, $relist);
					return $list[$key][$attr];
				}

			}
		}
		$relist = $list;
		F('_modelform/' . $skey, $list);
	}
	return $relist;
}
/**
 *解析extra字符串数据
 */
function extraToArray($extra) {
	$extra = preg_replace(array('/\n/i', '/\s/i'), array(',', ''), $extra);
	$dest  = array();
	$tema  = explode(',', $extra);
	foreach ($tema as $val) {
		if (strpos($extra, ':') !== false) {
			$temb = explode(':', $val);
			if (count($temb) === 2) {
				$dest[trim($temb[0])] = trim($temb[1]);
			}

		} else {
			$dest[trim($val)] = trim($val);
		}
	}
	return $dest;
}
/**
 *取区域名字
 **/
function getRegion($id = null) {
	$idarr = explode(',', $id);
	$key   = md5('area' . json_encode($idarr));
	$data  = F($key);
	if (empty($data)) {
		foreach ($idarr as $val) {
			$info = M('Area')->find($val);
			$data .= $info['area_name'];
		}
		F($key, $data);
	}
	return $data;

}
/**
 *取ip的物理地址
 ***/
function getIpLocation($ip = "127.0.0.1") {
	if (file_exists(__SITE_ROOT__ . __ROOT__ . '/TP/Library/Org/Net/UTFWry.dat')) {
		$Ip   = new \Org\Net\IpLocation('UTFWry.dat'); // 实例化类 参数表示IP地址库文件
		$area = $Ip->getlocation($ip); // 获取某个IP地址所在的位置
		return $area['country'] . $area['area'];
	} else {
		$uri  = "http://www.ip138.com/ips138.asp?ip=$ip";
		$opts = array(
			'http' => array(
				'method'  => 'GET',
				'timeout' => 2,
			),
		);
		$context = stream_context_create($opts);
		$str     = @file_get_contents($uri, false, $context);
		$str     = iconv('gbk', 'utf-8', $str);
		preg_match('/本站主数据：(.*?)<\/li>/si', $str, $out);
		return preg_replace('/\s+/', '', $out[1]);
	}

}
/**
 *生成表单
 */
function getForm($field, $da = array()) {
	$form = new \Common\Controller\FormController($field, $da);
	return $form->getData();
}
/**
 *取自定义表单
 */
function getCustomForm($metch, $name, $data) {
	$form = new \Common\Controller\CustomFormController($metch, $name, $data);
	return $form->$metch();
}
/**
 * 返回一个动态自动验证的数组
 */
function getModelRules($model_id = '') {
	$relus = getModelAttr($model_id);
	$rearr = array();
	foreach ($relus as $key => $val) {
		//查找出必填项
		if ($val['is_require'] == '1') {
			$rearr[] = array($val['name'], 'require', "{$val['title']}不能为空!");
		}
	}
	return $rearr;
}