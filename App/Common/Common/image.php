<?php
function image_water($src_img = '', $water_img = '', $dest_img = '', $water_text = '', $pos = 'bottom_right') {
	$isshuiyin = intval(C('SHUIYIN_ON'));
	if ($isshuiyin === 0) {
		return true;
	}

	if (!file_exists($src_img)) {
		return '文件不存在';
	}
	$image = new \Think\Image();
	empty($pos) && ($pos = intval(C('SHUIYIN_POS')));
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

	$result = false;
	if (empty($water_text)) {
		if (!file_exists($water_img)) {
			return '水印图片文件不存在';
		}
		$result = $image->open($src_img)->water($water_img, $pos, 80)->save($dest_img);
	} else {
		//文字水印
		$color     = C('SHUIYIN_TEXT_COLOR');
		$color     = empty($color) && ($color = '#000000');
		$font_size = intval(C('SHUIYIN_TEXT_SIZE'));
		$result    = $image->open($src_img)->text($water_text, '', $font_size, $color, $pos)->save($dest_img);

	}
	if ($result) {
		return true;
	} else {
		return '未知错误';
	}
}
/**
 * 生成缩略图
 * @param  [type]  $src_img [源图片]
 * @param  [type]  $dst_img [缩略图路径]
 * @param  integer $width   [缩略图宽]
 * @param  integer $height  [缩略图高]
 * @return [type]           [返回图片对象]
 */
function create_thumb($src_img, $dst_img, $width = 75, $height = 75) {
	if (!file_exists($src_img)) {
		return '文件不存在';
	}
	$image = new \Think\Image();
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
	// $result=$image->thumb($width, $height)->save($dst_img);

	//居中剪切
	$result = $image->thumb($width, $height, \Think\Image::IMAGE_THUMB_CENTER)->save($dst_img);

	//左上剪切
	// $result=$image->thumb($width, $height, \Think\Image::IMAGE_THUMB_NORTHWEST)->save($dst_img);

	//缩放填充
	//$result = $image->thumb($width, $height, \Think\Image::IMAGE_THUMB_FILLED)->save($dst_img);

	//固定大小
	// $result=$image->thumb($width, $height, \Think\Image::IMAGE_THUMB_FIXED)->save($dst_img);
	if ($result) {
		return true;
	} else {
		return '未知错误';
	}
}