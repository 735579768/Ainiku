<?php
//Author: 枫叶 <735579768@qq.com> <http://www.zhaokeli.com>
namespace Admin\Controller;
use Think\Controller;

defined("ACCESS_ROOT") || die("Invalid access");
class PublicController extends Controller {
	protected function _empty() {
		//后台统一的404页面
		if (!APP_DEBUG) {
			$this->display('Public/404');
		}
	}
	public function index() {
		$this->redirect('login');
	}
	protected function _initialize() {

		//先读取缓存配置
		$config = F('DB_CONFIG_DATA');
		if (!$config) {
			/* 读取数据库中的配置 */
			$config = api('Config/lists');
			//写入缓存
			F('DB_CONFIG_DATA', $config);
		}
		C($config); //添加配置
		defined('__DB_PREFIX__') or define('__DB_PREFIX__', C('DB_PREFIX'));
		//主题默认为空
		C('DEFAULT_THEME', '');

		C('TMPL_PARSE_STRING', array(
			'__STATIC__' => __ROOT__ . '/Public/Static',
			'__IMG__'    => __ROOT__ . '/Public/' . MODULE_NAME . '/' . C('DEFAULT_THEME') . '/images',
			'__CSS__'    => __ROOT__ . '/Public/' . MODULE_NAME . '/' . C('DEFAULT_THEME') . '/css',
			'__JS__'     => __ROOT__ . '/Public/' . MODULE_NAME . '/' . C('DEFAULT_THEME') . '/js',
		));
	}
	public function login($username = null, $password = null, $verify = null, $autologin = false) {
		if (IS_POST || $autologin) {
			/* 检测验证码 TODO: */
			if (!check_verify($verify) && !($autologin)) {
				$this->error('验证码输入错误！');
			}
			//自动判断用户名是哪个字段的
			$map[getAccountType($username)] = $username;
			$map['password']                = ainiku_ucenter_md5($password);
			$map['status']                  = 1;
			//$map['member_group_id']=1;
			$map['is_adminlogin'] = 1;
			$user                 = D('MemberView')->where($map)->find();
			if (empty($user)) {
				//登录失败
				cookie('__uid__', null);
				return $autologin ? (false) : $this->error('用户名或密码错误!');
			} else {
				//登陆成功

				/* 记录登录SESSION和COOKIES */
				$auth = array(
					'uid'             => $user['member_id'],
					'username'        => $user['username'],
					'last_login_time' => $user['update_time'],
				);
				session('user_auth', $auth);
				session('uinfo', $user);
				session('user_auth_sign', data_auth_sign($auth));
				//更新用户登录信息
				$this->updateLogin($user['member_id']);

				//把用户密码加密保存到cookie中
				if (!$autologin) {
					$u['u'] = ainiku_encrypt($username);
					$u['p'] = ainiku_encrypt($password);

					//如果有验证码的话就再次设置记录时间cookie
					$a = I('post.remember');
					$b = 0;
					switch ($a) {
					case 1:$b = 24 * 3600;
						break;
					case 2:$b = 24 * 3600 * 7;
						break;
					case 3:$b = 24 * 3600 * 30;
						break;
					default:$b = -1;
					}
					cookie('__uid__', $u, $b);
				}
				return $autologin ? $user['member_id'] : ($this->success('登录成功！', U($user['admin_index'], array('mainmenu' => 'true'))));
			}

		} else {
			if (is_login()) {
				$user = session('uinfo');
				redirect(U($user['admin_index'], array('mainmenu' => 'true')));
			} else {
				$this->display();

			}
		}
	}
	public function autologin() {
		$u = cookie('__uid__');
		if (!empty($u)) {
			return $this->login(ainiku_decrypt($u['u']), ainiku_decrypt($u['p']), null, true);
		}
	}
//    /* 退出登录 */
	//    public function logout(){
	//        if(is_login()){
	//            D('Member')->logout();
	//            session('[destroy]');
	//			cookie('u',null);
	//            $this->success('退出成功！', U('login'));
	//        } else {
	//            $this->redirect(U('login'));
	//        }
	//    }
	public function verify() {
		$conf = array(
			'imageH'   => 40,
			'imageW'   => 200,
			'fontSize' => 20,
			'bg'       => array(255, 255, 255),
			'useNoise' => false, // 是否添加杂点
			'length'   => 4,
		);
		$verify = new \Think\Verify($conf);
		$verify->entry(1);
	}
	/**
	 * 更新用户登录信息
	 * @param  integer $uid 用户ID
	 */
	protected function updateLogin($uid) {
		$ip       = get_client_ip();
		$location = getIpLocation($ip);
		$data     = array(
			'member_id'      => $uid,
			'update_time'    => NOW_TIME,
			'last_login_ip'  => $ip,
			'last_login_adr' => $location,
		);
		M('Member')->where("member_id=$uid")->setInc('login');
		M('Member')->save($data);
		//保存用户登陆日志
		M('MemberLog')->add(
			array(
				'member_id'   => $uid,
				'ip'          => $ip,
				'adr'         => $location,
				'create_time' => NOW_TIME,
			)
		);

	}
	/* 退出登录 */
	public function logout($modpassword = false) {
		if (is_login()) {
			//session('user_auth', null);
			//session('user_auth_sign', null);
			//  session('[destroy]');
			session(null);
			cookie(null);
			if (!$modpassword) {
				$this->success('退出成功！', U('Public/login'));
			}
		} else {
			$this->redirect(U('Public/login'));
		}
	}
}