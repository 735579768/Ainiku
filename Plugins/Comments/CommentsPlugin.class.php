<?php
namespace  Plugins\Comments;
require_once pathA('/Plugins/Plugin.class.php');
class CommentsPlugin extends \Plugins\Plugin{
	protected   $config=array(
            		'version'=>'1.0',
            	    'author'=>'qiaokeli',
            	    'name'=>'留言插件',
            	    'descr'=>'留言'
            	 );
	//钩子默认的调用方法
	public function run(){
	   $this->display('content');	
	}
   public function ajaxlist($arc_id='',$pid=''){
	  // empty($arc_id)&&die('没有评论');
	   $map['status']=1;
	   empty($pid)||$map['pid']=$pid;
	   //$map['arc_id']=$arc_id;
	   $list=M('Comments')->where($map)->order('create_time desc')->select();
	   foreach($list as $key=>$val){
		    $map['pid']=$val['id'];
			$child=M('Comments')->where($map)->order('create_time desc')->select();
			if(empty($child)){
				$list[$key]['_']=array();
				}else{
				$list[$key]['_']=$child;	
					}
		   }
	   if(empty($list)){
		die('没有评论');
   		}else{
	   $this->assign('_list',$list);
	   echo $this->fetch('ajaxlist');		   
		   }
	   die();
	   }
   public function add(){
		if(IS_POST){
			$verify=I('verify');
			if(empty($verify)){$this->error('请输入验证码!');}
			if(!check_verify($verify)){
				$this->error('验证码输入错误！');
			  }
			 $model=new \Plugins\Comments\CommentsModel();
			if($model->create()){
					$result=$model->add();
					if(0<$result){
						$list[]=M('Comments')->find($result);
						$this->assign('_list',$list);
						$str=$this->fetch('ajaxlist');
						$this->success(array('content'=>$str,msg=>'留言成功'));
						}else{
						$this->error('留言失败');	
							}						
				}else{
					$this->error($model->geterror());
					}
		}else{
			$this->display('add');
			}	 
	   }
    public  function install(){
		if(MODULE_NAME!=='Admin')die('');
			$prefix=C('DB_PREFIX');
$sql = <<<sql
				DROP TABLE IF EXISTS `{$prefix}comments`;
				CREATE TABLE `{$prefix}comments` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `pid` int(11)  NULL DEFAULT 0 ,
				  `arc_id` int(11)  NULL DEFAULT 0 ,
				  `title` varchar(255) DEFAULT NULL,
				  `content` varchar(255) DEFAULT NULL,
				  `name` varchar(255) DEFAULT NULL,
				  `mobile` varchar(255) DEFAULT NULL,
				  `email` varchar(255) DEFAULT NULL,
				  `url` varchar(255) DEFAULT NULL,
				  `qq` varchar(255) DEFAULT NULL,
				  `status` tinyint(1)  NULL DEFAULT 1 ,
				  `ip` varchar(255) DEFAULT NULL,
				  `location` varchar(255) DEFAULT NULL,
				  `create_time` int(11) DEFAULT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;
sql;
		$arr=explode(';',$sql);
		foreach($arr as $val){
			if(!empty($val))M()->execute($val);
			}
    	//向后台添加菜单，如果不添加的话直接返回真
      $data=array(
      	 'title'=>'留言管理',//插件后台菜单名字
         'pid'=>ADDONS_MENU,//不用改变
         'url'=>'Addons/plugin?pn=Comments&pm=lists',//填写后台菜单url名称和方法
         'group'=>'已装插件',//不用改变
         'type'=>'Comments'    //填写自己的插件名字
      );
      //添加到数据库
       if(M('Menu')->add($data)){
       	return true;
       }else{
       	return false;
       }
	}        
public function lists(){
		//只允许后台访问
		if(MODULE_NAME!=='Admin')die('');
	 	$name=I('name');
		$map['name']=array('like','%'.$name.'%');
		//$map['status']=array('egt',0);
    	$this->pages(array(
					'model'=>'Comments',
					'where'=>$map,
					'order'=>'status asc,id desc'
					));
	 $this->meta_title="留言列表";
	 return $this->fetch('lists');
	 }
public function check($id=''){
			//只允许后台访问
		if(MODULE_NAME!=='Admin')die('');
	$this->data=M('comments')->find($id);
	//M('comments')->where("id=$id")->setInc('is_view',1);
	$this->display('check');
	die();
	}	 
    function del(){
				//只允许后台访问
		if(MODULE_NAME!=='Admin')die('');
    	//$id=I("id");//I('get.article_id');
		$id=isset($_REQUEST['id'])?I('get.id'):I("id");
		if(empty($id))$this->error('请先进行选择');
		$model=M('Comments');
    	$result=$model->where("id in ($id)")->delete();
    	if(result){
    	  $this->success('已经彻底删除',U('index'));
    	}else{
    	  $this->error('操作失败');
    	}
    }
	function delall(){
				//只允许后台访问
		if(MODULE_NAME!=='Admin')die('');
		$result=M('Comments')->where("1=1")->delete();
    	if(result){
    	  $this->success('已经清空',U('index'));
    	}else{
    	  $this->error('操作失败');
    	}		
		}
	public function uninstall(){
		//只允许后台访问
		if(MODULE_NAME!=='Admin')die('');
		$prefix=C('DB_PREFIX');
		$sql = <<<sql
						DROP TABLE IF EXISTS `{$prefix}comments`;
sql;
				$arr=explode(';',$sql);
				foreach($arr as $val){
					if(!empty($val))M()->execute($val);
					}
	//删除后台添加的菜单，如果没有直接返回真
	$map['type']='Comments'; 
	  if(M('Menu')->where($map)->delete()){
	  	return true;
	  }else{
	  	return false;
	  }
	}
	public function set(){
		return true;
	}
}