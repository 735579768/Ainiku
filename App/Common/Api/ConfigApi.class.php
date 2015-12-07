<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace Common\Api;
defined("ACCESS_ROOT") || die("Invalid access");
class ConfigApi {
	/**
	 * 获取数据库中的配置列表
	 * @return array 配置数组
	 */
	public static function lists() {
		//  $map    = array('status' => 1);
		$map  = array();
		$data = M('Config')->where($map)->field('type,name,value')->select();

		$config = array();
		if ($data && is_array($data)) {
			foreach ($data as $value) {
				$config[$value['name']] = self::parse($value['type'], $value['value']);
			}
		}
		return $config;
	}

	/**
	 * 根据配置类型解析配置
	 * @param  integer $type  配置类型
	 * @param  string  $value 配置值
	 */
	private static function parse($type, $value) {
/*		//处理成数组
if(preg_match('/\w+?\:.+?[\r\n]/',$value)){
//if($type=='select' || $type=='radio' || $type=='checkbox'){
$array = preg_split('/[,;\r\n]+/', trim($value, ",;\r\n"));
if(strpos($value,':')){
$value  = array();
foreach ($array as $val) {
list($k, $v) = explode(':', $val);
$value[$k]   = $v;
}
}else{
$value =    $array;
}
//}
}*/
		//处理成布尔值
		switch ($value) {
		case 'true':$value = true;
			break;
		case 'false':$value = false;
			break;
		case '1':$value = true;
			break;
		case '0':$value = false;
			break;
		}
		//处理成数字
		if (gettype($value) === 'string') {
			if (preg_match('/^\d+\.\d+$/', $value)) {
				$value = doubleval($value);
			} else if (preg_match('/^\d+$/', $value)) {
				$value = intval($value);
			}
		}
		return $value;
	}
}