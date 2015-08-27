<?php
namespace  Plugins\Outlink;
require_once __ROOT_PATH__.'/Plugins/Plugin.class.php';
class OutlinkPlugin extends \Plugins\Plugin{
	protected   $config=array(
            		'version'=>'1.0',
            	    'author'=>'作者',
            	    'name'=>'外链分析',
            	    'descr'=>'外链分析工具'
            	 );
	  public function __construct(){
		 \Plugins\Plugin::__construct();
		 Vendor('PHPExcel.PHPExcel');
		 Vendor('PHPExcel.PHPExcel.IOFactory');
		 Vendor('PHPExcel.PHPExcel.Reader.Excel5');
		 Vendor('PHPExcel.PHPExcel.Worksheet.Drawing');
		 Vendor('PHPExcel.PHPExcel.Style.Fill');		  
		  }
	//钩子默认的调用方法
	public function run(){
	
	}
	public function fenxi(){
		$othernum=0;
		$str='';
		if(IS_POST){
			$arr=$this->importtext();
			$data=array();
			
			foreach($arr as $val){
				$v=explode(',',$val);
				$k=toutf8(preg_replace('/(\.\w+)\/.*/i','$1',$v[1]));
				$data[$k]=isset($data[$k])?(++$data[$k]):1;
				}
				//去掉小于1的链接		
			foreach($data as $key=>$val){
				if($val<=1){
				$othernum++;
				}else{
					$str.=empty($str)?("['$key($val)', $val]"):(",['$key($val)', $val]");
					}
				}
			//$str.=(",['小于10的外链的站点总共 $othernum 个', $othernum]");
			}
		$this->assign('other',"小于10个外链的站点总共 $othernum 个,已过滤不显示。");
		$this->assign('data',$str);
		return $this->fetch('fenxi');
		}
private function importtext(){
	$file='';$filetempname='';
	if($_FILES)
		{
			$file = $_FILES['excel']['name'];
			$filetempname= $_FILES['excel']['tmp_name'];
		}else{
			$this->error('请选择文件后再上传');
			}
	   //自己设置的上传文件存放路径
		$filePath =RUNTIME_PATH;
		$str = "";   
	
		//注意设置时区
		$time=date("y-m-d-H-i-s");//去当前上传的时间 
		//获取上传文件的扩展名
		$extend=strrchr($file,'.');
		//上传后的文件名
		$name=$time.$extend;
		$uploadfile=$filePath.$name;//上传后的文件名地址 
		//move_uploaded_file() 函数将上传的文件移动到新位置。若成功，则返回 true，否则返回 false。
		$result=move_uploaded_file($filetempname,$uploadfile);//假如上传到当前目录下
		//echo $result;
		if($result) //如果上传文件成功，就执行导入excel操作
		{
				$str=file_get_contents($uploadfile);
				$temarr=preg_split('/\n/',$str);
				return $temarr;
				//$tema=explode("\n",$str);
				//dump($tema);
		}
	}
private function importExcel(){
	$file='';$filetempname='';
	if($_FILES)
		{
			$file = $_FILES['excel']['name'];
			$filetempname= $_FILES['excel']['tmp_name'];
		}else{
			$this->error('请选择文件后再上传');
			}
	   //自己设置的上传文件存放路径
		$filePath =RUNTIME_PATH;
		$str = "";   
	
		//注意设置时区
		$time=date("y-m-d-H-i-s");//去当前上传的时间 
		//获取上传文件的扩展名
		$extend=strrchr($file,'.');
		//上传后的文件名
		$name=$time.$extend;
		$uploadfile=$filePath.$name;//上传后的文件名地址 
		//move_uploaded_file() 函数将上传的文件移动到新位置。若成功，则返回 true，否则返回 false。
		$result=move_uploaded_file($filetempname,$uploadfile);//假如上传到当前目录下
		//echo $result;
		if($result) //如果上传文件成功，就执行导入excel操作
		{
			include "conn.php";
			$objReader = \PHPExcel_IOFactory::createReader('Excel5');//use excel2007 for 2007 format 
			$objPHPExcel = $objReader->load($uploadfile); 
			$sheet = $objPHPExcel->getSheet(0); 
			$highestRow = $sheet->getHighestRow();           //取得总行数 
			$highestColumn = $sheet->getHighestColumn(); //取得总列数
			
			$colarr=array();
			$fieldarr=array();
			//循环读取excel文件,读取一条,插入一条
			for($j=1;$j<=$highestRow;$j++)                        //从第一行开始读取数据
			{ 
				
				$tem=array();
				for($k='A';$k<=$highestColumn;$k++)            //从A列读取数据
				{ 	
					
					//这种方法简单，但有不妥，以'\\'合并为数组，再分割\\为字段值插入到数据库
					//实测在excel中，如果某单元格的值包含了\\导入的数据会为空        
					//getValue 取内容   getCalculatedValue 取工式结果
					$str=$objPHPExcel->getActiveSheet()->getCell("$k$j")->getCalculatedValue();//读取单元格
					if(empty($str)&& $k=='A')break;
					if($j==1){
					   $fieldarr[$k]=$str;
					}else{
						$tem[$fieldarr[$k]]=$str;
						}
				} 
				if($j!==1 && !empty($tem))$colarr[]=$tem;
			}  
			unlink($uploadfile); //删除上传的excel文件
			return $colarr;
		}
		else
		{
		   $this->error("导入失败！");
		} 
		}
	public function getConfig(){
		return $this->config;
	}
    public  function install(){
			$prefix=C('DB_PREFIX');
$sql = <<<sql
				DROP TABLE IF EXISTS `{$prefix}outlink`;
				CREATE TABLE `{$prefix}outlink` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `url1` varchar(255) DEFAULT NULL,
				  `url2` varchar(255) DEFAULT NULL,
				  `mark` varchar(255) DEFAULT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;
sql;
		$arr=explode(';',$sql);
		foreach($arr as $val){
			if(!empty($val))M()->execute($val);
			}
    	//向后台添加菜单，如果不添加的话直接返回真
      $data=array(
      	 'title'=>'外链分析',//插件后台菜单名字
         'pid'=>ADDONS_MENU,//不用改变
         'url'=>'Addons/plugin?pn=Outlink&pm=fenxi',//填写后台菜单url名称和方法
         'group'=>'已装插件',//不用改变
         'type'=>'Outlink'    //填写自己的插件名字
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
						DROP TABLE IF EXISTS `{$prefix}outlink`;
sql;
				$arr=explode(';',$sql);
				foreach($arr as $val){
					if(!empty($val))M()->execute($val);
					}

	//删除后台添加的菜单，如果没有直接返回真
	$map['type']='Outlink'; 
	  if(M('Menu')->where($map)->delete()){
	  	return true;
	  }else{
	  	return false;
	  }
	}
	public function set(){
	    if(IS_POST){
				  $data=I('Outlink');
				 $model=M('Addons');
				$result= $model->where("mark='Outlink'")->save(array('param'=>json_encode($data)));	  	
					if(0<$result){
						$this->success('保存成功');	
					}else{
						$this->error('保存失败');			
					}	    	
	}else{
	    	   $data=M('Addons')->field('param')->where("mark='Outlink'")->find();
	  		   $this->assign('info',json_decode($data['param'],true));
	  		   $str=$this->fetch('config');
     		   return $str;
	    }
	}

}
