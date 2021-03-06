<?php
/**
 * 给图片添加水印
 * @param  string $src_img    要添加水印的图片路径
 * @param  string $water_img  水印图片路径
 * @param  string $dest_img   添加好水印后的图片路径
 * @param  string $water_text 文字水印
 * @param  string $pos        水印位置
 * @return [type]             返回 boolean类型,成功返回true,失败返回字符串
 */
function image_water($src_img = '', $water_img = '', $dest_img = '', $water_text = '', $pos = 'bottom_right') {
	$isshuiyin = intval(C('SHUIYIN_ON'));
	if ($isshuiyin == 0) {
		return true;
	}
	if (!is_file($src_img)) {
		return '不是文件类型';
	}
	if (!file_exists($src_img)) {
		return '文件不存在';
	}
	//创建目录
	create_folder(dirname($dst_img));
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
		$color     = hex_torgb($color);
		$font_size = intval(C('SHUIYIN_TEXT_SIZE'));
		//使用验证码的随机字体
		$fontpath = '';
		$ttfPath  = THINK_PATH . 'Library/Think/Verify/' . (false ? 'zhttfs' : 'ttfs') . '/';

		$dir  = dir($ttfPath);
		$ttfs = array();
		while (false !== ($file = $dir->read())) {
			if ($file[0] != '.' && (substr($file, -4) == '.ttf' || substr($file, -4) == '.ttf')) {
				$ttfs[] = $file;
			}
		}
		$dir->close();
		$fontpath = $ttfs[array_rand($ttfs)];
		$fontpath = $ttfPath . $fontpath;

		$result = $image->open($src_img)->text($water_text, $fontpath, $font_size, $color, $pos)->save($dest_img);

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
	if (!is_file($src_img)) {
		return '不是文件类型';
	}
	if (!file_exists($src_img)) {
		return '文件不存在';
	}
	//创建目录
	create_folder(dirname($dst_img));
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
	$model  = C('THUMB_MODEL');
	$result = null;
	switch ($model) {
	case '1':
		//等 比例缩放
		$result = $image->thumb($width, $height)->save($dst_img);
		break;
	case '2':
		//缩放填充
		$result = $image->thumb($width, $height, \Think\Image::IMAGE_THUMB_FILLED)->save($dst_img);

		break;
	case '3':
		//居中剪切
		$result = $image->thumb($width, $height, \Think\Image::IMAGE_THUMB_CENTER)->save($dst_img);

		break;
	case '4':
		//左上剪切
		$result = $image->thumb($width, $height, \Think\Image::IMAGE_THUMB_NORTHWEST)->save($dst_img);

		break;
	case '5':
		//右下角裁剪类型
		$result = $image->thumb($width, $height, \Think\Image::IMAGE_THUMB_SOUTHEAST)->save($dst_img);

		break;
	case '6':
		//固定大小
		$result = $image->thumb($width, $height, \Think\Image::IMAGE_THUMB_FIXED)->save($dst_img);
		break;

	default:
		//等 比例缩放
		$result = $image->thumb($width, $height)->save($dst_img);
		break;
	}

	if ($result) {
		return true;
	} else {
		return '未知错误';
	}
}