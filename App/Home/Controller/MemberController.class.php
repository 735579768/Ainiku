<?php
//Author: 枫叶 <735579768@qq.com> <http://www.zhaokeli.com>
namespace Home\Controller;
use Think\Controller;
defined("ACCESS_ROOT") || die("Invalid access");
class MemberController extends Controller {
    protected function _initialize(){
       /* 读取数据库中的配置 */
        $config =   F('DB_CONFIG_DATA');
        if(!$config){
            $config =   api('Config/lists');
            F('DB_CONFIG_DATA',$config);
        }
		//trace($config);
        C($config); //添加配置
		//var_dump($config);
		C('TMPL_PARSE_STRING',array(
	  	'__STATIC__' => __ROOT__ . '/Public/Static',
        '__IMG__'=> __ROOT__ .'/Public/'.MODULE_NAME.'/'.C('DEFAULT_THEME').'/images',
        '__CSS__'=> __ROOT__ .'/Public/'.MODULE_NAME.'/'.C('DEFAULT_THEME').'/css',
        '__JS__' => __ROOT__ .'/Public/'.MODULE_NAME.'/'.C('DEFAULT_THEME').'/js',
    	));
	}
	public function login($username=null,$password=null,$verify=null){
		if(IS_POST){
	            /* 检测验证码 TODO: */
			if(!check_verify($verify)){
				$this->error('验证码输入错误！');
			}
	//用户登陆验证开始
		$error='';
       $map = array();
	   $dbprefix=__DB_PREFIX__;
		$map[getAccountType($username)]=$username;
		$jin=$dbprefix."member_group as a on ".$dbprefix."member.member_group_id=a.member_group_id";
		$field="*,".$dbprefix."member.status as status";
		$muser=M('Member');
        $user = $muser->field($field)->where($map)->join($jin)->find();
        if(is_array($user) && $user['status']==='1'){
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
				 define('UID',$user['member_id']);
                //更新用户登录信息
                self::updateLogin($user['member_id']); 
				$this->success('登陆成功',U('/'));
            } else {
                $error = '密码错误！';//密码错误
            }
        } else {
            $error = '用户不存在或被禁用！';  // '用户不存在或被禁用！'
        }	
	//验证结束																
		$this->error($error);
		}else{
			//显示登陆页面
			$this->display();
			}
		}
	public function register($member_group_id=2,$username=null, $password=null, $email=null, $mobile=null){
		if(IS_POST){
	      $data = array(
            'member_group_id'=>$member_group_id,
            'username' => $username,
            'password' => $password,
            'email'    => $email,
            'mobile'   => $mobile,
			'create_time'=>NOW_TIME,
			'update_time'=>NOW_TIME
        );

        //验证手机
        //if(empty($data['mobile'])) unset($data['mobile']);
		$user=D('Member');
        /* 添加用户 */
        if($user->create($data)){
             $user->password=ainiku_ucenter_md5($user->password);
			// $result =$user->add();
          //  return $this->$uid ? '注册成功' : '注册失败'; //0-未知错误，大于0-注册成功
        	if(0<$result){
					$this->success('注册成功',U('login'));
				}else{
					$this->error('注册失败');
					}
		} else {
            return $this->error($user->getError());
        }
		}else{
			$this->display();
			}
	}
/**
 *会员中心样式
 **/
 function info(){
	 $this->display();
	 }
	/**
     * 注销当前用户
     * @return void
     */
    public function logout(){
        session('user_auth', null);
        session('user_auth_sign', null);
		session(null);
		redirect(U('/'));
    }
   /**
     * 更新用户登录信息
     * @param  integer $uid 用户ID
     */
    protected static  function updateLogin($uid){
		$ip=get_client_ip();
//		$Ipp = new \Org\Net\IpLocation('UTFWry.dat'); // 实例化类
//		$location = $Ipp->getlocation($ip);
		$location=getIpLocation($ip);
        $data = array(
            'member_id'              => $uid,
            'update_time' => NOW_TIME,
            'last_login_ip'   =>$ip ,
			'last_login_adr'  =>$location['country'].$location['area']
        );
		M('Member')->where("member_id=$uid")->setInc('login');
		M('Member')->save($data);
		//保存用户登陆日志
		M('MemberLog')->add(
		array(
				'member_id'=>$uid,
				'ip'=>$Ip,
				'adr'=>$location['country'].$location['area'],
				'create_time'=>NOW_TIME
				)
		);

    }
}