<?php
//Author: 枫叶 <735579768@qq.com> <http://www.zhaokeli.com>
namespace Daili\Controller;
use Common\Controller\CommonController;
if(!defined("ACCESS_ROOT"))die("Invalid access");
class DailiController extends CommonController {
	protected $model_name=null;
	protected $primarykey=null;
	protected $auth=null;

	protected function display($templateFile='',$charset='',$contentType='',$content='',$prefix=''){
		$str=$this->fetch($templateFile);
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

		   // 记录当前列表页的cookie
		   $forward=cookie('__forward__');
		   if(!IS_AJAX  && !IS_POST){
		  if(count($forward)>=2)array_shift($forward);
			isset($_SERVER['HTTP_REFERER'])?($forward[] = $_SERVER['HTTP_REFERER']):'';
			  cookie('__forward__',$forward);  
		   }
		  defined('__FORWARD__')||define('__FORWARD__',$forward[0]);
		 $this->meta_title='首页';
		//先读取缓存配置
        $config =   F('DB_CONFIG_DATA');
        if(!$config || APP_DEBUG){
            $config =   api('Config/lists');
            F('DB_CONFIG_DATA',$config);
        }
        C($config); //添加配置
		if(I('get.mainmenu')=='true')C('SHOW_PAGE_TRACE',false);
		defined('__DB_PREFIX__')  or  define('__DB_PREFIX__',C('DB_PREFIX'));
		 //主题默认为空
		 C('DEFAULT_THEME','');
		$this->assign('uinfo',session('uinfo'));
		 $this->uinfo=session('uinfo');
		 //取主导航
		$this->assign('ADMIN_MENU',C('ADMIN_MENU'));
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
}
