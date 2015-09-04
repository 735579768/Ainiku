<?php
namespace  Plugins\Chart;
require_once pathA('/Plugins/Plugin.class.php');
class ChartPlugin extends \Plugins\Plugin{
	protected   $config=array(
            		'version'=>'1.0',
            	    'author'=>'qiaokeli',
            	    'name'=>'访问流量',
            	    'descr'=>'流量分析图表'
            	 );
	//钩子默认的调用方法
	public function run($a=null,$b=null){
	  $this->assign('a',$a);
	  $this->assign('b',$b);
	   $this->display('content');	
	}
	public function getConfig(){
		return $this->config;
	}
    public  function install(){
//    	//向后台添加菜单，如果不添加的话直接返回真
//      $data=array(
//      	 'title'=>'测试插件',//插件后台菜单名字
//         'pid'=>ADDONS_MENU,//不用改变
//         'url'=>'Addons/plugin?name=Chart&method=set',//填写后台菜单url名称和方法
//         'group'=>'已装插件',//不用改变
//         'type'=>'Chart'    //填写自己的插件名字
//      );
//      //添加到数据库
//       if(M('Menu')->add($data)){
//       	return true;
//       }else{
//       	return false;
//       }
return true;
	}
	public function uninstall(){
	//删除后台添加的菜单，如果没有直接返回真
	$map['type']='Chart'; 
	  if(M('Menu')->where($map)->delete()){
	  	return true;
	  }else{
	  	return false;
	  }
	}
	public function tijiao(){
	$this->success('提交成功');
	}
	public function set(){
		//插件工菜单后台设置,没有的话直接返回真
	    if(IS_POST){
	      $data=array(
	      	'title'=>I('post.title'),
	        'url'=>I('post.url'),
	        'sort'=>I('post.sort')
	      );
 	     $model=M('Addons');
        $result= $model->where("mark='Chart'")->save(array('param'=>json_encode($data)));	  	
			if(0<$result){
				$this->success('保存成功');	
			}else{
				$this->error('保存失败');			
			}	    	
	    	}else{
	    	    $data=M('Addons')->field('param')->where("mark='Chart'")->find();
	  		   $this->assign('info',json_decode($data['param'],true));
	  			  $str=$this->fetch('config');
     		   return $str;
	    }

	}
}
