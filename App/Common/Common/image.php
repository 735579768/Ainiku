<?php
/**
 *给目标图片添加水印
//使用示例
markimg(array(
	'dst'=>'./images/1.jpg',//原始图像
	'src'=>'./images/ico.png',//水印图像
	'pos'=>'center'//水印位置('left,right,center'),
	'str'=>'水印文字'
));
 */
function markimg($info=array(
					'det'=>null,
					'src'=>null,
					'pos'=>'right',
					'str'=>''
					)){
		//原始图像 
       $dst =$info['dst'] ;//"./images/tu.png"; //注意图片路径要正确 
	   //水印图像 
       $src = $info['src'];//"./images/ico.png"; //注意路径要写对
		//水印在原图的位置比例
		$pos=$info['pos'];
       //得到原始图片信息 
       $dst_info = getimagesize($dst);
	   //检查图片大小符合添加水印的条件不
	   $tj=explode('X',C('SHUIYIN_TIAOJIAN'));  
       if(!($dst_info[0]>=intval($tj[0]) && $dst_info[1]>=intval($tj[1])))return false;
	   switch ($dst_info[2]) 
       { 
        case 1: $dst_im =imagecreatefromgif($dst);break;
        case 2: $dst_im =imagecreatefromjpeg($dst);break;
        case 3: $dst_im =imagecreatefrompng($dst);break; 
        case 6: $dst_im =imagecreatefromwbmp($dst);break; 
        default: return("不支持的文件类型1"); 
       } 
	
	 if($info['str']!=''){
		  $font_size = 14;
		  $fontname = 'C:\WINDOWS\Fonts\simkai.ttf';
		  $black = imagecolorallocate($dst_im,0,0,0);
		  $arr=imagettfbbox($font_size,0,$fontname,$info['str']);
		  $hh=abs($arr[7]-$arr[1]);
		  $ww=abs($arr[2]-$arr[0]);
	   switch($pos){
		   case 'right':
		   //右下角
			 imagettftext($dst_im ,$font_size,0,$dst_info[0]-$ww-10,$dst_info[1]-$hh+10,$black,$fontname,$info['str']);
		   break;
		   case 'center':
		   //正中间
			 imagettftext($dst_im ,$font_size,0,($dst_info[0]-$ww-10)/2,$dst_info[1]-$hh+10,$black,$fontname,$info['str']);
			break; 
		   default :
		   //左下角
		    imagettftext($dst_im ,$font_size,0,10,$dst_info[1]-$hh+10,$black,$fontname,$info['str']);
		   }
		 
	}else{
		 //添加图片水印
       $src_info = getimagesize($src); 
       switch ($src_info[2]) 
       { 
        case 1: $src_im =imagecreatefromgif($src);break;    
        case 2: $src_im =imagecreatefromjpeg($src);break;   
        case 3: $src_im =imagecreatefrompng($src);break;
        case 6: $src_im =imagecreatefromwbmp($src);break; 
        default: return("不支持的文件类型1"); 
       } 
       //支持png本身透明度的方式
	   switch($pos){
		   case 'right':
		   //右下角
	  	   imagecopy($dst_im,$src_im,$dst_info[0]-$src_info[0]-10,$dst_info[1]-$src_info[1]-10,0,0,$src_info[0],$src_info[1]);
		   break;
		   case 'center':
		   //正中间
			imagecopy($dst_im,$src_im,($dst_info[0]-$src_info[0])/2,($dst_info[1]-$src_info[1])/2,0,0,$src_info[0],$src_info[1]);
			break; 
		   default :
		   //左下角
			imagecopy($dst_im,$src_im,10,$dst_info[1]-$src_info[1]-10,0,0,$src_info[0],$src_info[1]);
		   }
	}
	   //保存图片 
       switch ($dst_info[2]){ 
        case 1: imagegif($dst_im,$dst);break;
        case 2: imagejpeg($dst_im,$dst);break; 
        case 3: imagepng($dst_im,$dst);break;    
        case 6: imagewbmp($dst_im,$dst);break; 
        default: 
        return("不支持的文件类型2"); 
       } 
       imagedestroy($dst_im); 
       imagedestroy($src_im);   	
	   return true;
	}
