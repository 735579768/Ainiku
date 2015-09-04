<?php
namespace  Plugins\ConInfo;
require_once pathA('/Plugins/Plugin.class.php');
class ConInfoPlugin extends \Plugins\Plugin{
	protected   $config=array(
            		'version'=>'1.0',
            	    'author'=>'qiaokeli',
            	    'name'=>'系统内容信息',
            	    'descr'=>'插件描述'
            	 );
	//钩子默认的调用方法
	public function run(){
		$this->assign('usercount',M('Member')->count());
		$this->assign('articlecount',M('Article')->count());
		$this->assign('goodscount',M('Goods')->count());
		$this->assign('catecount',M('Category')->count());
		$this->assign('groupcount',M('MemberGroup')->count());
		//$this->assign('adcount',M('Module')->count());
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