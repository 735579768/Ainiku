<?php
defined('PLUGIN_PATH') or define('PLUGIN_PATH', './Plugin/');
/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 * @author 寞枫 <735579768@.qq.com>
 */
function is_login() {
	$user = session('user_auth');
	if (empty($user)) {
		return 0;
	} else {
		return session('user_auth_sign') == data_auth_sign($user) ? $user['uid'] : 0;
	}
}
/**
 *前台自动登陆判断
 */
function auto_login() {
	$user = cookie('token');
	if (empty($user)) {session('user_auth', null);return 0;}
	;
	$uid = is_login();
	if (!$uid) {
		$uid = autologin();
	}

	return $uid;
}
/**
 *前台用户自动登出
 */
function loginout() {
	session('[destroy]');
	cookie(null);
	// cookie('token',null,array('expire'=>time()+(-1000)));
}
/**
 *前台用户判断cookie自动登陆
 */
function autologin() {
	$user = cookie('token');
	if (empty($user)) {
		return 0;
	} else {
		$username        = ainiku_decrypt($user['u']);
		$password        = ainiku_ucenter_md5(ainiku_decrypt($user['p']));
		$map['uesrname'] = $username;
		$map['password'] = $password;
		$info            = M('Member')->where($map)->find();
		if (empty($info)) {
			return 0;
		} else {
			/* 记录登录SESSION和COOKIES */
			$auth = array(
				'uid'             => $info['member_id'],
				'username'        => $info['username'],
				'last_login_time' => $info['last_login_time'],
			);
			session('user_auth', $auth);
			session('uinfo', $info);
			session('user_auth_sign', data_auth_sign($auth));
			define('UID', $info['member_id']);

			$uid      = $info['member_id'];
			$ip       = get_client_ip();
			$location = getIpLocation($ip);
			$data     = array(
				'member_id'      => $uid,
				'update_time'    => NOW_TIME,
				'last_login_ip'  => $ip,
				'last_login_adr' => $location['country'] . $location['area'],
			);
			M('Member')->where("member_id=$uid")->setInc('login');
			M('Member')->save($data);
			//保存用户登陆日志
			M('MemberLog')->add(
				array(
					'member_id'   => $uid,
					'ip'          => $Ip,
					'adr'         => $location['country'] . $location['area'],
					'create_time' => NOW_TIME,
				)
			);
			return $uid;

		}
	}

}
/**
 * 检测验证码
 * @param  integer $id 验证码ID
 * @return boolean     检测结果
 * @author 寞枫 <735579768@.qq.com>
 */
function check_verify($code, $id = 1) {
	$verify = new \Think\Verify();
	return $verify->check($code, $id);
}
/**
 * 系统加密方法
 * @param string $data 要加密的字符串
 * @param string $key  加密密钥
 * @param int $expire  过期时间 单位 秒
 * @return string
 * @author 枫叶 <735579768@qq.com>
 */
function ainiku_encrypt($data, $key = 'adminrootkl', $expire = 0) {
	if ($data == '') {return '';}
	$key  = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
	$data = base64_encode($data);
	$x    = 0;
	$len  = strlen($data);
	$l    = strlen($key);
	$char = '';

	for ($i = 0; $i < $len; $i++) {
		if ($x == $l) {
			$x = 0;
		}

		$char .= substr($key, $x, 1);
		$x++;
	}

	$str = sprintf('%010d', $expire ? $expire + time() : 0);

	for ($i = 0; $i < $len; $i++) {
		$str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1))) % 256);
	}
	return str_replace(array('+', '/', '='), array('-', '_', ''), base64_encode($str));
}

/**
 * 系统解密方法
 * @param  string $data 要解密的字符串 （必须是ainiku_encrypt方法加密的字符串）
 * @param  string $key  加密密钥
 * @return string
 * @author 枫叶 <735579768@qq.com>
 */
