<?php
//Author: 枫叶 <735579768@qq.com> <http://www.zhaokeli.com>
namespace Admin\Controller;
use Common\Controller\CommonController;
if(!defined("ACCESS_ROOT"))die("Invalid access");
class AdminController extends CommonController {
	protected $model_name=null;
	protected $primarykey=null;
	protected $auth=null;
	protected function _initialize(){
		 (get_naps_bot()!==false)&&die('');//不让蜘蛛抓取
		
		 // 获取当前用户ID
		 $uid=is_login();
		 if($uid){
			 defined('UID') or define('UID',$uid);
		 }else{
			 $login=A('Public');
			 $uid=$login->autologin();
			 $uid>0?define('UID',$uid):redirect(U('Public/login'));
		 }
		 (UID!=1)?define('IS_ADMIN',false):define('IS_ADMIN',true);

		//先读取缓存配置
		$config =   F('DB_CONFIG_DATA');
		if(!$config || APP_DEBUG){
			$config =   api('Config/lists');
			F('DB_CONFIG_DATA',$config);
		}
		C($config); //添加配置
		if(I('mainmenu')=='true'){
			define('MAIN_IFRAME','true');
			C('SHOW_PAGE_TRACE',false);
		}else{
			define('MAIN_IFRAME','false');
		}
		$this->assign('MAIN_IFRAME',MAIN_IFRAME);
		//定义数据表前缀
		defined('__DB_PREFIX__')  or  define('__DB_PREFIX__',C('DB_PREFIX'));
		//主题默认为空
		C('DEFAULT_THEME','');
		//检查访问权限
		import('Ainiku.Auth');
		$this->auth = new \Ainiku\Auth;
		if(!$this->auth->check()){
			$this->error('啊哦,没有此权限,请联系管理员！',U($user['admin_index']));
		}

		// 记录当前列表页的cookie
		$forward=cookie('__forward__');
		is_array($forward)||($forward=array());
		if(!IS_AJAX  && !IS_POST){
			if(count($forward)>=2)array_shift($forward);
			isset($_SERVER['HTTP_REFERER'])?($forward[] = $_SERVER['HTTP_REFERER']):'';
			cookie('__forward__',$forward);  
		}
		defined('__FORWARD__')||define('__FORWARD__',$forward[0]);
		//设置全局的模板变量
		$this->assign('meta_title','首页');
		$this->assign('uinfo',session('uinfo'));
		//防止重复请求,如果是主框架请求就只输出个目录菜单
		
		if(MAIN_IFRAME=='true' ||(CONTROLLER_NAME=='Index' && ACTION_NAME=='index')){
			//取主导航
			$this->getMainNav();
			$this->display(CONTROLLER_NAME.'/'.ACTION_NAME);
			die();
		}
	 }
	 /**
	  *取主导航
	  */
	 public function getMainNav(){
		 	$menu_id=I('menu_id');
			$nav=F('sys_mainnav');
			if(empty($nav)||APP_DEBUG){
				$where="pid=0 and hide=0";
				$nav=M('menu')->where($where)->order('sort asc')->select();
				F('sys_mainnav',$nav);
			}
		 	$this->assign('_MAINNAV_',$nav);
			
			//查到当前页面地址
			$current=array();
			if(empty($menu_id)){
				$act=CONTROLLER_NAME;
				if(C('CONTROLLER_LEVEL' )>1){
					$act=explode('\\',CONTROLLER_NAME);
					$act=$act[1];
					}
				$url=$act."/".ACTION_NAME;
				$url_sha1=sha1($url);
				$current=F('sys_current_url'.$url_sha1);
				if(empty($current)||APP_DEBUG){

					$current = M('Menu')->where(" url like '%".$url."%'")->find();
					F('sys_current_url'.$url_sha1,$current);
				}
			}else{
				$current=M('Menu')->find($menu_id);
				}
			
			if(empty($current))return false;
			$curid='';
			 if($current['pid']!=0){
				$curid=$current['pid'];
				 }else{
				$curid=$current['id'];	 
					 }
				 
			 //取当前分组列表
			 $grouplist=F('sys_grouplist'.$curid);
			 $childnav=F('sys_childnavlist'.$curid);
			 if(APP_DEBUG||empty($grouplist)||empty($childnav)){
				$map['hide']=0;
				$map['pid']=$curid;
				$model=M('menu');
				 $grouplist=$model->where($map)->group('`group`')->order('`group` asc')->select();
				 foreach($grouplist as $key=>$val){
					 $grouplist[$key]['group']=preg_replace('/\d*/','',$val['group']);
					 }
				 
				$childnav=M('menu')->where($map)->order('sort asc')->select();
				 foreach($childnav as $key=>$val){
					 $childnav[$key]['group']=preg_replace('/\d*/','',$val['group']);
					 }
				 F('sys_grouplist'.$curid,$grouplist);
				 F('sys_childnavlist'.$curid,$childnav);
			 }
			$this->assign('_GROUPLIST_',$grouplist);
		 	$this->assign('_CHILDNNAV_',$childnav); 
		 }
	/**
	 *后台模块通用改变状态
	 **/
	public function updatefield($table='',$id='',$field='',$value=''){
		if(empty($table) || empty($id) || empty($field)){
			$this->error(L('_PARAM_NOT_NULL_'));
		}
		$data=array(
			$field=>$value,
			'update_time'=>NOW_TIME
		);
	$result=M($table)->where(M($table)->getPk()."=$id")->save($data);
	(0<$result)?$this->success(L('_UPDATE_SUCCESS_')):$this->error(L('_UPDATE_FAIL_'));
	}
	 function setposition($table=null,$id=null,$field=null,$value=null){
		  if(IS_POST){
			  	$position=I('position');
			    $postr=implode(',',$position);
			  	$result=M($table)->where($table.'_id='.$id)->save(array($field=>$postr));
				if(0<$result){
					 $this->success(array(tomark($postr,$table,$field),$postr));
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
	protected function display($templateFile='',$charset='',$contentType='',$content='',$prefix=''){
		$str=$this->fetch($templateFile);
		$str=$this->auth->replaceurl($str);
		//查看是不是tab中的数据
		$mainmenu=I('get.mainmenu');
		if($mainmenu=='true'){
			$pattern='/<\/html>(.+)/si';
			$str=preg_replace($pattern,'</html>',$str);
			}
		if(!APP_DEBUG){
		//如果不是调试模式
			$pa=array(
					'/<\!\-\-.*?\-\->/i',//去掉html注释
					'/(\s*?\r?\n\s*?)+/i'//删除空白行
					);
			$pr=array('',"\n");
			$str=preg_replace($pa,$pr,$str);
			}
		echo $str;
		}
}
