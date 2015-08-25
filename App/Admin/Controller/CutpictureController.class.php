<?php
// | Author: 枫叶 <735579768@qq.com> <http://www.zhaokeli.com>
// +----------------------------------------------------------------------
namespace Admin\Controller;
if(!defined("ACCESS_ROOT"))die("Invalid access");
/**
 * 文件控制器
 * 主要用于下载模型的文件上传和下载
 */
class CutpictureController extends AdminController {
	public function  cutp(){
			//取配置的比例
			$bili=floatval($_POST['shearphoto']);
			$ShearPhoto["config"]=array(
			"proportional"=>$bili,//比例截图，JS端也要相应设置哦，不然系统会给你抱出错误,不设比例填0，如填比例 ：3/4  代表宽和高的比例是3/4
			"width"=>array(
						 //array(0,true),//此时的0   代表以用户取当时截取框的所截的大小为宽
						 array(150,true),//@参数1要生成的宽 （高度不用设，系统会按比例做事），    @参数2：是否为该图加水印,water参数要有水印地址才有效true或false
						// array(100,true),//@参数1要生成的宽 （高度不用设，系统会按比例做事），   @参数2：是否为该图加水印，water参数要有水印地址才有效true或false
						// array(70,true)
						 ),//你可以继续增加多张照片
			"water"=>"../images/waterimg2.png",//只接受PNG水印，当然你对PHP熟练，你可以对主程序进行修改		   
			"water_scope"=>100,       //图片少于多少不添加水印！没填水印地址，这里不起任何作用
			"temp"=>DATA_DIR_PATH."cutpicturefile".DIRECTORY_SEPARATOR."temp",  //等待截图的大图文件。就是上传图片的临时目录，截图后，图片会被删除
			"tempSaveTime"=>600,//临时图片（也就是temp内的图片）保存时间，需要永久保存请设为0。单位秒
			"saveURL"=>"./Uploads/image/cutfile".DIRECTORY_SEPARATOR.date('Ymd'),//后面不要加斜杠，系统会自动给补上！不要使用中文
			"filename"=>uniqid("cutpicture_")."_".mt_rand(100,999)."_"//文件名字定义！要生成多个文件时 系统会自动在后面补0 1 2  3.....;
			);		
$ShearPhoto["JSdate"]=isset($_POST["JSdate"])?json_decode(trim(stripslashes($_POST["JSdate"])),true):die('{"erro":"致命错误"}');	


$Shear =new ShearPhoto;//类实例开始
$result = $Shear->run($ShearPhoto["JSdate"],$ShearPhoto["config"]);//传入参数运行
if($result===false){       //切图失败时
 echo '{"erro":"'.$Shear->erro.'"}';            //把错误发给JS /请匆随意更改"erro"的编写方式，否则JS出错
}
else //切图成功时
 {
	 $dirname=pathinfo($ShearPhoto["JSdate"]["url"]);
	 $ShearPhotodirname=$dirname["dirname"].DIRECTORY_SEPARATOR."shearphoto.lock";//认证删除的密钥
	 file_exists($ShearPhotodirname) && @unlink($ShearPhoto["JSdate"]["url"]);//密钥存在，当然就删掉原图
	 //保存数据到数据库
	 foreach($result as $key=>$val){
		  $result[$key]['status']=1;
		  		$imgurl=str_replace(array('./','\\'),array('/','/'),$val['ImgUrl']);
		  		$data['path']=$imgurl;
				$data['sha1']=sha1_file();
				$data['thumbpath']=$imgurl;
				$data['destname']=$val['ImgName'];
				$data['srcname']=$val['ImgName'];
				$data['create_time']=time();
				$data['uid']=UID;
					$model=M('picture');
					if($model->create($data)){
						$re=$model->add($data);
						if($re>0){
							$result[$key]['id']=$re;
						}
					}
		 }
	
	 $result = json_encode($result); 
	  echo str_replace(array("\\\\","\/",ShearURL,"\\",'./'),array("\\","/","","/",'/'),$result);//去掉无用的字符修正URL地址，再把数据传弟给JS
      /*
     到此程序已运行完毕，并成功！你可以在这里愉快地写下你的逻辑代码
	 $result[X]["ImgUrl"] //图片路径  X是数字
	 $result[X]["ImgName"] //图片文件名字  X是数字
	 $result[X]["ImgWidth"]//图片宽度    X是数字
	 $result[X]["ImgHeight"] //图片高度    X是数字
	 用var_dump($result)展开，你便一目了然！  
      */
     //ShearPhoto 作者:明哥先生 QQ399195513		
  }		
		}
		
		
		function uploadf(){
			//取配置的比例
			$bili=floatval($_POST['shearphoto']);
			$ShearPhoto["config"]=array(
			"proportional"=>$bili,//比例截图，JS端也要相应设置哦，不然系统会给你抱出错误,不设比例填0，如填比例 ：3/4  代表宽和高的比例是3/4
			"width"=>array(
						 //array(0,true),//此时的0   代表以用户取当时截取框的所截的大小为宽
						 array(150,true),//@参数1要生成的宽 （高度不用设，系统会按比例做事），    @参数2：是否为该图加水印,water参数要有水印地址才有效true或false
						 array(100,true),//@参数1要生成的宽 （高度不用设，系统会按比例做事），   @参数2：是否为该图加水印，water参数要有水印地址才有效true或false
						 array(70,true)
						 ),//你可以继续增加多张照片
			"water"=>"../images/waterimg2.png",//只接受PNG水印，当然你对PHP熟练，你可以对主程序进行修改		   
			"water_scope"=>100,       //图片少于多少不添加水印！没填水印地址，这里不起任何作用
			"temp"=>DATA_DIR_PATH."cutpicturefile".DIRECTORY_SEPARATOR."temp",  //等待截图的大图文件。就是上传图片的临时目录，截图后，图片会被删除
			"tempSaveTime"=>600,//临时图片（也就是temp内的图片）保存时间，需要永久保存请设为0。单位秒
			"saveURL"=>"./Uploads/cutfile".DIRECTORY_SEPARATOR."shearphoto_file",//后面不要加斜杠，系统会自动给补上！不要使用中文
			"filename"=>uniqid("shearphoto_")."_".mt_rand(100,999)."_"//文件名字定义！要生成多个文件时 系统会自动在后面补0 1 2  3.....;
			);	
			
			 $ini_set = array(
			'max_size' => 2 * 1024 * 1024,  //文件大小限制设置  M单位
			'out_time' => 20,                //上传超时设置
			'list' =>  $ShearPhoto["config"]["temp"].DIRECTORY_SEPARATOR, //上传路径
			'whitelist' => array(
						   ".jpeg",
						   ".gif",
						   ".png",
						   ".jpg")//上传的文件后缀
		 );
		/*设置部份结束*/
		ini_set('max_execution_time', $ini_set['out_time']);

		register_shutdown_function('errobug'); //注册FUNCTION,接收系统致命错误
		error_reporting(0); //关闭错误提示
		if (!isset($_FILES['UpFile'])) {
			$this->HandleError();
		}
		if (isset($_FILES['UpFile']['error']) && $_FILES['UpFile']['error'] != 0) {
			$uploadErrors = array(
				0 => '文件上传成功',
				1 => '上传的文件超过了 php.ini 文件中的 upload_max_filesize directive 里的设置',
				2 => '上传的文件超过了 HTML form 文件中的 MAX_FILE_SIZE directive 里的设置',
				3 => '上传的文件仅为部分文件',
				4 => '没有文件上传',
				6 => '缺少临时文件夹'
			);
			$this->HandleError($uploadErrors[$_FILES['UpFile']['error']]);
		}
		if (!isset($_FILES['UpFile']['tmp_name']) || !@is_uploaded_file($_FILES['UpFile']['tmp_name'])) {
			$this->HandleError('无法找到上传的文件，上传失败');
		 }
		if (!isset($_FILES['UpFile']['name'])) {
			$this->HandleError('上传空名字文件名');
		}
		$POST_MAX_SIZE = ini_get('post_max_size');
		$unit = strtoupper(substr($POST_MAX_SIZE, -1));
		$multiplier = $unit == 'M' ? 1048576 : ($unit == 'K' ? 1024 : ($unit == 'G' ? 1073741824 : 1));
		if ((int)$_SERVER['CONTENT_LENGTH'] > $multiplier * (int)$POST_MAX_SIZE && $POST_MAX_SIZE) {
			$this->HandleError('超过POST_MAX_SIZE的设置值，请查看PHP.ini的设置');
		}
		$file_size = @filesize($_FILES['UpFile']['tmp_name']);
		if (!$file_size || $file_size > $ini_set['max_size']) {
		  $this->HandleError('零字节文件 或 上传的文件已经超过所设置最大值');
		}
		$UpFile = array();
		$type = getimagesize($_FILES['UpFile']['tmp_name']); //验证是否真图片！这是1.3升级修的BUG，先前版本没判断是否真图，有点败笔
		$type = image_type_to_extension($type[2]);
		if (!in_array(strtolower($type) , $ini_set['whitelist'])) {
			HandleError('不允许上传此类型文件');
		}
		$type==".jpeg" && ($type=".jpg");
		$UpFile['filename']=uniqid("temp_")."_".mt_rand(100,999).$type;
		
		$UpFile['file_url'] = $ini_set['list'] . $UpFile['filename'];
		
		file_exists($ini_set['list']) or @mkdir($ini_set['list'], 511,true);
		
		if (!move_uploaded_file($_FILES['UpFile']['tmp_name'], $UpFile['file_url'])) {
			HandleError('文件保存失败');
		}
		/*
		来到这里时，已经代表上传成功，你可以在这里尽情写的你逻辑
		*/
		echo('{"success":"'.str_replace(array("\\\\","\/",ShearURL,"\\",'./'),array("\\","/","","/",'/'),$UpFile['file_url']).'"}');		
			
			}
		function errobug() {
			$e = error_get_last();
			$e['type'] > 0 and $e['type'] != 8 and HandleError();
		}
		function HandleError($erro = '系统错误') {
			 die('{"erro":"'.$erro.'"}');
		}
}



 class ShearPhoto {
    public $erro = false;
    protected function rotate($src, $R) {
        $arr = array(
            -90, 
            -180, 
            -270
        );
        if (in_array($R, $arr)) {
			 $rotatesrc = imagerotate($src, $R, 0);
            imagedestroy($src);
        } else {
            return $src;
        }
      return $rotatesrc;
    }
	
    protected function delTempImg($temp,$deltime) {
		if($deltime==0)  return;
	$dir = opendir($temp);
	$time=time();
	   while (($file = readdir($dir)) !== false)
       {
	      if($file!="." and $file!=".." and $file!="shearphoto.lock"){
		     $fileUrl= $temp.DIRECTORY_SEPARATOR.$file;
	         $pastTime=$time-filemtime($fileUrl);
	          if($pastTime<0 || $pastTime>$deltime)@unlink($fileUrl);
	      }
       }
           closedir($dir);
	}
    public function run($JSconfig, $PHPconfig) {
	 $tempurl=$PHPconfig["temp"].DIRECTORY_SEPARATOR."shearphoto.lock";
	!file_exists($tempurl)	&& file_put_contents($tempurl,"ShearPhoto Please don't delete");
	 $this->delTempImg($PHPconfig["temp"],$PHPconfig["tempSaveTime"]);
	 if (!isset($JSconfig["url"]) || 
    !isset($JSconfig["R"]) || !isset($JSconfig["X"]) || !isset($JSconfig["Y"]) || !isset($JSconfig["IW"]) || !isset($JSconfig["IH"]) || !isset($JSconfig["P"]) || !isset($JSconfig["FW"]) || !isset($JSconfig["FH"]) ) {
     $this->erro = "服务端接收到的数据缺少参数";
            return false;
        } 
		
		
		
        if (!file_exists($JSconfig["url"])) { 
            $this->erro = "此图片路径有误";
            return false;
        }
	
		 
        foreach ($JSconfig as $k => $v) { 
            if ($k !== "url") {
				if (!is_numeric($v)) {
                    $this->erro = "传递的参数有误";
                    return false;
                }
            }
        } //验证是否为字除了url
        if ($PHPconfig["proportional"] !== floatval($JSconfig["P"])) {
            $this->erro = "JS设置的比例和PHP设置不一致";
            return false;
        }
        list($w, $h, $type) = getimagesize($JSconfig["url"]); //验证是否真图片！
        $strtype = image_type_to_extension($type);
        $type_array = array(
            ".jpeg",
            ".gif",
            ".png",
            ".jpg"
        );
        if (!in_array(strtolower($strtype) , $type_array)) {
            $this->erro = "无法读取图片";
            return false;
        }  
		if($JSconfig["R"]==-90 || $JSconfig["R"]==-270){ list($w,$h)= array($h,$w);}
		return $this->createshear($PHPconfig, $w, $h, $type, $strtype, $JSconfig);
    }
    protected function createshear($PHPconfig, $w, $h, $type, $strtype, $JSconfig) {
        switch ($type) {
            case 1:  
                $src = @imagecreatefromgif($JSconfig["url"]);
                break;

            case 2:  
                $src = @imagecreatefromjpeg($JSconfig["url"]);
				
                break;

            case 3:  
                $src = @imagecreatefrompng($JSconfig["url"]);
                break;

            default:
                return false;
                break;
				 
				
        }
        $src = $this->rotate($src, $JSconfig["R"]);
		
			 
        $dest = imagecreatetruecolor($JSconfig["IW"], $JSconfig["IH"]); 
        imagecopy($dest, $src, 0, 0, $JSconfig["X"],  $JSconfig["Y"], $w, $h);
	     imagedestroy($src);
        return $this->compression($dest, $PHPconfig, $JSconfig["IW"], $JSconfig["IH"], $type, $strtype, $JSconfig);
    }
    protected function CreateArray($PHPconfig, $JSconfig, $strtype) {
        $arr = array();
        if ($PHPconfig["proportional"] > 0) {
            $proportion = $PHPconfig["proportional"];
        } else {
            $proportion = $JSconfig["IW"] / $JSconfig["IH"];
        }
		  
		  if (isset($PHPconfig["water"]) &&  $PHPconfig["water"]  && file_exists($PHPconfig["water"])) {
              $water_or = true;
        }else{
			 $water_or=false;
		}
		if(!file_exists($PHPconfig["saveURL"])) 
		if(!mkdir($PHPconfig["saveURL"],0777,true)){
			$this->erro = "目录权限有问题";
            return false;
			}
        foreach ($PHPconfig["width"] as $k => $v) {
            ($v[0] == 0) and ($v[0] = $JSconfig["FW"]);
            $height = $v[0] / $proportion;
            $strtype == ".jpeg" and $strtype = ".jpg";
            $file_url = $PHPconfig["saveURL"] .DIRECTORY_SEPARATOR. $PHPconfig["filename"] . $k . $strtype;
            $water = ($v[1] === true && $water_or === true) ? true : false;
            $arr[$k] = array(
                $v[0],
                $height,
                $file_url,
                $water
            );
        }
        return $arr;
    }
    protected function compression($DigShear,$PHPconfig, $w, $h, $type, $strtype, $JSconfig) {
		$arrimg=$this->CreateArray($PHPconfig, $JSconfig, $strtype);
		if(!$arrimg) return false;
        $zip_photo = new zip_img(array(
            "dest" => $DigShear,
            "water" => $PHPconfig["water"],
            "water_scope" => $PHPconfig["water_scope"],
            "w" => $w,
            "h" => $h,
            "type" => $type,
            "strtype" => $strtype,
            "zip_array" => $arrimg
        ));
        return $zip_photo->run();
    }
}
  
  
  class zip_img {
    protected $arg;
    protected $waterimg = false;
    protected $GDfun = false;
	protected $result =array();
    final function __construct($arg) {
        $this->arg = $arg;
        if (isset($arg["water"]) and $arg["water"] and file_exists($arg["water"])) {
            list($W, $H, $type) = getimagesize($arg["water"]);
            if ($type == 3) {
                $this->waterimg = array(
                    imagecreatefrompng($arg["water"]) ,
                    $W,
                    $H
                );
            }
        }
        switch ($arg["type"]) {
            case 1: 
                $this->GDfun = "imagegif";
                break;

            case 2: 
                $this->GDfun = "imagejpeg";
                break;

            case 3: 
                $this->GDfun = "imagepng";
                break;

            default:
                break;
        }
    }
    protected function zip_img($dest, $width, $height, $save_url, $water) {
        $createsrc = imagecreatetruecolor($width, $height);
        imagecopyresampled($createsrc, $dest, 0, 0, 0, 0, $width, $height, $this->arg["w"], $this->arg["h"]);
        $water === true and $createsrc = $this->add_water($createsrc, $width, $height);
        $this->saveimg($createsrc,$save_url,$width, $height);
    }
    protected function add_water($src, $width, $height) {
        if ($this->waterimg and is_numeric($this->arg["water_scope"]) and $width > $this->arg["water_scope"] and $height > $this->arg["water_scope"]) {
            imagecopy($src, $this->waterimg[0], $width - $this->waterimg[1] - 10, $height - $this->waterimg[2] - 10, 0, 0, $this->waterimg[1], $this->waterimg[2]);
        }
        return $src;
    }
    protected function saveimg($createsrc, $save_url,$width, $height) {
        @call_user_func($this->GDfun, $createsrc, $save_url);
        imagedestroy($createsrc);
		array_push($this->result,array("ImgUrl"=>$save_url, "ImgName"=>basename($save_url),"ImgWidth"=>$width,"ImgHeight"=>$height));
    }
    final function __destruct() {
        @imagedestroy($this->arg["dest"]);
        $this->waterimg[0] and @imagedestroy($this->waterimg[0]);
	 }
    public function run() {
        $dest = $this->arg["dest"];
        $zip_array = $this->arg["zip_array"];
        foreach ($zip_array as $k => $v) {
            list($width, $height, $save_url, $water) = $v;
            $this->zip_img($dest, $width, $height, $save_url, $water);
        }
            return $this->result;
    }
}
