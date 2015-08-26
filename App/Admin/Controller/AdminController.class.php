<?php
//Author: 枫叶 <735579768@qq.com> <http://www.zhaokeli.com>
namespace Admin\Controller;
use Common\Controller\CommonController;
if(!defined("ACCESS_ROOT"))die("Invalid access");
class AdminController extends CommonController {
	protected $model_name=null;
	protected $primarykey=null;
	protected $auth=null;

	protected function display($templateFile='',$charset='',$contentType='',$content='',$prefix=''){
		$str=$this->fetch($templateFile);
		$str=$this->auth->replaceurl($str);
		//查看是不是tab中的数据
		$mainmenu=I('get.mainmenu');
		if($mainmenu=='true'){
			$pattern='/<\/html>(.+)/si';
			$str=preg_replace($pattern,'</html>',$str);
			}
		echo $str;
		}
	 protected function _initialize(){
		   // 记录当前列表页的cookie
		   $forward=cookie('__forward__');
		   if(!IS_AJAX  && !IS_POST){
		  if(count($forward)>=2)array_shift($forward);
//			  $forward[]=$_SERVER['REQUEST_URI'];
			 //$forward=$_SERVER['REQUEST_URI'];
			$forward[]= $_SERVER['HTTP_REFERER'];
			  cookie('__forward__',$forward);  
		   }
		  defined('__FORWARD__')||define('__FORWARD__',$forward[0]);
		 $this->meta_title='首页';
		 //定义数据表前缀
		 defined('DBPREFIX') or define('DBPREFIX',C('DB_PREFIX'));
		 // 获取当前用户ID
         defined('UID') or define('UID',is_login());
		 if(!UID) $this->redirect('Public/login');
		 if(UID!=1){
			 defined('IS_ADMIN') or define('IS_ADMIN',false);
			 }else{
			 defined('IS_ADMIN') or define('IS_ADMIN',true);	
				}
		//先读取缓存配置
        $config =   F('DB_CONFIG_DATA');
        if(!$config || APP_DEBUG){
            $config =   api('Config/lists');
            F('DB_CONFIG_DATA',$config);
        }
        C($config); //添加配置
	//C('SHOW_PAGE_TRACE',true);
		if(I('get.mainmenu')=='true')C('SHOW_PAGE_TRACE',false);
		defined('__DB_PREFIX__')  or  define('__DB_PREFIX__',C('DB_PREFIX'));
		 //设置开发模式
		  defined('ISDEV') or define('ISDEV',APP_DEBUG);
		 //主题默认为空
		 C('DEFAULT_THEME','');
		//赋值当前登陆用户信息
		$uinfo=session('uinfo');
		$map[getAccountType($uinfo['username'])]=$uinfo['username'];
		$jin=__DB_PREFIX__."member_group as a on ".__DB_PREFIX__."member.member_group_id=a.member_group_id";
		$field="*,".__DB_PREFIX__."member.status as status";
        $user = D('Member')->field($field)->where($map)->join($jin)->find();
		session('uinfo',$user);
		$this->assign('uinfo',$user);
			//检查访问权限
			import('Ainiku.Auth');
			$this->auth = new \Ainiku\Auth;
			if(!$this->auth->check()){
				//die('not access');
				$this->error('啊哦,没有此权限,请联系管理员！',U($user['admin_index']));
				}

		 $this->uinfo=session('uinfo');
		 //取主导航
		$this->getMainNav();
	 }
	 /**
	  *取主导航
	  */
	 public function getMainNav(){
		 	$menu_id=I('menu_id');
			//if(empty($menu_id))die('no access');
		    //$authmenu=$this->getUserMenuId();
			$where="pid=0 and hide=0";
//			if(!APP_DEBUG){
//				$where.=" and is_dev=0";
//				}
		 	$nav=M('menu')->where($where)->order('sort asc')->select();
//			if(!IS_ADMIN){
//				foreach($nav as $key=>$val){
//					if(!in_array($val['id'],$authmenu)){
//						unset($nav[$key]);
//						}
//					}
//				}
		 	$this->assign('_MAINNAV_',$nav);
			
			//查到当前页面地址
			$current=array();
			if(empty($menu_id)){
				$act=CONTROLLER_NAME;
				if(C('CONTROLLER_LEVEL' )>1){
					$act=explode('\\',CONTROLLER_NAME);
					$act=$act[1];
					}
			 	$current = M('Menu')->where(" url like '%".$act."/".ACTION_NAME."%'")->find();
			}else{
				$current=M('Menu')->find($menu_id);
				}
			
			if(empty($current))return false;
			$curid=null;
			 if($current['pid']!=0){
				$curid=$current['pid'];
				//$current  = M('Menu')->where("hide=0 and id={$current['pid']}")->find();
				 }else{
				$curid=$current['id'];	 
					 }
				 
			 //取当前分组列表
			//$where="hide=0 and pid=$curid";
			$map['hide']=0;
			$map['pid']=$curid;
			//if(!APP_DEBUG)$map['url']=array('like',"%menu/%");
			//$strid=implode(',',$authmenu);
			//if(!IS_ADMIN)$map['id']=array('IN',$strid);
			$model=M('menu');
			 $grouplist=$model->where($map)->group('`group`')->order('sort asc,`group` asc')->select();
			 $this->assign('_GROUPLIST_',$grouplist);
			 //trace($grouplist);
			 //取当前后台的子菜单
			 //$map="hide=0 and pid=$curid";
			//if(!APP_DEBUG)$map['url']=array('not like','%menu/%');
			//if(!IS_ADMIN)$map['id']=array('IN',$strid);
			$childnav=M('menu')->where($map)->order('sort asc')->select();
//			if(!IS_ADMIN){
//				foreach($childnav as $key=>$val){
//					if(!in_array($val['id'],$authmenu)){
//						unset($childnav[$key]);
//						}
//					}
//				}
		 	$this->assign('_CHILDNNAV_',$childnav); 
		 }
	/**
	 *后台模块通用改变状态
	 **/
	public function updatefield($table='',$id='',$field='',$value=''){
		if(empty($table) || empty($id) || empty($field)){
			$this->error('参数不能为空');
		}
		$data=array(
			$field=>$value,
			'update_time'=>NOW_TIME
		);
	//$result=M($table)->where($this->primarykey."=$id")->save($data);
	$result=M($table)->where(M($table)->getPk()."=$id")->save($data);
	if(0<$result){
		$this->success('更新成功');	
		}else{
		$this->error('更新失败');
			}
	}
	 function setposition($table=null,$id=null,$field=null,$value=null){
		  if(IS_POST){
			  $postr=implode(',',I('position'));
			  	$result=M($table)->where($table.'_id='.$id)->save(array($field=>$postr));
				if(0<$result){
					 $this->success(tomark($postr,$table,$field));
					}
			  }else{
				  $str='<form id="positionform" method="post" action="'.U('setposition').'">
				  <input type="hidden" name="table" value="'.$table.'" />
				  <input type="hidden" name="id" value="'.$id.'" />
				  <input type="hidden" name="field" value="'.$field.'" />
				  '.tomark($value,$table,$field,true).'
				  </form>';
				  $this->success($str);
				  }
		 }
}