function ainiku_decrypt($data, $key = 'adminrootkl') {
	if ($data == '') {return '';}
	$key  = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
	$data = str_replace(array('-', '_'), array('+', '/'), $data);
	$mod4 = strlen($data) % 4;
	if ($mod4) {
		$data .= substr('====', $mod4);
	}
	$data   = base64_decode($data);
	$expire = substr($data, 0, 10);
	$data   = substr($data, 10);

	if ($expire > 0 && $expire < time()) {
		return '';
	}
	$x    = 0;
	$len  = strlen($data);
	$l    = strlen($key);
	$char = $str = '';

	for ($i = 0; $i < $len; $i++) {
		if ($x == $l) {
			$x = 0;
		}

		$char .= substr($key, $x, 1);
		$x++;
	}

	for ($i = 0; $i < $len; $i++) {
		if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
			$str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
		} else {
			$str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
		}
	}
	return base64_decode($str);
}
/**
 *系统用户密码加密算法
 */
function ainiku_ucenter_md5($str, $key = '') {
	$key = $key ? C('DATA_AUTH_KEY') : 'adminrootkl';
	return '' === $str ? '' : md5(sha1($str) . $key);
}
/**
 * 数据签名认证
 * @param  array  $data 被认证的数据
 * @return string       签名
 * @author 寞枫 <735579768@qq.com>
 */
function data_auth_sign($data) {
	//数据类型检测
	if (!is_array($data)) {
		$data = (array) $data;
	}
	ksort($data); //排序
	$code = http_build_query($data); //url编码并生成query字符串
	$sign = sha1($code); //生成签名
	return $sign;
}
/**
 * 调用系统的API接口方法（静态方法）
 * api('User/getName','id=5'); 调用公共模块的User接口的getName方法
 * api('Admin/User/getName','id=5');  调用Admin模块的User接口
 * @param  string  $name 格式 [模块名]/接口名/方法名
 * @param  array|string  $vars 参数
 */
function api($name, $vars = array()) {
	$array     = explode('/', $name);
	$method    = array_pop($array);
	$classname = array_pop($array);
	$module    = $array ? array_pop($array) : 'Common';
	$callback  = $module . '\\Api\\' . $classname . 'Api::' . $method;
	if (is_string($vars)) {
		parse_str($vars, $vars);
	}
	return call_user_func_array($callback, $vars);
}
/**
 * 时间戳格式化
 * @param int $time
 * @return string 完整的时间显示
 * @author huajie <banhuajie@163.com>
 */
function time_format($time = NULL, $format = 'Y-m-d H:i:s') {
	if (preg_match('/\d{1,4}[^\d]+\d{1,2}[^\d]+\d{1,2}(\s+\d{1,2}[^\d]+\d{1,2}[^\d]+\d{1,2})?/', $time)) {
		return $time;
	} else {
		$time = $time === NULL ? NOW_TIME : intval($time);
		return date($format, $time);
	}
}
function time_format2($time = NULL, $format = 'Y-m-d H:i:s') {
	if ($time === NULL) {
		return '';
	} else {
		return date($format, intval($time));
	}

}

/*
 *功能：发送邮件
 *@param:$to     要发送的目标邮箱
 *@param:$$subject          邮件的主题
 *@param:$body              邮件的主体内容
 *@param:FromName           发送人的用户名(昵称)
 *@param:toname             接收人的用户名(昵称)
 *@param:extra_msg          附加信息(可为空)
 */

