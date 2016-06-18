<?php
/**
 *给目标图片添加水印
 *使用示例
 *markimg(array(
 *'dst'=>'./images/1.jpg',//原始图像
 *'src'=>'./images/ico.png',//水印图像
 *'pos'=>'center'//水印位置('left,right,center'),
 *'str'=>'水印文字'
 *));
 */
function markimg($info = array(
	'det' => null,
	'src' => null,
	'pos' => 'bottom_right',
	'str' => '',
)) {
	$isshuiyin = intval(C('SHUIYIN_ON'));
	if ($isshuiyin === 0) {
		return true;
	}
// IMAGE_WATER_NORTHWEST =   1 ; //左上角水印
	// IMAGE_WATER_NORTH     =   2 ; //上居中水印
	// IMAGE_WATER_NORTHEAST =   3 ; //右上角水印
	// IMAGE_WATER_WEST      =   4 ; //左居中水印
	// IMAGE_WATER_CENTER    =   5 ; //居中水印
	// IMAGE_WATER_EAST      =   6 ; //右居中水印
	// IMAGE_WATER_SOUTHWEST =   7 ; //左下角水印
	// IMAGE_WATER_SOUTH     =   8 ; //下居中水印
	// IMAGE_WATER_SOUTHEAST =   9 ; //右下角水印
	$image = new \Think\Image('Imagick');
	// $src_w = $image->width(); // 返回图片的宽度
	// $src_h = $image->height(); // 返回图片的高度
	// $type  = $image->type(); // 返回图片的类型
	// $mime  = $image->mime(); // 返回图片的mime类型
	// $size  = $image->size(); // 返回图片的尺寸数组 0 图片宽度 1 图片高度
	$info['pos'] = empty($info['pos']) && ($info['pos'] = intval(C('SHUIYIN_POS')));
	switch ($pos) {
	case 'top_left':
		$pos = 1;
		break;

	case 'top_center':
		$pos = 2;
		break;

	case 'top_right':
		$pos = 3;
		break;

	case 'left_center':
		$pos = 4;
		break;

	case 'center_center':
		$pos = 5;
		break;

	case 'right_center':
		$pos = 6;
		break;

	case 'bottom_left':
		$pos = 7;
		break;

	case 'bottom_center':
		$pos = 8;
		break;

	case 'bottom_right':
		$pos = 9;
		break;

	default:
		$pos = 1;
	}

	if (empty($info['str'])) {
		//左上
		$image->open($info['dst'])->water($info['src'], $pos, 50)->save($info['dst']);
	} else {
		//文字水印
		$color     = C('SHUIYIN_TEXT_COLOR');
		$color     = empty($color) && ($color = '#000000');
		$font_size = intval(C('SHUIYIN_TEXT_SIZE'));
		$image->open($info['dst'])->text($info['str'], '', $font_size, $color, $pos)->save($info['dst']);
	}
}
/**
 * 生成缩略图
 * @author yangzhiguo0903@163.com
 * @param string     源图绝对完整地址{带文件名及后缀名}
 * @param string     目标图绝对完整地址{带文件名及后缀名}
 * @param int        缩略图宽{0:此时目标高度不能为0，目标宽度为源图宽*(目标高度/源图高)}
 * @param int        缩略图高{0:此时目标宽度不能为0，目标高度为源图高*(目标宽度/源图宽)}
 * @param bool createimg  true输出到浏览器 false输出到文件
 * @param bool proportion  剪切true  缩放false会有白边框
 * @return boolean
 */
function img2thumb($src_img, $dst_img, $width = 75, $height = 75, $createimg = true, $proportion = false) {
	$image = new \Think\Image('Imagick');
	$image->open($src_img);
	// $src_w = $image->width(); // 返回图片的宽度
	// $src_h = $image->height(); // 返回图片的高度
	// $type  = $image->type(); // 返回图片的类型
	// $mime  = $image->mime(); // 返回图片的mime类型
	// $size  = $image->size(); // 返回图片的尺寸数组 0 图片宽度 1 图片高度

	//IMAGE_THUMB_SCALE     =   1 ; //等比例缩放类型 默认
	// IMAGE_THUMB_FILLED    =   2 ; //缩放后填充类型
	// IMAGE_THUMB_CENTER    =   3 ; //居中裁剪类型
	// IMAGE_THUMB_NORTHWEST =   4 ; //左上角裁剪类型
	// IMAGE_THUMB_SOUTHEAST =   5 ; //右下角裁剪类型
	// IMAGE_THUMB_FIXED     =   6 ; //固定尺寸缩放类型
	//等 比例缩放
	$image->thumb($width, $height)->save($dst_img);

	//居中剪切
	$image->thumb($width, $height, \Think\Image::IMAGE_THUMB_CENTER)->save($dst_img);

	//左上剪切
	$image->thumb($width, $height, \Think\Image::IMAGE_THUMB_NORTHWEST)->save($dst_img);

	//缩放填充
	$image->thumb($width, $height, \Think\Image::IMAGE_THUMB_FILLED)->save($dst_img);

	//固定大小
	$image->thumb($width, $height, \Think\Image::IMAGE_THUMB_FIXED)->save($dst_img);
}