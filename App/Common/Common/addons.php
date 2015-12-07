<?php
defined('ADDONS_MENU') or define('ADDONS_MENU', 280);
defined('ADDONS_PATH') or define('ADDONS_PATH', './Plugins/');
defined('ADDONS_DIR_NAME') or define('ADDONS_DIR_NAME', 'Plugins');
/**
 * 返回一个目录中的目录列表(只返回一级)
 * @param string $path
 */
function getDirList($dir) {
	$dirArray[] = NULL;
	if (false != ($handle = opendir($dir))) {
		$i = 0;
		while (false !== ($file = readdir($handle))) {
			//去掉"“.”、“..”以及带“.xxx”后缀的文件
			if ($file != "." && $file != ".." && !strpos($file, ".")) {
				$dirArray[$i] = $file;
				$i++;
			}
		}
		//关闭句柄
		closedir($handle);
	}
	return $dirArray;
}
/**
 * 系统钩子函数
 * @param $name 钩子名字
 */
function hook($name, $param = array()) {
	$map['status'] = 1;
	$map['mark']   = $name;
	$map['status'] = 1;
	$rows          = M('Hooks')->field('id,pluginid')->where($map)->find();
	if (!empty($rows)) {
		$pluginidarr = explode(',', $rows['pluginid']);
	} else {
		//钩子不存在
		//throw new \Exception("钩子$name不存在");
		trace("钩子'{$name}'不存在或被禁用");
		return null;
	}
	//运行钩子上的插件列表
	$str = '';
	if (!empty($pluginidarr)) {
		foreach ($pluginidarr as $a) {
			if (empty($a)) {
				continue;
			}

			$model = M('addons')->where("id=$a")->find();
			if (!empty($model) && $model['status'] === '1') {
				$method = isset($param['method']) ? $param['method'] : 'run';
				$str .= runPluginMethod($model['mark'], $method, $param);
			} elseif ($model['status'] === 0) {
				trace("插件:[名字]{$model['name']},[标识]{$model['mark']} 被禁用");
			} elseif (!empty($model['id'])) {
				removePluginFromHook($rows['id'], $a);
				trace("插件id: $a 对应的插件不存在,已从钩子列表中移除");
			}
		}
	}
	return $str;
}
/*
 * 生成插件url地址
 * @param string $name Test/set 插件和操作
 * @param string $param 数组参数
 * */
function UP($name = null, $param = array()) {
	$a  = array();
	$ab = strpos($name, '?');
	if ($ab !== false) {
		$a = explode('/', substr($name, 0, $ab));
	} else {
		$a = explode('/', $name);
	}
	$data = array(
		'pn' => $a[0],
		'pm' => $a[1],
	);
	$data = array_merge($data, $param);
	//查找参数字符串
	preg_match('/\w+\/\w+\?(.*)/', $name, $out);
	if (!empty($out[1])) {
		$data = array_merge($data, parseParam($out[1]));
	}

	//C('URL_MODEL',0);
	return U('Addons/plugin', $data);
}
/**
 *参数转成数组
 */
function parseParam($str) {
	$arr   = explode('&', $str);
	$rearr = array();
	foreach ($arr as $val) {
		$ar                  = explode('=', $val);
		$rearr[trim($ar[0])] = trim($ar[1]);
	}
	return $rearr;
}
/*
 * 调用插件方法
 * @param string $name 插件名字
 * @param string $method 插件调用的方法
 * @param string $param一维数组传参数,返数组顺序传参
 * 返回方法的返回值
 */
function runPluginMethod($pn = null, $pm = null, $param = array()) {
	//包含插件目录
	require_once ADDONS_PATH . $pn . '/' . $pn . 'Plugin.class.php';
	$str    = "\\" . ADDONS_DIR_NAME . "\\$pn\\" . $pn . 'Plugin';
	$temobj = new $str();
	return call_user_func_array(array($temobj, $pm), $param);
}
function plugin($name, $param = array()) {
	try {
		//包含插件目录
		$narr = explode('/', $name);
		require_once ADDONS_PATH . $narr[0] . '/' . $narr[0] . 'Plugin.class.php';
		$str    = "\\" . ADDONS_DIR_NAME . "\\{$narr[0]}\\" . $narr[0] . 'Plugin';
		$temobj = new $str();
		return call_user_func_array(array($temobj, $narr[1]), array($param));
	} catch (Exception $e) {
		throw new \Think\Exception($e->getMessage());
	}
}
///**
// * 实例化模型类 格式 [资源://][模块/]模型
// * @param string $name 资源地址
// * @param string $layer 模型层名称
// * @return Think\Model
// */
//function PD($name='',$layer='') {
//    if(empty($name)) return new Think\Model;
//    static $_model  =   array();
//    $layer          =   $layer? : C('DEFAULT_M_LAYER');
//    if(isset($_model[$name.$layer]))
//        return $_model[$name.$layer];
//    $class          =   parse_res_name($name,$layer);
//    if(class_exists($class)) {
//        $model      =   new $class(basename($name));
//    }elseif(false === strpos($name,'/')){
//        // 自动加载公共模块下面的模型
//        if(!C('APP_USE_NAMESPACE')){
//            import('Common/'.$layer.'/'.$class);
//        }else{
//            $class      =   '\\Common\\'.$layer.'\\'.$name.$layer;
//        }
//        $model      =   class_exists($class)? new $class($name) : new Think\Model($name);
//    }else {
//        Think\Log::record('D方法实例化没找到模型类'.$class,Think\Log::NOTICE);
//        $model      =   new Think\Model(basename($name));
//    }
//    $_model[$name.$layer]  =  $model;
//    return $model;
//}
/*
 * 从钩子中移除一个插件
 * @param string $hid 钩子id
 * @param string $aid 插件id,支持多个用,分隔
 * 成功返回true，失败返回false
 * */
function removePluginFromHook($hid, $aid) {
	$model  = M('Hooks')->where("id=$hid")->field('pluginid')->find();
	$temarr = explode(',', $model['pluginid']);
	$result = array();
	foreach ($temarr as $a) {
		$str = stripos($aid, $a);
		if ($str === false) {
			$result[] = $a;
		}
	}
	$str = implode(',', $result);
	$res = M('Hooks')->where("id=$hid")->save(array('pluginid' => $str));
	if ($res) {
		return true;
	} else {
		return false;
	}
}
/*
 * 向钩子中添加一个插件
 * @param string $hid 钩子id
 * @param string $aid 插件id,支持多个用,分隔
 * 成功返回true，失败返回false
 * */
function addPluginToHook($hid, $aid) {
	$model = M('Hooks')->where("id=$hid")->field('pluginid')->find();
	$str1  = $model['pluginid'];
	$str   = '';
	if (empty($str1)) {
		$str = $aid;
	} else {
		$str = $str1 . ',' . $aid;
	}
	$res = M('Hooks')->where("id=$hid")->save(array('pluginid' => $str));
	if ($res) {
		return true;
	} else {
		return false;
	}
}
/**
 *实例化扩展模型
 *@param $pname 扩展名字
 *@param $model 模型名字
 */
function DP($model, $pname) {
	if (empty($model)) {
		return new Think\Model;
	}

	$model = '\Plugins\\' . $pname . '\\Model\\' . $model . 'Model';
	return new $model();
}