function sendMail($conf = array()) {
	//ini_set("memory_limit","100M");
	$to         = isset($conf['to']) ? $conf['to'] : '';
	$subject    = isset($conf['subject']) ? $conf['subject'] : '';
	$body       = isset($conf['body']) ? $conf['body'] : '';
	$fromname   = isset($conf['fromname']) ? $conf['fromname'] : '';
	$toname     = isset($conf['toname']) ? $conf['toname'] : '';
	$extra_msg  = isset($conf['extra_msg']) ? $conf['extra_msg'] : '';
	$host       = isset($conf['host']) ? $conf['host'] : '';
	$port       = isset($conf['port']) ? $conf['port'] : '';
	$uname      = isset($conf['username']) ? $conf['username'] : '';
	$pwd        = isset($conf['password']) ? $conf['password'] : '';
	$attachment = isset($conf['attachment']) ? $conf['attachment'] : '';
	$fromemail  = isset($conf['fromemail']) ? $conf['fromemail'] : 'service@ainiku.com'; //来源邮箱

	$host  = empty($host) ? C('MAIL_SMTP_HOST') : $host;
	$port  = empty($port) ? C('MAIL_SMTP_PORT') : $port;
	$uname = empty($uname) ? C('MAIL_SMTP_USER') : $uname;
	$pwd   = empty($pwd) ? C('MAIL_SMTP_PASS') : $pwd;

	$fromname = empty($fromname) ? $uname : $fromname;
	$body     = empty($body) ? C('MAIL_SMTP_CE') : $body;
	$body     = toutf8($body);

	if (empty($uname)) {
		return '收件人邮箱不能为空';
	}

	if (empty($host)) {
		return '主机址不能为空';
	}

	if (empty($body)) {
		return '邮件内容不能为空';
	}

	if (empty($pwd)) {
		return '密码不能为空';
	}

	import('Ainiku.PHPMailer');
	$mail = new PHPMailer;

	$mail->SMTPDebug = 0; // 关闭SMTP调试功能
	$mail->IsSMTP(); // send via SMTP
	$mail->Host     = $host; // SMTP servers
	$mail->SMTPAuth = true; // turn on SMTP
	$mail->Username = $uname; // SMTP username
	$mail->Password = $pwd; // SMTP password
	$mail->From     = $fromemail; // 发件人邮箱
	$mail->FromName = $fromname; // 发件人
	$mail->CharSet  = "utf-8"; // 这里指定字符集！
	$mail->Encoding = "base64";
	$mail->AddAddress($to, $toname); // 收件人邮箱和姓名
	$mail->IsHTML(true); // send as HTML
	//$mail->SMTPSecure = 'ssl';   // 使用安全协议用qq邮箱时要用
	$mail->Subject  = $subject; // 邮件主题
	$mail->Body     = $body;
	$mail->WordWrap = 50; // set word wrap 换行字数
	$mail->AltBody  = "text/html";
	$mail->AddReplyTo($fromemail, $fromname); //快捷回复
	if (!empty($attachment)) {
		$mail->AddAttachment($attachment);
	}
	//$mail->AddAttachment("sendmail.rar");     // attachment 附件
	//$mail->AddAttachment("psd.jpg", "psd.jpg");  //添加一个附件并且重命名

	if (!$mail->Send()) {
		return APP_DEBUG ? $mail->ErrorInfo : '邮件发送错误!';
	} else {
		return true;
	}
}
/**
 *删除系统上传的图片
 */
