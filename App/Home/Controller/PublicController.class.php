<?php
//Author: 枫叶 <735579768@qq.com> <http://www.zhaokeli.com>
namespace Home\Controller;
use Think\Controller;
if(!defined("ACCESS_ROOT"))die("Invalid access");
class PublicController extends Controller {
    public function verify(){
		$conf=array(
				'imageH'=>50,
				'imageW'=>150,
				'fontSize'=>20,
				'bg'=>  array(255, 255, 255),
				'length'=>4,
				 'useNoise'  => false           // 是否添加杂点	
		);
        $verify = new \Think\Verify($conf);
        $verify->entry(1);
    }
	public function error404(){
		//前台统一的404页面
    	header('HTTP/1.1 404 Not Found');
		header("status: 404 Not Found");
    	$this->display('Public:404');
		die();
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
		$map['password']=ainiku_ucenter_md5($password);
		$map['status']=1;
		$map['member_group_id']=2;//前台只允许会员登陆
        $user = M('Member')->field($field)->where($map)->find();
        if(empty($user)){																
				$this->error('用户名或密码错误！');
        } else {
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
        }	

		}else{
			//显示登陆页面
			if(IS_AJAX){
				$this->display('ajaxlogin');
				}else{
				$this->display();	
					}
			}
		}
	public function register($member_group_id=11,$username=null, $password=null, $email=null, $mobile=null){
		if(IS_POST){
	      $data = array(
            'member_group_id'=>$member_group_id,
            'username' => $username,
            'password' => $password,
            'email'    => $email,
            'mobile'   => $mobile,
			'account'	=>createAccount(),
			'create_time'=>NOW_TIME,
			'update_time'=>NOW_TIME
        );

        //验证手机
        //if(empty($data['mobile'])) unset($data['mobile']);
		$user=D('Member');
        /* 添加用户 */
        if($user->create()){
             $user->password=ainiku_ucenter_md5($user->password);
			 $user->member_group_id=$member_group_id;
			 $result =$user->add();
           //return $this->$uid ? '注册成功' : '注册失败'; //0-未知错误，大于0-注册成功
        	if(0<$result){
					$this->success('注册成功',U('login'));
				}else{
					$this->error('注册失败');
					}
		} else {
            return $this->error($user->getError());
        }
		}else{
			if(IS_AJAX){
				$this->display('ajaxregister');
				}else{
				$this->display();	
					}
			
			}
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
	/**
     * 注销当前用户
     * @return void
     */
    public function logout(){
        session('user_auth', null);
        session('user_auth_sign', null);
		session(null);
		loginout();
		redirect('/');
    }
}