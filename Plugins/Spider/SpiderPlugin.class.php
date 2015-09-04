<?php
namespace  Plugins\Spider;
require_once pathA('/Plugins/Plugin.class.php');
class SpiderPlugin extends \Plugins\Plugin{
	protected   $config=array(
            		'version'=>'1.0',
            	    'author'=>'作者',
            	    'name'=>'蜘蛛访问记录',
            	    'descr'=>'蜘蛛访问'
            	 );
	private $spiderinfo=array(
									'baidu'=>array('name'=>'baidu','title'=>'百度'),
									'google'=>array('name'=>'google','title'=>'谷歌'),
									'360'=>array('name'=>'360','title'=>'360'),
									'sousou'=>array('name'=>'sousou','title'=>'搜搜'),
									'sougou'=>array('name'=>'sougou','title'=>'搜狗'),
									'youdao'=>array('name'=>'youdao','title'=>'有道'),
									'yahoo'=>array('name'=>'yahoo','title'=>'雅虎')
		);
	//钩子默认的调用方法
	public function run(){
		
		//当前一天0点时间
		$curday=strtotime(date(NOW_TIME,'Y/m/d').'00:00:00');
		$onehour=strtotime('2015-06-01 01:00:00')-strtotime('2015-06-01 00:00:00');
		
		
	     $data=M('Addons')->field('param')->where("mark='Spider'")->find();
	  	 $sp=json_decode($data['param'],true);
		$spiderarr=array();
//		$sparr=array(
//									'baidu'=>array('name'=>'baidu','title'=>'百度'),
//									'google'=>array('name'=>'google','title'=>'谷歌'),
//									'360'=>array('name'=>'360','title'=>'360'),
//									'sousou'=>array('name'=>'sousou','title'=>'搜搜'),
//									'sougou'=>array('name'=>'sougou','title'=>'搜狗'),
//									'youdao'=>array('name'=>'youdao','title'=>'有道'),
//									'yahoo'=>array('name'=>'yahoo','title'=>'雅虎')
//		);
		foreach($sp as $val){
			$spiderarr[]=$this->spiderinfo[$val];
			}
		//查询每个小时的访问量
		$data=array();
		//初始0点访问量
		foreach($spiderarr as $val)$data[$val['name']][0]=0;
		//设置坐标点
		$_y=0;
		//查询百度谷歌蜘蛛
		for($i=1;$i<24;$i++){
			$map['create_time']=array(array('gt',$curday),array('lt',$curday+$onehour),'and');
			
			foreach($spiderarr as $val){
					$map['spider_name']=$val['name'];
					$data[$val['name']][$i]=M('Spider')->field('sum(views) views')->where($map)->select();
					$data[$val['name']][$i]=empty($data[$val['name']][$i][0]['views'])?0:$data[$val['name']][$i][0]['views'];
					if($data[$val['name']][$i]>$_y)$_y=$data[$val['name']][$i];				
				}
				$curday+=$onehour;					
			}
		//平均Y轴
		$_y=($_y<6)?6:$_y;
		$dijia=(($_y%6)>0)?(($_y+(6-($_y%6)))/6):($_y/6);
		for($i=0;$i<=6*$dijia;$i+=$dijia){
			$data['_y'][]=$i;
			}
		 $this->assign('spider',$spiderarr);
		 $this->assign('data',$data);	
		  $this->display('content');	
	}
	public function lists(){
		$starttime=strtotime(I('starttime'));
		$endtime=strtotime(I('endtime'));
		$map=array();
			$field=array(
					'start'=>array(
						'field'=>'starttime',
						'name'=>'starttime',
						'type'=>'datetime',
						'title'=>'开始时间',
						'note'=>'',
						'extra'=>null,
						'is_show'=>3,
						'value'=>$starttime
					),
					'end'=>array(
						'field'=>'endtime',
						'name'=>'endtime',
						'type'=>'datetime',
						'title'=>'结束时间',
						'note'=>'',
						'extra'=>null,
						'is_show'=>3,
						'value'=>$endtime
					)
			);
	$this->assign('fieldarr',$field);
	$this->assign('data',null);

		if($starttime!=$endtime){
		if(!empty($starttime) && !empty($endtime)){
			$map['__DB_PREFIX__spider.create_time']=array(array('gt',$starttime),array('lt',$endtime),'and');
			}else if(!empty($starttime)){
				$map['__DB_PREFIX__spider.create_time']=array('gt',$starttime);
			}else if(!empty($endtime)){
			$map['__DB_PREFIX__spider.create_time']=array('lt',$endtime);
				}
		}

	 $data=M('Addons')->field('param')->where("mark='Spider'")->find();
	  $sp=json_decode($data['param'],true);
	  $spiderarr=array();
		foreach($sp as $val){
			$spiderarr[]=$this->spiderinfo[$val];
			}	  
		$this->assign('spinfo',$spiderarr);	
		$spidername=I('spider_name');
		$map['spider_name']=array('like',"%$spidername%");
		$this->pages(array(
						'where'=>$map,
						'model'=>'Spider',
						'order'=>'id desc'
		));
		 return $this->fetch('lists');	
		}
	public function delall(){
		$result=M('Spider')->where('1=1')->delete();
		if($result>0){
			$this->success('清空成功');
			}else{
			$this->error('清空失败');	
				}
		}
	public  function addinfo(){
		$mar=$this->get_naps_bot();
	   if($mar!==false){
		   $data['spider_name']=strtolower($mar);
		   $data['url']=$_SERVER['REQUEST_URI'];
		   $data['ip']=get_client_ip();
			$data['location'] =  getIpLocation($data['ip']);
		   //$result=M('Spider')->where($data)->setInc('views');
		   //if(!$result){
			   $data['views']=1;
			   $data['create_time']=NOW_TIME;
			   M('Spider')->add($data);
		   //}
		   }		
		}
	public function getConfig(){
		return $this->config;
	}
    public  function install(){
			$prefix=C('DB_PREFIX');
$sql = <<<sql
				DROP TABLE IF EXISTS `{$prefix}spider`;
				CREATE TABLE `{$prefix}spider` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `spider_name` varchar(255) DEFAULT NULL,
				  `spider_title` varchar(255) DEFAULT NULL,
				  `url` varchar(255) DEFAULT NULL,
				  `views` int(11)  NULL DEFAULT 0 ,
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
      	 'title'=>'蜘蛛访问',//插件后台菜单名字
         'pid'=>ADDONS_MENU,//不用改变
         'url'=>'Addons/plugin?pn=Spider&pm=lists',//填写后台菜单url名称和方法
         'group'=>'已装插件',//不用改变
         'type'=>'Spider'    //填写自己的插件名字
      );
      //添加到数据库
       if(M('Menu')->add($data)){
       	return true;
       }else{
       	return false;
       }
	}        

	public function uninstall(){
		$prefix=C('DB_PREFIX');
		$sql = <<<sql
						DROP TABLE IF EXISTS `{$prefix}spider`;
sql;
				$arr=explode(';',$sql);
				foreach($arr as $val){
					if(!empty($val))M()->execute($val);
					}

	//删除后台添加的菜单，如果没有直接返回真
	$map['type']='Spider'; 
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
	    if(IS_POST){
				  $data=I('spider');
				 $model=M('Addons');
				$result= $model->where("mark='Spider'")->save(array('param'=>json_encode($data)));	  	
					if(0<$result){
						$this->success('保存成功');	
					}else{
						$this->error('保存失败');			
					}	    	
	}else{
	    	   $data=M('Addons')->field('param')->where("mark='Spider'")->find();
	  		   $this->assign('info',json_decode($data['param'],true));
	  		   $str=$this->fetch('config');
     		   return $str;
	    }
	}