/**
 * 生成缩略图
 * @author yangzhiguo0903@163.com
 * @param string     源图绝对完整地址{带文件名及后缀名}
 * @param string     目标图绝对完整地址{带文件名及后缀名}
 * @param int        缩略图宽{0:此时目标高度不能为0，目标宽度为源图宽*(目标高度/源图高)}
 * @param int        缩略图高{0:此时目标宽度不能为0，目标高度为源图高*(目标宽度/源图宽)}
 * @param bool proportion  剪切true  缩放false会有白边框
 * @param bool createimg  true输出到浏览器 false输出到文件
 * @return boolean
 */
function img2thumb($src_img, $dst_img, $width = 75, $height = 75,$createimg=true,$proportion = true)
{
    if(!is_file($src_img))
    {
        return false;
    }
	$to='';
	if(!empty($dst_img)){
		$ot =strtolower(pathinfo($dst_img, PATHINFO_EXTENSION));
		$dirname =pathinfo($dst_img, PATHINFO_DIRNAME);
		if(!file_exists($dirname))createFolder($dirname);
	}else{
		$ot =strtolower(pathinfo($src_img, PATHINFO_EXTENSION));
		}
    $otfunc = 'image' . ($ot == 'jpg' ? 'jpeg' : $ot);
    $srcinfo = getimagesize($src_img);
    $src_w = $srcinfo[0];
    $src_h = $srcinfo[1];
	//if($src_h<intval(C('THUMB_HEIGHT')) || $src_w<intval(C('THUMB_WIDTH'))){return false;}
    $type  = strtolower(substr(image_type_to_extension($srcinfo[2]), 1));
    $createfun = 'imagecreatefrom' . ($type == 'jpg' ? 'jpeg' : $type);
 if(!$createimg)header('content-type:image/'.($type == 'jpg' ? 'jpeg' : $type));
    $dst_h = $height;
    $dst_w = $width;
    $x = $y = 0;
$src_image=$createfun($src_img);
imagesavealpha($src_image,true);//这里很重要;
$cropped_image = imagecreatetruecolor($width, $height);
imagealphablending($cropped_image,false);//这里很重要,意思是不合并颜色,直接用$img图像颜色替换,包括透明色;
imagesavealpha($cropped_image,true);//这里很重要,意思是不要丢了$thumb图像的透明色

$white = imagecolorallocate($cropped_image, 255, 255, 255);
$alpha= imagecolorallocatealpha($cropped_image,255,255,255,127);
imagefill($cropped_image, 0, 0, $alpha);
 if($proportion && $src_w>$width && $src_h>$height){
	 //源图比缩略图大的情况
	 if($width==$height){
		 //缩略图宽高一样的情况
		 		//源图宽高一样
				
				if($src_h==$src_w){
						   imagecopyresampled($cropped_image, $src_image,0,0, 0, 0, $width, $height, $src_w, $src_h);
							 if($createimg){$otfunc($cropped_image, $dst_img);}else{$otfunc($cropped_image);}
							imagedestroy($cropped_image);
							imagedestroy($src_image);
   							 return true;					 
					}
					
				 if($src_w>$src_h){
						 //如果源图大于缩略图
						 $ww=$src_h;
						 $_x=($src_w-$src_h)/2; 
						   imagecopyresampled($cropped_image, $src_image,0,0, $_x, 0, $width, $height, $ww, $ww);
							 if($createimg){$otfunc($cropped_image, $dst_img);}else{$otfunc($cropped_image);}
							imagedestroy($cropped_image);
							imagedestroy($src_image);
   							 return true;					 
					 }else{
							 $ww=$src_w;
							 $_y=($src_h-$src_w)/2; 
						   imagecopyresampled($cropped_image, $src_image,0,0, 0, $_y, $width, $height, $ww, $ww);
							 if($createimg){$otfunc($cropped_image, $dst_img);}else{$otfunc($cropped_image);}
							imagedestroy($cropped_image);
							imagedestroy($src_image);
   							 return true;					 
						 } 
		 }else{
			 //缩略图宽高不一样的情况
			 if($width>$height){
				 	$bili=($width/$height);
					$hh=$src_w/$bili;
					$_y=($src_h-$hh)/2;
				 	if($src_w>$src_h){
							//查找合适剪切的高
							$tem_w=$src_w;
							$tem_h=0;
							while(true){
								$tem_h=($tem_w/$bili);
								if($tem_h<$src_h){break;}else{$tem_w--;}
								}
							$_x=($src_w-$tem_w)/2;
							$_y=($src_h-$tem_h)/2;
						   imagecopyresampled($cropped_image, $src_image,0,0, $_x, $_y, $width, $height, $tem_w, $tem_h);
							 if($createimg){$otfunc($cropped_image, $dst_img);}else{$otfunc($cropped_image);}
							imagedestroy($cropped_image);
							imagedestroy($src_image);
   							 return true;						 
						}else{						
						   imagecopyresampled($cropped_image, $src_image,0,0, 0, $_y, $width, $height, $src_w, $hh);
							 if($createimg){$otfunc($cropped_image, $dst_img);}else{$otfunc($cropped_image);}
							imagedestroy($cropped_image);
							imagedestroy($src_image);
   							 return true;								
							}
				 }else{
					 $bili=($height/$width);
					$ww=$src_h/$bili;
					$_x=($src_w-$ww)/2;
				 	if($src_h>$src_w){
							//查找合适剪切的高
							$tem_h=$src_h;
							$tem_w=0;
							while(true){
								$tem_w=($tem_h/$bili);
								if($tem_w<$src_w){break;}else{$tem_h--;}
								}
							$_x=($src_w-$tem_w)/2;
							$_y=($src_h-$tem_h)/2;
						   imagecopyresampled($cropped_image, $src_image,0,0, $_x, 0, $width, $height, $tem_w, $tem_h);
							 if($createimg){$otfunc($cropped_image, $dst_img);}else{$otfunc($cropped_image);}
							imagedestroy($cropped_image);
							imagedestroy($src_image);
   							 return true;		
						}else{						
						   imagecopyresampled($cropped_image, $src_image,0,0, $_x, 0, $width, $height, $ww, $src_h);
 							if($createimg){$otfunc($cropped_image, $dst_img);}else{$otfunc($cropped_image);}
							imagedestroy($cropped_image);
							imagedestroy($src_image);
   							 return true;								
							}					 
					 }
			 }
	 }else{
		  //源图任意一个长度比缩略图小的情况
		  if($src_h<$height && $src_w<$width){
			  $_x=($width-$src_w)/2;
			  $_y=($height-$src_h)/2;
			  imagecopyresampled($cropped_image, $src_image,$_x, $_y,0, 0, $src_w, $src_h, $src_w, $src_h);
			  }else if($src_h<$height){
				   $hh=$width/($src_w/$src_h);
				   $_y=($height-$hh)/2;
				    imagecopyresampled($cropped_image, $src_image,0, $_y,0, 0, $width, $src_h, $src_w, $src_h);
				  }else{
				   $ww=$height/($src_h/$src_w);
				   $_x=($width-$ww)/2;
				    imagecopyresampled($cropped_image, $src_image,$_x, 0,0, 0, $src_w, $height,$src_w, $src_h);					  
					  }				   
 							if($createimg){$otfunc($cropped_image, $dst_img);}else{$otfunc($cropped_image);}
							imagedestroy($cropped_image);
							imagedestroy($src_image);
   							 return true;								 
		 }
}
///**
// * 生成缩略图
// * @author yangzhiguo0903@163.com
// * @param string     源图绝对完整地址{带文件名及后缀名}
// * @param string     目标图绝对完整地址{带文件名及后缀名}
// * @param int        缩略图宽{0:此时目标高度不能为0，目标宽度为源图宽*(目标高度/源图高)}
// * @param int        缩略图高{0:此时目标宽度不能为0，目标高度为源图高*(目标宽度/源图宽)}
// * @param int        是否裁切{宽,高必须非0}
// * @param int/float  缩放{0:不缩放, 0<this<1:缩放到相应比例(此时宽高限制和裁切均失效)}
// * @return boolean
// */
//function imgtothumb($src_img, $dst_img, $width = 75, $height = 75, $cut = 0, $proportion = 0)
//{
//    if(!is_file($src_img))
//    {
//        return false;
//    }
//    $ot =strtolower(pathinfo($dst_img, PATHINFO_EXTENSION));
//	$dirname =pathinfo($dst_img, PATHINFO_DIRNAME);
//	if(!file_exists($dirname))createFolder($dirname);
//	
//    $otfunc = 'image' . ($ot == 'jpg' ? 'jpeg' : $ot);
//    $srcinfo = getimagesize($src_img);
//    $src_w = $srcinfo[0];
//    $src_h = $srcinfo[1];
//	//if($src_h<intval(C('THUMB_HEIGHT')) || $src_w<intval(C('THUMB_WIDTH'))){return false;}
//    $type  = strtolower(substr(image_type_to_extension($srcinfo[2]), 1));
//    $createfun = 'imagecreatefrom' . ($type == 'jpg' ? 'jpeg' : $type);
// 
//    $dst_h = $height;
//    $dst_w = $width;
//    $x = $y = 0;
// 
////    /**
////     * 缩略图不超过源图尺寸（前提是宽或高只有一个）
////     */
////    if(($width> $src_w && $height> $src_h) || ($height> $src_h && $width == 0) || ($width> $src_w && $height == 0))
////    {
////        $proportion = 1;
////    }
////    if($width> $src_w)
////    {
////        $dst_w = $width = $src_w;
////    }
////    if($height> $src_h)
////    {
////        $dst_h = $height = $src_h;
////    }
//// 
////    if(!$width && !$height && !$proportion)
////    {
////        return false;
////    }
////    if(!$proportion)
////    {
////        if($cut == 0)
////        {
////            if($dst_w && $dst_h)
////            {
////                if($dst_w/$src_w> $dst_h/$src_h)
////                {
////                    $dst_w = $src_w * ($dst_h / $src_h);
////                    $x = 0 - ($dst_w - $width) / 2;
////                }
////                else
////                {
////                    $dst_h = $src_h * ($dst_w / $src_w);
////                    $y = 0 - ($dst_h - $height) / 2;
////                }
////            }
////            else if($dst_w xor $dst_h)
////            {
////                if($dst_w && !$dst_h)  //有宽无高
////                {
////                    $propor = $dst_w / $src_w;
////                    $height = $dst_h  = $src_h * $propor;
////                }
////                else if(!$dst_w && $dst_h)  //有高无宽
////                {
////                    $propor = $dst_h / $src_h;
////                    $width  = $dst_w = $src_w * $propor;
////                }
////            }
////        }
////        else
////        {
////            if(!$dst_h)  //裁剪时无高
////            {
////                $height = $dst_h = $dst_w;
////            }
////            if(!$dst_w)  //裁剪时无宽
////            {
////                $width = $dst_w = $dst_h;
////            }
////            $propor = min(max($dst_w / $src_w, $dst_h / $src_h), 1);
////            $dst_w = (int)round($src_w * $propor);
////            $dst_h = (int)round($src_h * $propor);
////            $x = ($width - $dst_w) / 2;
////            $y = ($height - $dst_h) / 2;
////        }
////    }
////    else
////    {
////        $proportion = min($proportion, 1);
////        $height = $dst_h = $src_h * $proportion;
////        $width  = $dst_w = $src_w * $proportion;
////    }
// 
//    $src = $createfun($src_img);
//	
//    $dst = imagecreatetruecolor($dst_w, $dst_h);
//   //$white = imagecolorallocate($dst, 255, 255, 255);
//   //imagefill($dst, 0, 0, $white);
//   $alpha = imagecolorallocatealpha($dst, 255, 255, 255, 127);
//	imagealphablending($dst ,false);//关闭混合模式，以便透明颜色能覆盖原画布
//	imagefill($dst, 0, 0, $alpha);;//填充
//	imagesavealpha($dst ,true);//这里很重要,意思是不要丢了$thumb图像的透明色	
////	
//
// 
//    if(function_exists('imagecopyresampled'))
//    {
//        imagecopyresampled($dst, $src, $x, $y, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
//    }
//    else
//    {
//        imagecopyresized($dst, $src, $x, $y, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
//    }
//    $otfunc($dst, $dst_img);
//    imagedestroy($dst);
//    imagedestroy($src);
//    return true;
//}
/**
 * BMP 创建函数
 * @author simon
 * @param string $filename path of bmp file
 * @example who use,who knows
 * @return resource of GD
 */
