<?php
namespace  Plugins\About;
require_once __ROOT__.'./Plugins/Plugin.class.php';
class AboutPlugin extends \Plugins\Plugin{
	protected   $config=array(
            		'version'=>'1.0',
            	    'author'=>'qiaokeli',
            	    'name'=>'关于我们',
            	    'descr'=>'企业信息'
            	 );
	//钩子默认的调用方法
	public function run(){
	   $this->display('content');	
	}
    
	public function getConfig(){
		return $this->config;
	}
    public  function install(){
		return true;
	}
	public function uninstall(){
	 return  true;	
	}
	public function set(){
		return true;
	}
}