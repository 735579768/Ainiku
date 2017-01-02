<?php
namespace Plugins\Qlogin;

// require_once ADDONS_PATH . 'Plugin.class.php';
class QloginPlugin extends \Plugins\Plugin {
	protected $config = array(
		'version' => '1.0',
		'author'  => 'qiaokeli',
		'name'    => 'QQ登陆',
		'descr'   => 'QQ互联',
	);
	public function __construct() {
		parent::__construct();
		$data = $this->getParam();
		define('Q_APPID', $data['appid']);
		define('Q_APPKEY', $data['appkey']);
	}
	function run() {
		$this->display('content');
	}
	function qcon() {
		$data    = $this->getParam();
		$callurl = $data['callback'] . '/' . UP('Qlogin/qcallfunc');
		define('Q_CALLBACK', $callurl);
		Vendor('QQLogin.API.qqlogin');
		die();
	}
	/**
	 * 取扩展参数
	 * @return [type] [description]
	 */
	private function getParam() {
		$data = M('Addons')->field('param')->where("mark='Qlogin'")->find();
		return json_decode($data['param'], true);
	}
	function qcallfunc() {
		$data = $this->getParam();
		define('Q_CALLBACK', $data['callback'] . UP('Qlogin/qcallfunc'));
		Vendor('QQLogin.API.qqcallfunc');
		if (session('openid') != '') {
			$uid = $this->QQregister(session('openid'));
			if ($uid) {
				$uid = $this->login(session('openid'), '', 5);
				if (0 < $uid) {
					//UC登录成功
					//redirect(U('/'));
					echo '<script>window.opener.qqlogin.success();window.close();</script>';
					die();
				}
			} else {
				die('error');
			}

		} else {
			die('error');
		}
	}
	public function QQregister($openid) {
		$qqinfo                  = session('qqinfo');
		$data['openid']          = $openid;
		$data['member_group_id'] = 2;
		//$data['account']         = create_account();
		$data['nickname'] = $qqinfo['nickname'];

		$mem = M('Member');
		$re  = $mem->where("openid='$openid'")->find();
		if (!empty($re)) {
			$data['member_id'] = $re['member_id'];
			if ($mem->create($data)) {
				//保存或更新qq的信息到数据库
				$mem->save();
			}
			//已经是登陆用户
			return $re['member_id'];
		} else {
			/* 添加用户 */
			$data['last_login_ip'] = get_client_ip();
			$data['account']       = create_account();
			$data['username']      = $data['account'];
			//生成用户名
			$uid = $mem->add($data);
			return $uid ? $uid : 0; //0-未知错误，大于0-注册成功
		}

	}
	/**
	 * 用户登录认证
	 * @param  string  $username 用户名
	 * @param  string  $password 用户密码
	 * @param  integer $type     用户名类型 （1-用户名，2-邮箱，3-手机，4-UID）
	 * @return integer           登录成功-用户ID，登录失败-错误编号
	 */
	private function login($username, $password, $type = 1) {
		$map = array();
		switch ($type) {
		case 1:
			$map['username'] = $username;
			break;
		case 2:
			$map['email'] = $username;
			break;
		case 3:
			$map['mobile'] = $username;
			break;
		case 4:
			$map['id'] = $username;
			break;
		case 5:
			$map['openid'] = $username;
			break;
		default:
			return 0; //参数错误
		}

		/* 获取用户数据 */
		$user = M('Member')->where($map)->find();
		if (!empty($user) && $user['status'] == '1') {
			/* 验证用户密码 */
			/* 记录登录SESSION和COOKIES */
			$auth = array(
				'uid'             => $user['member_id'],
				'username'        => $user['username'],
				'last_login_time' => $user['last_login_time'],
			);

			session('user_auth', $auth);
			session('uinfo', $user);
			session('user_auth_sign', data_auth_sign($auth));

/*			var_dump($_SESSION);
die();*/
			$this->updateLogin($user['member_id']); //更新用户登录信息
			return $user['member_id']; //登录成功，返回用户ID
		} else {
			return '用户不存在或被禁用';
		}
	}
	/**
	 * 更新用户登录信息
	 * @param  integer $uid 用户ID
	 */
	protected function updateLogin($uid) {
		$data = array(
			'member_id'       => $uid,
			'last_login_time' => NOW_TIME,
			'last_login_ip'   => get_client_ip(),
			'create_time'     => NOW_TIME,
			'update_time'     => NOW_TIME,
		);
		D('Member')->save($data);
	}
	public function getConfig() {
		return $this->config;
	}
	public function install() {
		//向后台添加菜单，如果不添加的话直接返回真
		$data = array(
			'title' => 'QQ登陆', //插件后台菜单名字
			'pid'   => ADDONS_MENU, //不用改变
			'url'   => 'Addons/plugin?pn=Qlogin&pm=set', //填写后台菜单url名称和方法
			'group' => '已装插件', //不用改变
			'type'  => 'Qlogin', //填写自己的插件名字
		);
		//添加到数据库
		if (M('Menu')->add($data)) {
			return true;
		} else {
			return false;
		}
	}
	public function uninstall() {
		//删除后台添加的菜单，如果没有直接返回真
		$map['type'] = 'Qlogin';
		if (M('Menu')->where($map)->delete()) {
			return true;
		} else {
			return false;
		}
	}
	public function set() {
		//插件工菜单后台设置,没有的话直接返回真
		if (IS_POST) {
			$data = array(
				'update_time' => NOW_TIME,
				'appid'       => I('post.appid'),
				'appkey'      => I('post.appkey'),
				'callback'    => I('post.callback'),
			);
			$model  = M('Addons');
			$result = $model->where("mark='Qlogin'")->save(array('param' => json_encode($data)));
			if (0 < $result) {
				$this->success('保存成功');
			} else {
				$this->error('保存失败');
			}
		} else {
			$data = M('Addons')->field('param')->where("mark='Qlogin'")->find();
			$this->assign('info', json_decode($data['param'], true));
			$str = $this->fetch('config');
			return $str;
		}

	}
}
