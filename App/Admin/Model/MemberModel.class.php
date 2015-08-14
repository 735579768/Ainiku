<?php
// | Author: 枫叶 <735579768@qq.com> <http://www.zhaokeli.com>
// +----------------------------------------------------------------------
namespace Admin\Model;
use Think\Model;

if(!defined("ACCESS_ROOT"))die("Invalid access");

/**
 * 用户模型
 * @author 寞枫 <735579768@qq.com>
 */

class MemberModel extends BaseModel {
    /* 用户模型自动验证 */
    protected $_validate = array(
        array('member_group_id', 'require', '请选择用户组'),
		/* 验证用户名 */
        array('username', '4,30', -1, self::EXISTS_VALIDATE, 'length'), //用户名长度不合法
        array('username', 'checkDenyMember', -2, self::EXISTS_VALIDATE, 'callback'), //用户名禁止注册
        array('username', '', -3, self::EXISTS_VALIDATE, 'unique',3), //用户名被占用
        /* 验证密码 */
        array('password', '5,30', -4, self::EXISTS_VALIDATE, 'length'), //密码长度不合法
        /* 验证邮箱 */
        array('email', 'email', -5, self::EXISTS_VALIDATE), //邮箱格式不正确
        array('email', '1,32', -6, self::EXISTS_VALIDATE, 'length'), //邮箱长度不合法
        array('email', 'checkDenyEmail', -7, self::EXISTS_VALIDATE, 'callback'), //邮箱禁止注册
       // array('email', '', -8, self::EXISTS_VALIDATE, 'unique',1), //邮箱被占用
        /* 验证手机号码 */
        array('mobile', '//', -9, self::EXISTS_VALIDATE), //手机格式不正确 TODO:
        array('mobile', 'checkDenyMobile', -10, self::EXISTS_VALIDATE, 'callback'), //手机禁止注册
        array('mobile', '', -11, self::EXISTS_VALIDATE, 'unique'), //手机号被占用
    );
    /* 用户模型自动完成 */
    protected $_auto = array(
        array('reg_ip', 'get_client_ip', self::MODEL_INSERT, 'function', 1),
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_BOTH)
        //array('status', '1', self::MODEL_INSERT),
    );
   /**
     * 检测用户名是不是被禁止注册
     * @param  string $username 用户名
     * @return boolean          ture - 未禁用，false - 禁止注册
     */
    protected function checkDenyMember($username){
        return true; //TODO: 暂不限制，下一个版本完善
    }

    /**
     * 检测邮箱是不是被禁止注册
     * @param  string $email 邮箱
     * @return boolean       ture - 未禁用，false - 禁止注册
     */
    protected function checkDenyEmail($email){
        return true; //TODO: 暂不限制，下一个版本完善
    }

    /**
     * 检测手机是不是被禁止注册
     * @param  string $mobile 手机
     * @return boolean        ture - 未禁用，false - 禁止注册
     */
    protected function checkDenyMobile($mobile){
        return true; //TODO: 暂不限制，下一个版本完善
    }


    /**
   /**
     * 注册一个新用户
     * @param  string $username 用户名
     * @param  string $password 用户密码
     * @param  string $email    用户邮箱
     * @param  string $mobile   用户手机号码
     * @return integer          注册成功-用户信息，注册失败-错误编号
     */
    public function register($member_group_id,$username, $password, $email, $mobile){
        $data = array(
            'member_group_id'=>$member_group_id,
            'username' => $username,
            'password' => $password,
            'email'    => $email,
            'mobile'   => $mobile,
			'create_time'=>NOW_TIME,
			'update_time'=>NOW_TIME,
			'account'=>createAccount()
        );

        //验证手机
        if(empty($data['mobile'])) unset($data['mobile']);

        /* 添加用户 */
        if($this->create($data)){
			$this->data['password']=ainiku_ucenter_md5($this->data['password']);
            $uid = $this->add();
            return $uid ? $uid : 0; //0-未知错误，大于0-注册成功
        } else {
            return $this->getError(); //错误详情见自动验证注释
        }
    }

    /**
     * 用户登录认证
     * @param  string  $username 用户名
     * @param  string  $password 用户密码
     * @param  integer $type     用户名类型 （1-用户名，2-邮箱，3-手机，4-UID）
     * @return integer           登录成功-用户ID，登录失败-错误编号
     */
    public function login($username, $password, $type = 1){
        $map = array();
//        switch ($type) {
//            case 1:
//                $map['username'] = $username;
//                break;
//            case 2:
//                $map['email'] = $username;
//                break;
//            case 3:
//                $map['mobile'] = $username;
//                break;
//            case 4:
//                $map['id'] = $username;
//                break;
//            default:
//                return 0; //参数错误
//        }
		$map[getAccountType($username)]=$username;
		$jin=__DB_PREFIX__."member_group as a on ".__DB_PREFIX__."member.member_group_id=a.member_group_id";
		$field="*,".__DB_PREFIX__."member.status as status";
        $user = $this->field($field)->where($map)->join($jin)->find();
		//echo($this->getlastsql());
        if(is_array($user) && $user['status']==='1' &&  $user['is_adminlogin']==='1'){
            /* 验证用户密码 */
          $md5pas=ainiku_ucenter_md5($password);
          if($md5pas === $user['password']){

                /* 记录登录SESSION和COOKIES */
                $auth = array(
                    'uid'             => $user['member_id'],
                    'username'        => $user['username'],
                    'last_login_time' => $user['last_login_time'],
                );
                session('user_auth', $auth);
				session('uinfo',$user);
                session('user_auth_sign', data_auth_sign($auth));
                //更新用户登录信息
                $this->updateLogin($user['member_id']); 
                return $user['member_id']; //登录成功，返回用户ID
            } else {
                return -2;//密码错误
            }
        } else {
            return -1;  // '用户不存在或被禁用！'
        }
    }
   /**
     * 更新用户登录信息
     * @param  integer $uid 用户ID
     */
    protected function updateLogin($uid){
		$ip=get_client_ip();
//		$Ipp = new \Org\Net\IpLocation('UTFWry.dat'); // 实例化类
//		$location = $Ipp->getlocation($ip);
		$location=getIpLocation($ip);
        $data = array(
            'member_id'              => $uid,
            'update_time' => NOW_TIME,
            'last_login_ip'   =>$ip ,
			'last_login_adr'  =>$location
        );
		M('Member')->where("member_id=$uid")->setInc('login');
		$this->save($data);
		//保存用户登陆日志
		M('MemberLog')->add(
		array(
				'member_id'=>$uid,
				'ip'=>$ip,
				'adr'=>$location,
				'create_time'=>NOW_TIME
				)
		);

    }
	/**
     * 注销当前用户
     * @return void
     */
    public function logout(){
        session('user_auth', null);
        session('user_auth_sign', null);
		session(null);
    }

}
