<?php
namespace Plugins\Erweima;
require_once ADDONS_PATH . 'Plugin.class.php';
require_once './Plugins/Erweima/phpqrcode.php';
class ErweimaPlugin extends \Plugins\Plugin {
	protected $config = array(
		'version' => '1.0',
		'author'  => 'qiaokeli',
		'name'    => '二维码',
		'descr'   => '生成二维码',
	);
	//钩子默认的调用方法
	public function create($whsize = 200, $content = '', $logo = './Plugins/Erweima/logo.jpg') {
/*		$im = $this->imgradius();
header('Content-type: image/png');
imagepng($im);
imagedestroy($im);*/

//		$content=I('content');
		//		$whsize=I('size');
		//'http://www.zhaokeli.com赵克立'; //二维码内容
		$value                = empty($content) ? 'http://www.59vip.cn' : $content;
		$errorCorrectionLevel = 'H'; //容错级别L(7%)M(15%)Q(25%)H(30%)
		$imgsize              = empty($whsize) ? 200 : $whsize; //图片的大小(像素)
		$matrixPointSize      = $imgsize / 37; //生成图片大小
		//生成二维码图片
		$filename = IMAGE_CACHE_DIR . 'erweima/qrcode.png';
		createfolder(dirname($filename));
		\QRcode::png($value, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
		//$logo = './Plugins/Erweima/logo.jpg';//准备好的logo图片
		$QR = $filename; //已经生成的原始二维码图

		$logobg = './Plugins/Erweima/yjx.jpg'; //圆角图片
		$QR     = imagecreatefromstring(file_get_contents($QR));

		if ($logo !== false) {
			//把logo处理成圆角的
			//取圆角图片大小
			//$logobg = imagecreatefromstring(file_get_contents($logobg));
			$logobg = imagecreatetruecolor(200, 200);
			$white  = imagecolorallocate($logobg, 255, 255, 255);
			imagefill($logobg, 0, 0, $white);

			$logo = imagecreatefromstring(file_get_contents($logo));
			//处理成圆角
			$logo          = $this->imgradius($logo);
			$logobg_width  = imagesx($logobg);
			$logobg_height = imagesy($logobg);

			$logo_width  = imagesx($logo);
			$logo_height = imagesy($logo);
			//logo需要是正方形
			if ($logo_width > $logo_height) {
				$logo_width = $logo_height;
			} else {
				$logo_height = $logo_width;
			}

			imagecopyresampled($logobg, $logo, 15, 15, 0, 0, $logobg_width - 30, $logobg_height - 30, $logo_width, $logo_height);

/*			header('Content-type: image/png');
imagepng($logobg);
imagedestroy($logobg);*/
			$QR_width  = imagesx($QR);
			$QR_height = imagesy($QR);

			$logo = $logobg; //imagecreatefromstring(file_get_contents('logo1.png'));

			$logo_width  = imagesx($logo);
			$logo_height = imagesy($logo);

			$logo_qr_width  = $QR_width / 3; //中间的logo大小
			$scale          = $logo_width / $logo_qr_width;
			$logo_qr_height = $logo_height / $scale;
			$from_width     = ($QR_width - $logo_qr_width) / 2;
			$from_height    = ($QR_height - $logo_qr_height) / 2;

			imagecopyresampled($QR, $logo, $from_width, $from_height, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
		}
		header('Content-type: image/png');
		imagepng($QR);
		imagedestroy($QR);
		die();
	}
	public function getConfig() {
		return $this->config;
	}
	public function install() {
		//向后台添加菜单，如果不添加的话直接返回真
		$data = array(
			'title' => '二维码', //插件后台菜单名字
			'pid'   => ADDONS_MENU, //不用改变
			'url'   => 'Addons/plugin?pn=Erweima&pm=set', //填写后台菜单url名称和方法
			'group' => '已装插件', //不用改变
			'type'  => 'Erweima', //填写自己的插件名字
		);
		//添加到数据库
		if (M('Menu')->add($data)) {
			return true;
		} else {
			return false;
		}
		return true;
	}
	public function uninstall() {
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
	public function set() {
		return $this->fetch('content');
	}
	/**
	 * 把图片处理成圆角
	 * $filename 图片路径
	 * $radius = 5; //圆角的像素，值越大越圆
	 */
	public function imgradius($filename = './Plugins/Erweima/logo.jpg', $radius = 10) {
		$img = null;
		if (is_string($filename)) {
			$img = imagecreatefromjpeg($filename);
		} else {
			$img = $filename;
		}

		$ww = imagesx($img);
		$hh = imagesy($img);

		//整个图,也就是白色背景
		$im      = imagecreatetruecolor($ww, $hh);
		$bgcolor = imagecolorallocate($im, 255, 255, 255);
		imagefill($im, 0, 0, $bgcolor);

		//这里调用函数，绘制淡色的圆角背景，
		$im = $this->imagebackgroundmycard($im, 0, 0, $ww, $hh, $radius);

		//$filename = 'E:\wwwroot\ainiku\Plugins\Erweima\logo.jpg';

		//第一个参数是上面已经用过的大的背景图，也就我们的画板，
		//第二个参数：上面这个目录拿到的capy用的资源文件了
		//第三个单数距离大卡片左边的距离
		//第三个单数距离大卡片上边的距离
		//第三第四是资源图片开始拷贝的位置，这里我是从左上角开始copy的，所以是0和0；
		//第五第六个参数是图片拷过去的大小
		imagecopy($im, $img, 0, 0, 0, 0, $ww, $hh);

		//画圆角
		$lt_corner = $this->get_lt_rounder_corner($radius, 0xef, 0xef, 0xe1);
		//圆角的背景色
		$im = $this->myradus($im, 0, 0, $lt_corner, $radius, $ww, $hh);

		//生成图片
		/*imagepng($im, "test.png");
		imagedestroy($im);*/
		return $im;
	}

	/**
	 * 画一个带圆角的背景图
	 * @param $im  底图
	 * @param $dst_x 画出的图的（0，0）位于底图的x轴位置
	 * @param $dst_y 画出的图的（0，0）位于底图的y轴位置
	 * @param $image_w 画的图的宽
	 * @param $image_h 画的图的高
	 * @param $radius 圆角的值
	 */
	function imagebackgroundmycard($im, $dst_x, $dst_y, $image_w, $image_h, $radius) {
		$resource = imagecreatetruecolor($image_w, $image_h);
		$bgcolor  = imagecolorallocate($resource, 0xef, 0xef, 0xe1); //该图的背景色

		imagefill($resource, 0, 0, $bgcolor);
		$lt_corner = $this->get_lt_rounder_corner($radius, 255, 255, 255); //圆角的背景色

		// lt(左上角)
		imagecopymerge($resource, $lt_corner, 0, 0, 0, 0, $radius, $radius, 100);
		// lb(左下角)
		$lb_corner = imagerotate($lt_corner, 90, 0);
		imagecopymerge($resource, $lb_corner, 0, $image_h - $radius, 0, 0, $radius, $radius, 100);
		// rb(右上角)
		$rb_corner = imagerotate($lt_corner, 180, 0);
		imagecopymerge($resource, $rb_corner, $image_w - $radius, $image_h - $radius, 0, 0, $radius, $radius, 100);
		// rt(右下角)
		$rt_corner = imagerotate($lt_corner, 270, 0);
		imagecopymerge($resource, $rt_corner, $image_w - $radius, 0, 0, 0, $radius, $radius, 100);

		imagecopy($im, $resource, $dst_x, $dst_y, 0, 0, $image_w, $image_h);
		return $im;
	}
	/**
	 * @param $im  大的背景图，也是我们的画板
	 * @param $lt_corner 我们画的圆角
	 * @param $radius  圆角的程度
	 * @param $image_h 图片的高
	 * @param $image_w 图片的宽
	 */
	public function myradus($im, $lift, $top, $lt_corner, $radius, $image_h, $image_w) {
		/// lt(左上角)
		imagecopymerge($im, $lt_corner, $lift, $top, 0, 0, $radius, $radius, 100);
		// lb(左下角)
		$lb_corner = imagerotate($lt_corner, 90, 0);
		imagecopymerge($im, $lb_corner, $lift, $image_h - $radius + $top, 0, 0, $radius, $radius, 100);
		// rb(右上角)
		$rb_corner = imagerotate($lt_corner, 180, 0);
		imagecopymerge($im, $rb_corner, $image_w + $lift - $radius, $image_h + $top - $radius, 0, 0, $radius, $radius, 100);
		// rt(右下角)
		$rt_corner = imagerotate($lt_corner, 270, 0);
		imagecopymerge($im, $rt_corner, $image_w - $radius + $lift, $top, 0, 0, $radius, $radius, 100);
		return $im;
	}

	/** 画圆角
	 * @param $radius 圆角位置
	 * @param $color_r 色值0-255
	 * @param $color_g 色值0-255
	 * @param $color_b 色值0-255
	 * @return resource 返回圆角
	 */
	public function get_lt_rounder_corner($radius, $color_r, $color_g, $color_b) {
		// 创建一个正方形的图像
		$img = imagecreatetruecolor($radius, $radius);
		// 图像的背景
		$bgcolor = imagecolorallocate($img, $color_r, $color_g, $color_b);
		$fgcolor = imagecolorallocate($img, 0, 0, 0);
		imagefill($img, 0, 0, $bgcolor);
		// $radius,$radius：以图像的右下角开始画弧
		// $radius*2, $radius*2：已宽度、高度画弧
		// 180, 270：指定了角度的起始和结束点
		// fgcolor：指定颜色
		imagefilledarc($img, $radius, $radius, $radius * 2, $radius * 2, 180, 270, $fgcolor, IMG_ARC_PIE);
		// 将弧角图片的颜色设置为透明
		imagecolortransparent($img, $fgcolor);
		return $img;
	}
}