function addspiderlog(){
		$searchbot = get_naps_bot(); //判断是不是蜘蛛
		$url=$_SERVER['HTTP_REFERER']; //来源网站
		//下面判断如果是来自百度的用户或是你网站内部的链接
		if ($searchbot || ($url!='' and strpos($url,'baidu.com')!==false)|| strpos($url, '你网站的域名')) {
		//符合的要求的链接可以进入你的网站
		}else{
		//不符合的话就显示提示信息
		 
		die();
		}
	}

function get_naps_bot(){
		 $data=M('Addons')->field('param')->where("mark='Spider'")->find();
	  	 $sp=json_decode($data['param'],true);
		 $restr=false;
			$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
			if (strpos($useragent, 'googlebot') !== false){
			$restr='google';
			}
		if (strpos($useragent, '360Spider') !== false || strpos($useragent, 'haosouspider') !== false ){
			$restr='360';
			}
			if (strpos($useragent, 'baiduspider') !== false){
			$restr='baidu';
			}
			if (strpos($useragent, 'msnbot') !== false){
			$restr='bing';
			}
			if (strpos($useragent, 'slurp') !== false){
			$restr= 'yahoo';
			}
			if (strpos($useragent, 'sosospider') !== false){
			$restr='soso';
			}
			if (strpos($useragent, 'sogou spider') !== false){
			$restr='sogou';
			}
			if (strpos($useragent, 'yodaobot') !== false){
			$restr='yodao';
			}
			if(in_array($restr,$sp)){
				return $restr;
				}else{
				return false;
					}
			
	}
}
