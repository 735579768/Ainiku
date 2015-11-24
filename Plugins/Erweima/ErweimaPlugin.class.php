<?php
namespace  Plugins\Erweima;
require_once ADDONS_PATH.'Plugin.class.php';  
require_once './Plugins/Erweima/phpqrcode.php';
class ErweimaPlugin extends \Plugins\Plugin{
	protected   $config=array(
            		'version'=>'1.0',
            	    'author'=>'qiaokeli',
            	    'name'=>'二维码',
            	    'descr'=>'生成二维码'
            	 );
	//钩子默认的调用方法
	public function create($whsize=200,$content='',$logo='./Plugins/Erweima/logo.jpg'){
//		$content=I('content');
//		$whsize=I('size');
		//'http://www.zhaokeli.com赵克立'; //二维码内容 
		$value = empty($content)?'http://www.59vip.cn':$content;  
		$errorCorrectionLevel = 'H';//容错级别L(7%)M(15%)Q(25%)H(30%)
		$imgsize=empty($whsize)?200:$whsize;//图片的大小(像素)   
		$matrixPointSize = $imgsize/37;//生成图片大小   
		//生成二维码图片 
		$filename= IMAGE_CACHE_DIR.'erweima/qrcode.png';
		createfolder(dirname($filename));
		\QRcode::png($value,$filename, $errorCorrectionLevel, $matrixPointSize, 2);   
		//$logo = './Plugins/Erweima/logo.jpg';//准备好的logo图片   
		$QR = $filename;//已经生成的原始二维码图 
		$logobg='./Plugins/Erweima/yjx.jpg';//圆角图片  
		$QR = imagecreatefromstring(file_get_contents($QR)); 
		
		if($logo !== false)
		{
			//把logo处理成圆角的
			//取圆角图片大小
			$logobg = imagecreatefromstring(file_get_contents($logobg));	
			$logo = imagecreatefromstring(file_get_contents($logo));
		 
			$logobg_width=imagesx($logobg);
			$logobg_height=imagesy($logobg);
		 
			$logo_width = imagesx($logo);
			$logo_height = imagesy($logo);
			//logo需要是正方形
			if($logo_width>$logo_height){
				$logo_width=$logo_height;
			}else{
				$logo_height=$logo_width;
				}
			imagecopyresampled($logobg, $logo, 10, 10, 0, 0, $logobg_width-20, $logobg_height-20, $logo_width, $logo_height);
			
			$QR_width = imagesx($QR);
			$QR_height = imagesy($QR); 
			
			$logo = $logobg;//imagecreatefromstring(file_get_contents('logo1.png'));
			
			$logo_width = imagesx($logo);
			$logo_height = imagesy($logo);
			
			$logo_qr_width = $QR_width / 3;//中间的logo大小
			$scale = $logo_width / $logo_qr_width;
			$logo_qr_height = $logo_height / $scale;
			$from_width = ($QR_width - $logo_qr_width) / 2;
			$from_height=($QR_height - $logo_qr_height)/2;
		 
			imagecopyresampled($QR, $logo, $from_width, $from_height, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
		}
		header('Content-type: image/png');
		imagepng($QR);
		imagedestroy($QR);
		die();
	}
	public function getConfig(){
		return $this->config;
	}
    public  function install(){
    	//向后台添加菜单，如果不添加的话直接返回真
      $data=array(
      	 'title'=>'二维码',//插件后台菜单名字
         'pid'=>ADDONS_MENU,//不用改变
         'url'=>'Addons/plugin?pn=Erweima&pm=set',//填写后台菜单url名称和方法
         'group'=>'已装插件',//不用改变
         'type'=>'Erweima'    //填写自己的插件名字
      );
      //添加到数据库
       if(M('Menu')->add($data)){
       	return true;
       }else{
       	return false;
       }
		return true;
	}
	public function uninstall(){
//	//删除后台添加的菜单，如果没有直接返回真
//	$map['type']='Test'; 
//	  if(M('Menu')->where($map)->delete()){
//	  	return true;
//	  }else{
//	  	return false;
//	  }
//	}
//	public function tijiao(){
//	$this->success('提交成功');
		return true;
	}
	public function set(){
		return $this->fetch('content');
	}
}