function imagecreatefrombmp( $filename ){
    if ( !$f1 = fopen( $filename, "rb" ) )
        return FALSE;
     
    $FILE = unpack( "vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread( $f1, 14 ) );
    if ( $FILE['file_type'] != 19778 )
        return FALSE;
     
    $BMP = unpack( 'Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel' . '/Vcompression/Vsize_bitmap/Vhoriz_resolution' . '/Vvert_resolution/Vcolors_used/Vcolors_important', fread( $f1, 40 ) );
    $BMP['colors'] = pow( 2, $BMP['bits_per_pixel'] );
    if ( $BMP['size_bitmap'] == 0 )
        $BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];
    $BMP['bytes_per_pixel'] = $BMP['bits_per_pixel'] / 8;
    $BMP['bytes_per_pixel2'] = ceil( $BMP['bytes_per_pixel'] );
    $BMP['decal'] = ($BMP['width'] * $BMP['bytes_per_pixel'] / 4);
    $BMP['decal'] -= floor( $BMP['width'] * $BMP['bytes_per_pixel'] / 4 );
    $BMP['decal'] = 4 - (4 * $BMP['decal']);
    if ( $BMP['decal'] == 4 )
        $BMP['decal'] = 0;
     
    $PALETTE = array();
    if ( $BMP['colors'] < 16777216 ){
        $PALETTE = unpack( 'V' . $BMP['colors'], fread( $f1, $BMP['colors'] * 4 ) );
    }
     
    $IMG = fread( $f1, $BMP['size_bitmap'] );
    $VIDE = chr( 0 );
     
    $res = imagecreatetruecolor( $BMP['width'], $BMP['height'] );
    $P = 0;
    $Y = $BMP['height'] - 1;
    while( $Y >= 0 ){
        $X = 0;
        while( $X < $BMP['width'] ){
            if ( $BMP['bits_per_pixel'] == 32 ){
                $COLOR = unpack( "V", substr( $IMG, $P, 3 ) );
                $B = ord(substr($IMG, $P,1));
                $G = ord(substr($IMG, $P+1,1));
                $R = ord(substr($IMG, $P+2,1));
                $color = imagecolorexact( $res, $R, $G, $B );
                if ( $color == -1 )
                    $color = imagecolorallocate( $res, $R, $G, $B );
                $COLOR[0] = $R*256*256+$G*256+$B;
                $COLOR[1] = $color;
            }elseif ( $BMP['bits_per_pixel'] == 24 )
                $COLOR = unpack( "V", substr( $IMG, $P, 3 ) . $VIDE );
            elseif ( $BMP['bits_per_pixel'] == 16 ){
                $COLOR = unpack( "n", substr( $IMG, $P, 2 ) );
                $COLOR[1] = $PALETTE[$COLOR[1] + 1];
            }elseif ( $BMP['bits_per_pixel'] == 8 ){
                $COLOR = unpack( "n", $VIDE . substr( $IMG, $P, 1 ) );
                $COLOR[1] = $PALETTE[$COLOR[1] + 1];
            }elseif ( $BMP['bits_per_pixel'] == 4 ){
                $COLOR = unpack( "n", $VIDE . substr( $IMG, floor( $P ), 1 ) );
                if ( ($P * 2) % 2 == 0 )
                    $COLOR[1] = ($COLOR[1] >> 4);
                else
                    $COLOR[1] = ($COLOR[1] & 0x0F);
                $COLOR[1] = $PALETTE[$COLOR[1] + 1];
            }elseif ( $BMP['bits_per_pixel'] == 1 ){
                $COLOR = unpack( "n", $VIDE . substr( $IMG, floor( $P ), 1 ) );
                if ( ($P * 8) % 8 == 0 )
                    $COLOR[1] = $COLOR[1] >> 7;
                elseif ( ($P * 8) % 8 == 1 )
                    $COLOR[1] = ($COLOR[1] & 0x40) >> 6;
                elseif ( ($P * 8) % 8 == 2 )
                    $COLOR[1] = ($COLOR[1] & 0x20) >> 5;
                elseif ( ($P * 8) % 8 == 3 )
                    $COLOR[1] = ($COLOR[1] & 0x10) >> 4;
                elseif ( ($P * 8) % 8 == 4 )
                    $COLOR[1] = ($COLOR[1] & 0x8) >> 3;
                elseif ( ($P * 8) % 8 == 5 )
                    $COLOR[1] = ($COLOR[1] & 0x4) >> 2;
                elseif ( ($P * 8) % 8 == 6 )
                    $COLOR[1] = ($COLOR[1] & 0x2) >> 1;
                elseif ( ($P * 8) % 8 == 7 )
                    $COLOR[1] = ($COLOR[1] & 0x1);
                $COLOR[1] = $PALETTE[$COLOR[1] + 1];
            }else
                return FALSE;
            imagesetpixel( $res, $X, $Y, $COLOR[1] );
            $X++;
            $P += $BMP['bytes_per_pixel'];
        }
        $Y--;
        $P += $BMP['decal'];
    }
    fclose( $f1 );
     
    return $res;
}
/** 
* 创建bmp格式图片 
* 
* @author: legend(legendsky@hotmail.com) 
* @link: http://www.ugia.cn/?p=96 
* @description: create Bitmap-File with GD library 
* @version: 0.1 
* 
* @param resource $im          图像资源 
* @param string   $filename    如果要另存为文件，请指定文件名，为空则直接在浏览器输出 
* @param integer  $bit         图像质量(1、4、8、16、24、32位) 
* @param integer  $compression 压缩方式，0为不压缩，1使用RLE8压缩算法进行压缩 
* 
* @return integer 
*/ 
function imagebmp(&$im, $filename = '', $bit = 8, $compression = 0) 
{ 
    if (!in_array($bit, array(1, 4, 8, 16, 24, 32))) 
    { 
        $bit = 8; 
    } 
    else if ($bit == 32) // todo:32 bit 
    { 
        $bit = 24; 
    } 
  
    $bits = pow(2, $bit); 
    
    // 调整调色板 
    imagetruecolortopalette($im, true, $bits); 
    $width  = imagesx($im); 
    $height = imagesy($im); 
    $colors_num = imagecolorstotal($im); 
    
    if ($bit <= 8) 
    { 
        // 颜色索引 
        $rgb_quad = ''; 
        for ($i = 0; $i < $colors_num; $i ++) 
        { 
            $colors = imagecolorsforindex($im, $i); 
            $rgb_quad .= chr($colors['blue']) . chr($colors['green']) . chr($colors['red']) . "\0"; 
        } 
        
        // 位图数据 
        $bmp_data = ''; 
  
        // 非压缩 
        if ($compression == 0 || $bit < 8) 
        { 
            if (!in_array($bit, array(1, 4, 8))) 
            { 
                $bit = 8; 
            } 
  
            $compression = 0; 
            
            // 每行字节数必须为4的倍数，补齐。 
            $extra = ''; 
            $padding = 4 - ceil($width / (8 / $bit)) % 4; 
            if ($padding % 4 != 0) 
            { 
                $extra = str_repeat("\0", $padding); 
            } 
            
            for ($j = $height - 1; $j >= 0; $j --) 
            { 
                $i = 0; 
                while ($i < $width) 
                { 
                    $bin = 0; 
                    $limit = $width - $i < 8 / $bit ? (8 / $bit - $width + $i) * $bit : 0; 
  
                    for ($k = 8 - $bit; $k >= $limit; $k -= $bit) 
                    { 
                        $index = imagecolorat($im, $i, $j); 
                        $bin |= $index << $k; 
                        $i ++; 
                    } 
  
                    $bmp_data .= chr($bin); 
                } 
                
                $bmp_data .= $extra; 
            } 
        } 
        // RLE8 压缩 
        else if ($compression == 1 && $bit == 8) 
        { 
            for ($j = $height - 1; $j >= 0; $j --) 
            { 
                $last_index = "\0"; 
                $same_num   = 0; 
                for ($i = 0; $i <= $width; $i ++) 
                { 
                    $index = imagecolorat($im, $i, $j); 
                    if ($index !== $last_index || $same_num > 255) 
                    { 
                        if ($same_num != 0) 
                        { 
                            $bmp_data .= chr($same_num) . chr($last_index); 
                        } 
  
                        $last_index = $index; 
                        $same_num = 1; 
                    } 
                    else 
                    { 
                        $same_num ++; 
                    } 
                } 
  
                $bmp_data .= "\0\0"; 
            } 
            
            $bmp_data .= "\0\1"; 
        } 
  
        $size_quad = strlen($rgb_quad); 
        $size_data = strlen($bmp_data); 
    } 
    else 
    { 
        // 每行字节数必须为4的倍数，补齐。 
        $extra = ''; 
        $padding = 4 - ($width * ($bit / 8)) % 4; 
        if ($padding % 4 != 0) 
        { 
            $extra = str_repeat("\0", $padding); 
        } 
  
        // 位图数据 
        $bmp_data = ''; 
  
        for ($j = $height - 1; $j >= 0; $j --) 
        { 
            for ($i = 0; $i < $width; $i ++) 
            { 
                $index  = imagecolorat($im, $i, $j); 
                $colors = imagecolorsforindex($im, $index); 
                
                if ($bit == 16) 
                { 
                    $bin = 0 << $bit; 
  
                    $bin |= ($colors['red'] >> 3) << 10; 
                    $bin |= ($colors['green'] >> 3) << 5; 
                    $bin |= $colors['blue'] >> 3; 
  
                    $bmp_data .= pack("v", $bin); 
                } 
                else 
                { 
                    $bmp_data .= pack("c*", $colors['blue'], $colors['green'], $colors['red']); 
                } 
                
                // todo: 32bit; 
            } 
  
            $bmp_data .= $extra; 
        } 
  
        $size_quad = 0; 
        $size_data = strlen($bmp_data); 
        $colors_num = 0; 
    } 
  
    // 位图文件头 
    $file_header = "BM" . pack("V3", 54 + $size_quad + $size_data, 0, 54 + $size_quad); 
  
    // 位图信息头 
    $info_header = pack("V3v2V*", 0x28, $width, $height, 1, $bit, $compression, $size_data, 0, 0, $colors_num, 0); 
    
    // 写入文件 
    if ($filename != '') 
    { 
        $fp = fopen("test.bmp", "wb"); 
  
        fwrite($fp, $file_header); 
        fwrite($fp, $info_header); 
        fwrite($fp, $rgb_quad); 
        fwrite($fp, $bmp_data); 
        fclose($fp); 
  
        return 1; 
    } 
    
    // 浏览器输出 
    header("Content-Type: image/bmp"); 
    echo $file_header . $info_header; 
    echo $rgb_quad; 
    echo $bmp_data; 
    
    return 1; 
} 