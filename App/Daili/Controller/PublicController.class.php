<?php
//Author: 枫叶 <735579768@qq.com> <http://www.zhaokeli.com>
namespace Daili\Controller;
use Think\Controller;
if(!defined("ACCESS_ROOT"))die("Invalid access");
class PublicController extends Controller {
 	protected function _empty(){
		//后台统一的404页面
			$this->display('Public:404');
		}
	public function index(){
		$this->redirect('login');
		}
	protected function _initialize(){
		 //定义数据表前缀
		 defined('DBPREFIX') or define('DBPREFIX',C('DB_PREFIX'));
		//先读取缓存配置
        $config =   F('DB_CONFIG_DATA');
        if(!$config){
			/* 读取数据库中的配置 */
            $config =   api('Config/lists');
			//写入缓存
            F('DB_CONFIG_DATA',$config);
        }
        C($config); //添加配置
		defined('__DB_PREFIX__')  or  define('__DB_PREFIX__',C('DB_PREFIX'));
		 //主题默认为空
		 C('DEFAULT_THEME','');
	}
	public function login($username=null,$password=null,$verify=null,$autologin=false){
      if(IS_POST ||$autologin){
            /* 检测验证码 TODO: */
			if(!check_verify($verify) && !($autologin)){
				$this->error('验证码输入错误！');
			}
            $uid = D('Member')->login($username, $password);
            if(0 < $uid){ 
					//UC登录成功//把用户密码加密保存到cookie中
					
					if(!$autologin){
						$u['u']=ainiku_encrypt($username);
						$u['p']=ainiku_encrypt($password);
						
						//如果有验证码的话就再次设置记录时间cookie
						$a=I('post.remember');
						$b=0;
						switch($a){
							case 1: $b=24*3600; break;
							case 2: $b=24*3600*7;break;
							case 3: $b=24*3600*30; break;
							default:$b=-1;							   							
							}
						cookie('__uid__',$u,$b);
					}
					return $autologin?$uid:($this->success('登录成功！')));
            } else { 
			   //登录失败
				
				//清空cookie
				cookie('__uid__',null);
                switch($uid) {
                    case -1: $error = '用户不存在或被禁用！'; break; //系统级别禁用
                    case -2: $error = '密码错误！'; break;
                    default: $error = L('_UNKNOWN_ERROR_'); break; // 0-接口参数错误（调试阶段使用）
                }
				return $autologin?(false):$this->error($error);
            }
        } else {
            if(is_login()){
                redirect(U('Index/index'));
            }else{
					$this->display();	
                
            }
        }
    }
	public function autologin(){
		$u=cookie('__uid__');
		if(!empty($u)){
		return $this->login(ainiku_decrypt($u['u']),ainiku_decrypt($u['p']),null,true);
		}
		}
    /* 退出登录 */
    public function logout(){
        if(is_login()){
            D('Member')->logout();
            session('[destroy]');
			cookie(null);
            $this->success('退出成功！', U('Public/login'));
        } else {
            $this->redirect(U('Public/login'));
        }
    }
    public function verify(){
		$conf=array(
				'imageH'=>50,
				'imageW'=>200,
				'fontSize'=>25,
				'bg'=>  array(255, 255, 255),
				 'useNoise'  => false ,          // 是否添加杂点	
				'length'=>4
		);
        $verify = new \Think\Verify($conf);
        $verify->entry(1);
    }
}