<?php
namespace  Plugins\PathInfo;
require_once __ROOT__.'./Plugins/Plugin.class.php';
class PathInfoPlugin extends \Plugins\Plugin{
	protected   $config=array(
            		'version'=>'1.0',
            	    'author'=>'qiaokeli',
            	    'name'=>'系统路径信息',
            	    'descr'=>'系统路径信息'
            	 );
	//钩子默认的调用方法
	public function run($a=null,$b=null){
	  $this->assign('a',$a);
	  $this->assign('b',$b);
	  //检测缓存目录
	  $runtime=F(md5('runtimeauth'));
	  if(empty($runtime)){
	  $runtime=check_dir_iswritable(RUNTIME_PATH);
	  F(md5('runtimeauth'),$runtime);
	  }
	  $this->assign('runtimestatus',$runtime);
	  
	  $download=F(md5('downloadauth'));
	  if(empty($download)){
	  $download=check_dir_iswritable(C('DOWNLOAD_UPLOAD.rootPath'));
	  F(md5('downloadauth'),$download);
	  }
	  $this->assign('downloadstatus',$download);
	  
	  $picture=F(md5('pictureauth'));
	  if(empty($picture)){
	  $picture=check_dir_iswritable('.'.C('FILE_UPLOAD.rootPath').'/');
	  F(md5('pictureauth'),$picture);
	  }
	  $this->assign('picturestatus',$picture);	  
	  
	  $edit=F(md5('editauth'));
	  if(empty($edit)){
	  $edit=check_dir_iswritable(C('EDITOR_UPLOAD.rootPath'));
	  F(md5('editauth'),$edit);
	  }
	  $this->assign('editstatus',$edit);
	  
	  //取缓存目录大小
	  $dirsize=F(md5('dirsize'));
	  if(empty($dirsize)){
		  $dirsize=(getDirSize($rutimepath=str_replace(MODULE_NAME.'/','',RUNTIME_PATH))/1000).'k';
	  	  F(md5('dirsize'),$dirsize);
	  }
	  $runsize=F(md5('runtimecachesize'));
	  if(empty($runsize))$runsize='';
	  $this->assign('runsize',$runsize);

	  //取上传目录大小
	  $data=F(md5('uploadssizecache'));
	  if(empty($data))$data='';
	  $this->assign('uploadssize',$data); 
	  $this->display('content');	
	}
	//取上传目录大小
	public function setuploadssize(){
		set_time_limit(0);
		$data['size']=(getDirSize('./Uploads/')/1000).'k';
		$data['time']=time();
		F(md5('uploadssizecache'),$data);
		}
	//取缓存目录大小
	public function setrunsize(){
		set_time_limit(0);
		$data['size']=(getDirSize(RUNTIME_PATH)/1000).'k';
		$data['time']=time();
		F(md5('runtimecachesize'),$data);
		}
	public function getConfig(){
		return $this->config;
	}
    public  function install(){
	 return true;
	}
	public function uninstall(){
     return true;
	}
	public function set(){

	}
}