function delimage($id = null) {
	if (empty($id)) {
		return false;
	}

	//die(file_exists('/oiuou'));
	//图片id号删除
	if (preg_match('/\d+/', $id)) {
		//删除本地文件
		$map['id'] = $id;
		//if(!IS_ADMIN)$map['uid']=UID;
		$row = M('Picture')->find($id);

		if (!empty($row)) {
			//查找是不是有多个记录共用一个图片
			$row2 = M('Picture')->where("sha1='{$row['sha1']}'")->select();
			if (count($row2) < 2) {
				if (realpath('.' . $row['path'])) {
					unlink(realpath('.' . $row['path']));
				}

				if (realpath('.' . $row['thumbpath'])) {
					unlink(realpath('.' . $row['thumbpath']));
				}

			}
			$result = M('Picture')->delete($id);

			if (0 < $result) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
		//正则字符串中的图片路径
	} else if (file_exists('.' . $id)) {
		preg_match_all('/<img.*?src=[\'|\"]{1}(.*?)[\'|\"]{1}.*?>/', $id, $out, PREG_PATTERN_ORDER);
		foreach ($out[1] as $val) {
			$result = M('Picture')->where("uid=" . UID . "  and (path='$val' or thumbpath='$val')  ")->delete();
			if (file_exists('.' . $val)) {
				unlink('.' . $val);
			}

			//删除缩略图
			$thumbpath = str_replace('image', 'image/thumb', '.' . $val);
			if (file_exists($thumbpath)) {
				unlink($thumbpath);
			}

		}
		return true;

		//判断是不是文件路径

	} else {
		//删除数据库中的图片记录
		$result = M('Picture')->where("uid=" . UID . "  and (path='$id' or thumbpath='$id')  ")->delete();
		if (0 < $result) {unlink($id);}
		return true;
	}
//		}else{
	//			return false;
	//			}
}

/**
 *删除系统上传的附件/文件
 **/
function delfile($id = null) {
	if (empty($id)) {
		return false;
	}

	//删除本地文件
	$row = M('File')->find($id);
	if (!empty($row)) {
		$path = realpath('.' . $row['path']);
		if ($path) {
			unlink($path);
		}

		$result = M('File')->delete($id);
		if (0 < $result) {
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}
//生成不重复的订单号
function createorder() {
	return substr(date("YmdHis") . mt_rand(1000, 9999), 2);
}
/**
 * 得到新订单号
 * @return  string
 */
function createOrderSn() {
	/* 选择一个随机的方案 */
	while (true) {
		mt_srand((double) microtime() * 1000000);
		$ordernum = date('ymd') . str_pad(mt_rand(1000000, 9999999), 5, '0', STR_PAD_LEFT);
		//查询数据库中是不是有这个记录
		$info = M('Order')->where("order_sn=$ordernum")->find();
		if (empty($info)) {
			return $ordernum;
		}
	}
}
/**
 * 字符串截取，支持中文和其他编码
 * @static
 * @access public
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * @return string
 */
function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = true) {
	if (function_exists("mb_substr")) {
		$slice = mb_substr($str, $start, $length, $charset);
	} elseif (function_exists('iconv_substr')) {
		$slice = iconv_substr($str, $start, $length, $charset);
		if (false === $slice) {
			$slice = '';
		}
	} else {
		$re['utf-8']  = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
		$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
		$re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
		$re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
		preg_match_all($re[$charset], $str, $match);
		$slice = join("", array_slice($match[0], $start, $length));
	}
	return $suffix ? $slice . '...' : $slice;
}

/**
 *把字符串转成数组
 *@param $str 字符串
 *@param $charset 字符串编码
 */
function mbstringtoarray($str, $charset) {
	$strlen = mb_strlen($str);
	while ($strlen) {
		$array[] = mb_substr($str, 0, 1, $charset);
		$str     = mb_substr($str, 1, $strlen, $charset);
		$strlen  = mb_strlen($str);
	}
	return $array;
}

function removeHtml($str, $start = 0, $length, $charset = "utf-8", $suffix = true) {
	//$str=strip_tags($str);
	$str = preg_replace(array('/<.*?>/i', '/[\s|\t|\n|\r]+/i'), '', $str);
	return msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = true);
}
/**
 *过滤html标签
 *@param string
 */
function filter_html($str) {
	$panner = '/((&lt;)|<)(.*?)(>|(&gt;))/si';
	$str    = preg_replace($panner, '', $str);
	return $str;
}

/**
 *支持中文json编码
 */
function gbkjson_encode($str) {
	return urldecode(json_encode(url_encode($str)));
}
function url_encode($str) {
	if (is_array($str)) {
		foreach ($str as $key => $value) {
			$str[urlencode($key)] = url_encode($value);
		}
	} else {
		$str = urlencode($str);
	}

	return $str;
}

/**
 *检查目录是否可写
 */
function check_dir_iswritable($dir_path) {
	$dir_path   = str_replace('\\', '/', $dir_path);
	$is_writale = 1;
	if (!is_dir($dir_path)) {
		$is_writale = 0;
		return $is_writale;
	} else {
		$file_hd = fopen($dir_path . '/test.txt', 'w');
		if (!$file_hd) {
			$is_writale = 0;
			return $is_writale;
		} else {
			fclose($file_hd);
			unlink($dir_path . '/test.txt');
		}

		$dir_hd = opendir($dir_path);
		while (false !== ($file = readdir($dir_hd))) {
			if ($file != "." && $file != "..") {
				if (is_file($dir_path . '/' . $file)) {
					//文件不可写，直接返回
					if (!is_writable($dir_path . '/' . $file)) {
						return 0;
					}
				} else {
					$file_hd2 = fopen($dir_path . '/' . $file . '/test.txt', 'w');
					if (!$file_hd2) {

						$is_writale = 0;
						return $is_writale;
					} else {
						fclose($file_hd2);
						unlink($dir_path . '/' . $file . '/test.txt');
					}

					//递归
					$is_writale = check_dir_iswritable($dir_path . '/' . $file);
				}
			}
		}
	}
	return $is_writale;
}

/**
 *生成账号
 **/
function createAccount() {
	while (true) {
		$num            = rand(10000000, 99999999);
		$map['account'] = $num;
		$info           = M('Member')->where($map)->select();
		if (empty($info)) {
			return $num;
		}
	}
}
/**
 *判断账号是什么类型
 **/
function getAccountType($str) {
	if (preg_match('/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/i', $str)) {return 'email';}
	if (preg_match('/^1[34578]\d{9}$/', $str)) {return 'mobile';}
	return 'username';
}

/**
 *汉字转拼音
 **/
function Pinyin($_String, $_Code = 'UTF8') {
	//GBK页面可改为gb2312，其他随意填写为UTF8
	$_DataKey = "a|ai|an|ang|ao|ba|bai|ban|bang|bao|bei|ben|beng|bi|bian|biao|bie|bin|bing|bo|bu|ca|cai|can|cang|cao|ce|ceng|cha" . "|chai|chan|chang|chao|che|chen|cheng|chi|chong|chou|chu|chuai|chuan|chuang|chui|chun|chuo|ci|cong|cou|cu|" .
		"cuan|cui|cun|cuo|da|dai|dan|dang|dao|de|deng|di|dian|diao|die|ding|diu|dong|dou|du|duan|dui|dun|duo|e|en|er" .
		"|fa|fan|fang|fei|fen|feng|fo|fou|fu|ga|gai|gan|gang|gao|ge|gei|gen|geng|gong|gou|gu|gua|guai|guan|guang|gui" .
		"|gun|guo|ha|hai|han|hang|hao|he|hei|hen|heng|hong|hou|hu|hua|huai|huan|huang|hui|hun|huo|ji|jia|jian|jiang" .
		"|jiao|jie|jin|jing|jiong|jiu|ju|juan|jue|jun|ka|kai|kan|kang|kao|ke|ken|keng|kong|kou|ku|kua|kuai|kuan|kuang" .
		"|kui|kun|kuo|la|lai|lan|lang|lao|le|lei|leng|li|lia|lian|liang|liao|lie|lin|ling|liu|long|lou|lu|lv|luan|lue" .
		"|lun|luo|ma|mai|man|mang|mao|me|mei|men|meng|mi|mian|miao|mie|min|ming|miu|mo|mou|mu|na|nai|nan|nang|nao|ne" .
		"|nei|nen|neng|ni|nian|niang|niao|nie|nin|ning|niu|nong|nu|nv|nuan|nue|nuo|o|ou|pa|pai|pan|pang|pao|pei|pen" .
		"|peng|pi|pian|piao|pie|pin|ping|po|pu|qi|qia|qian|qiang|qiao|qie|qin|qing|qiong|qiu|qu|quan|que|qun|ran|rang" .
		"|rao|re|ren|reng|ri|rong|rou|ru|ruan|rui|run|ruo|sa|sai|san|sang|sao|se|sen|seng|sha|shai|shan|shang|shao|" .
		"she|shen|sheng|shi|shou|shu|shua|shuai|shuan|shuang|shui|shun|shuo|si|song|sou|su|suan|sui|sun|suo|ta|tai|" .
		"tan|tang|tao|te|teng|ti|tian|tiao|tie|ting|tong|tou|tu|tuan|tui|tun|tuo|wa|wai|wan|wang|wei|wen|weng|wo|wu" .
		"|xi|xia|xian|xiang|xiao|xie|xin|xing|xiong|xiu|xu|xuan|xue|xun|ya|yan|yang|yao|ye|yi|yin|ying|yo|yong|you" .
		"|yu|yuan|yue|yun|za|zai|zan|zang|zao|ze|zei|zen|zeng|zha|zhai|zhan|zhang|zhao|zhe|zhen|zheng|zhi|zhong|" .
		"zhou|zhu|zhua|zhuai|zhuan|zhuang|zhui|zhun|zhuo|zi|zong|zou|zu|zuan|zui|zun|zuo";
	$_DataValue = "-20319|-20317|-20304|-20295|-20292|-20283|-20265|-20257|-20242|-20230|-20051|-20036|-20032|-20026|-20002|-19990" .
		"|-19986|-19982|-19976|-19805|-19784|-19775|-19774|-19763|-19756|-19751|-19746|-19741|-19739|-19728|-19725" .
		"|-19715|-19540|-19531|-19525|-19515|-19500|-19484|-19479|-19467|-19289|-19288|-19281|-19275|-19270|-19263" .
		"|-19261|-19249|-19243|-19242|-19238|-19235|-19227|-19224|-19218|-19212|-19038|-19023|-19018|-19006|-19003" .
		"|-18996|-18977|-18961|-18952|-18783|-18774|-18773|-18763|-18756|-18741|-18735|-18731|-18722|-18710|-18697" .
		"|-18696|-18526|-18518|-18501|-18490|-18478|-18463|-18448|-18447|-18446|-18239|-18237|-18231|-18220|-18211" .
		"|-18201|-18184|-18183|-18181|-18012|-17997|-17988|-17970|-17964|-17961|-17950|-17947|-17931|-17928|-17922" .
		"|-17759|-17752|-17733|-17730|-17721|-17703|-17701|-17697|-17692|-17683|-17676|-17496|-17487|-17482|-17468" .
		"|-17454|-17433|-17427|-17417|-17202|-17185|-16983|-16970|-16942|-16915|-16733|-16708|-16706|-16689|-16664" .
		"|-16657|-16647|-16474|-16470|-16465|-16459|-16452|-16448|-16433|-16429|-16427|-16423|-16419|-16412|-16407" .
		"|-16403|-16401|-16393|-16220|-16216|-16212|-16205|-16202|-16187|-16180|-16171|-16169|-16158|-16155|-15959" .
		"|-15958|-15944|-15933|-15920|-15915|-15903|-15889|-15878|-15707|-15701|-15681|-15667|-15661|-15659|-15652" .
		"|-15640|-15631|-15625|-15454|-15448|-15436|-15435|-15419|-15416|-15408|-15394|-15385|-15377|-15375|-15369" .
		"|-15363|-15362|-15183|-15180|-15165|-15158|-15153|-15150|-15149|-15144|-15143|-15141|-15140|-15139|-15128" .
		"|-15121|-15119|-15117|-15110|-15109|-14941|-14937|-14933|-14930|-14929|-14928|-14926|-14922|-14921|-14914" .
		"|-14908|-14902|-14894|-14889|-14882|-14873|-14871|-14857|-14678|-14674|-14670|-14668|-14663|-14654|-14645" .
		"|-14630|-14594|-14429|-14407|-14399|-14384|-14379|-14368|-14355|-14353|-14345|-14170|-14159|-14151|-14149" .
		"|-14145|-14140|-14137|-14135|-14125|-14123|-14122|-14112|-14109|-14099|-14097|-14094|-14092|-14090|-14087" .
		"|-14083|-13917|-13914|-13910|-13907|-13906|-13905|-13896|-13894|-13878|-13870|-13859|-13847|-13831|-13658" .
		"|-13611|-13601|-13406|-13404|-13400|-13398|-13395|-13391|-13387|-13383|-13367|-13359|-13356|-13343|-13340" .
		"|-13329|-13326|-13318|-13147|-13138|-13120|-13107|-13096|-13095|-13091|-13076|-13068|-13063|-13060|-12888" .
		"|-12875|-12871|-12860|-12858|-12852|-12849|-12838|-12831|-12829|-12812|-12802|-12607|-12597|-12594|-12585" .
		"|-12556|-12359|-12346|-12320|-12300|-12120|-12099|-12089|-12074|-12067|-12058|-12039|-11867|-11861|-11847" .
		"|-11831|-11798|-11781|-11604|-11589|-11536|-11358|-11340|-11339|-11324|-11303|-11097|-11077|-11067|-11055" .
		"|-11052|-11045|-11041|-11038|-11024|-11020|-11019|-11018|-11014|-10838|-10832|-10815|-10800|-10790|-10780" .
		"|-10764|-10587|-10544|-10533|-10519|-10331|-10329|-10328|-10322|-10315|-10309|-10307|-10296|-10281|-10274" .
		"|-10270|-10262|-10260|-10256|-10254";
	$_TDataKey   = explode('|', $_DataKey);
	$_TDataValue = explode('|', $_DataValue);
	$_Data       = array_combine($_TDataKey, $_TDataValue);
	arsort($_Data);
	reset($_Data);
	if ($_Code != 'gb2312') {
		$_String = _U2_Utf8_Gb($_String);
	}

	$_Res = '';
	for ($i = 0; $i < strlen($_String); $i++) {
		$_P = ord(substr($_String, $i, 1));
		if ($_P > 160) {
			$_Q = ord(substr($_String, ++$i, 1));
			$_P = $_P * 256 + $_Q - 65536;
		}
		$_Res .= _Pinyin($_P, $_Data);
	}
	return preg_replace("/[^a-z0-9]*/", '', $_Res);
}
function _Pinyin($_Num, $_Data) {
	if ($_Num > 0 && $_Num < 160) {
		return chr($_Num);
	} elseif ($_Num < -20319 || $_Num > -10247) {
		return '';
	} else {
		foreach ($_Data as $k => $v) {
			if ($v <= $_Num) {
				break;
			}
		}
		return $k;
	}
}
function _U2_Utf8_Gb($_C) {
	$_String = '';
	if ($_C < 0x80) {
		$_String .= $_C;
	} elseif ($_C < 0x800) {
		$_String .= chr(0xC0 | $_C >> 6);
		$_String .= chr(0x80 | $_C & 0x3F);
	} elseif ($_C < 0x10000) {
		$_String .= chr(0xE0 | $_C >> 12);
		$_String .= chr(0x80 | $_C >> 6 & 0x3F);
		$_String .= chr(0x80 | $_C & 0x3F);
	} elseif ($_C < 0x200000) {
		$_String .= chr(0xF0 | $_C >> 18);
		$_String .= chr(0x80 | $_C >> 12 & 0x3F);
		$_String .= chr(0x80 | $_C >> 6 & 0x3F);
		$_String .= chr(0x80 | $_C & 0x3F);
	}
	return iconv('UTF-8', 'GB2312', $_String);
}

/** 删除所有空目录
 * @param String $path 目录路径
 */
function rm_empty_dir($path) {
	if (is_dir($path) && ($handle = opendir($path)) !== false) {
		while (($file = readdir($handle)) !== false) {
			// 遍历文件夹
			if ($file != '.' && $file != '..') {
				$curfile = $path . '/' . $file; // 当前目录
				if (is_dir($curfile)) {
					// 目录
					rm_empty_dir($curfile); // 如果是目录则继续遍历
					if (count(scandir($curfile)) == 2) { // 目录为空,=2是因为. 和 ..存在
						rmdir($curfile); // 删除空目录
					}
				}
			}
		}
		closedir($handle);
	}
}
/**
 *把字符串转成utf8如果本身就是utf8的话原样返回
 *
 **/
function is_utf8($str) {
	$len = strlen($str);
	for ($i = 0; $i < $len; $i++) {
		$c = ord($str[$i]);
		if ($c > 128) {
			if (($c > 247)) {
				return false;
			} elseif ($c > 239) {
				$bytes = 4;
			} elseif ($c > 223) {
				$bytes = 3;
			} elseif ($c > 191) {
				$bytes = 2;
			} else {
				return false;
			}

			if (($i + $bytes) > $len) {
				return false;
			}

			while ($bytes > 1) {
				$i++;
				$b = ord($str[$i]);
				if ($b < 128 || $b > 191) {
					return false;
				}

				$bytes--;
			}
		}
	}
	return true;
}
/**
 *把字符串转成utf
 *
 **/
function toUtf8($str = null) {
	if (is_utf8($str)) {
		return $str;
	} else {
		return iconv('gbk', 'utf-8', $str);
	}
}
/**
 *压缩css
 **/
function compress_css($path) {
	$dirname = dirname($path); //当前css文件的路径目录
	$ipath   = $path;
	$str     = '';
	if ($ipath) {
		$str = file_get_contents($ipath);
//把文件压缩
		$arr = array('/(\n|\t|\s)*/i', '/\n|\t|\s(\{|}|\,|\:|\;)/i', '/(\{|}|\,|\:|\;)\s/i');
		$str = preg_replace($arr, '$1', $str);
		$str = preg_replace('/(\/\*.*?\*\/\n?)/i', '', $str);

//查找出样式文件中的图片
		preg_match_all("/url\(\s*?[\'|\"]?(.*?)[\'|\"]?\)/", $str, $out);
		foreach ($out[1] as $v) {
			if (strpos($v, '../images') !== false) {
				$src_new = str_replace("../images", $dirname . "/images", $v); //源绝对路径
				$src_new = str_replace('css/', '', $src_new);
				$new     = str_replace("../images", STYLE_CACHE_DIR . MODULE_NAME . "/images", $v); //设置新路径
				$new     = __SITE_ROOT__ . __ROOT__ . str_replace('./', '/', $new);
				createFolder(dirname($new));
				if (file_exists($src_new)) { //判断是否存在
					copy($src_new, $new); //复制到新目录
				}
			}
		}
		$str = str_replace('../images', './images', $str);
	} else {
		die("path is not found:" . $ipath);
	}
	return $str;
}
/**
 *压缩JS文件并替换JS嵌套include文件
 */
function compress_js($jspath) {
//	import('Ainiku.JavaScriptPacker');
	//    $packer = new JavaScriptPacker($js, 'Normal', true, false);
	//    return $packer->pack();
	$js = file_get_contents($jspath);
	import('Ainiku.JSMin');
	return JSMin::minify($js);
}
/**
 *写字符串到文件
 */
function writetofile($filename, $str) {
	if (createFolder(dirname($filename))) {
		return file_put_contents($filename, $str);
	} else {
		//\Think\Log::write("mkdir err: ".$dirname($fpath));
		die($dirname($fpath));
	}

}
/**
 *创建文件夹
 */
function createFolder($path) {
	$path = pathA($path);
	if (!is_dir($path)) {
		return mkdir($path, 0777, true); //第三个参数为true即可以创建多极目录
	} else {
		return true;
	}
}
/**
 *判断是不是蜘蛛访问
 */
function get_naps_bot() {
	$spider    = 'googlebot,360spider,haosouspider,baiduspider,msnbot,slurp,sosospider,sogou spider,yodaobot';
	$restr     = false;
	$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
	if (strpos($useragent, 'googlebot') !== false) {
		$restr = 'google';
	}
	if (strpos($useragent, '360Spider') !== false || strpos($useragent, 'haosouspider') !== false) {
		$restr = '360';
	}
	if (strpos($useragent, 'baiduspider') !== false) {
		$restr = 'baidu';
	}
	if (strpos($useragent, 'msnbot') !== false) {
		$restr = 'bing';
	}
	if (strpos($useragent, 'slurp') !== false) {
		$restr = 'yahoo';
	}
	if (strpos($useragent, 'sosospider') !== false) {
		$restr = 'soso';
	}
	if (strpos($useragent, 'sogou spider') !== false) {
		$restr = 'sogou';
	}
	if (strpos($useragent, 'yodaobot') !== false) {
		$restr = 'yodao';
	}
	return $restr;

}
/**
 *把路径格式化为本地文件的绝对路径
 */
function pathA($path) {
	$path = str_replace(array('\\', './', __SITE_ROOT__ . __ROOT__, __SITE_ROOT__, __ROOT__), array('/', '/', '', '', ''), $path);
	return __SITE_ROOT__ . __ROOT__ . $path;
}
/**
 *把路径格式化为相对于网站根目录的路径
 */
function pathR($path) {
	$path = str_replace(array('\\', './', __SITE_ROOT__ . __ROOT__, __SITE_ROOT__, __ROOT__), array('/', '/', '', '', ''), $path);
	return __ROOT__ . $path;
}
/**
 *判断文件是否已经被修改
 */
function file_ismod($filepath) {
	$filearr = array();
	if (is_array($filepath)) {
		$filearr = $filepath;
	} else {
		$filearr[] = $filepath;
	}
	foreach ($filearr as $val) {
		$sval    = pathA($val);
		$modtime = date('Y-m-d h:i:s', filemtime($sval));
		if ($modtime) {
			$path     = str_replace(array('/', '\\'), array('_'), $val);
			$modstime = F('_modfile/' . $path);
			if ($modtime !== $modstime) {
				F('_modfile/' . $path, $modtime);
				return true;
			}
		}
	}
	return false;

}

/**
 *更新缓存配置
 */
function updateConfig() {
	//重新添加配置
	$config = F('DB_CONFIG_DATA');
	$config = api('Config/lists');
	F('DB_CONFIG_DATA', $config);
}
/**
 * 发送post请求
 * @param string $url 请求地址
 * @param array $post_data post键值对数据
 * @return string
 */
function http_post($url, $post_data) {
	$postdata = http_build_query($post_data);
	$options  = array(
		'http' => array(
			'method'  => 'POST',
			'header'  => 'Content-type:application/x-www-form-urlencoded',
			'content' => $postdata,
			'timeout' => 15 * 60, // 超时时间（单位:s）
		),
	);
	$context = stream_context_create($options);
	$result  = file_get_contents($url, false, $context);
	return $result;
}