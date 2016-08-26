<?php
namespace Home\Controller;
use Think\Controller;

defined("ACCESS_ROOT") || die("Invalid access");
class IndexController extends HomeController {
	public function index() {
		echo '<meta charset="utf-8">';

		// die();
		// $this->display();
		$str = <<<eot
<script>
var ainiku={
	ueupload:"/index.php?m=Home&c=File&a=ueupload",
	umeupload:"/index.php?m=Home&c=File&a=umeupload&",
	controllername:'Oskefu',
	actionname:'allUser',
	defaultmenu:"/index.php?m=Home&c=Oskefu&a=index",
	updatefield:"/index.php?m=Home&c=Index&a=updatefield",
	delimg:"/index.php?m=Home&c=File&a=delimg",
	delattach:"/index.php?m=Home&c=File&a=delattach",
	deleditorimg:"/index.php?m=Home&c=File&a=deleditorimg",
	setposition:"/index.php?m=Home&c=Index&a=setposition",
	getmenu:"/index.php?m=Home&c=Ajax&a=getmenu"
	};
</script>
<link href="/Public/Static/css/reset.css?r=24713" type="text/css" rel="stylesheet" />
<link href="/Public/Static/css/common.css?r=24713" type="text/css" rel="stylesheet" />
<link href="/Public/Home/default/css/index.css?r=24713" type="text/css" rel="stylesheet" />
<link href="/Public/Static/css/font-awesome.min.css?r=24713" type="text/css" rel="stylesheet" />
<script src="/Public/Home/default/js/jquery-1.9.1.min.js?r=72229" type="text/javascript" ></script>
<script src="/Public/Static/js/ainiku.js?r=72229" type="text/javascript" ></script>
<script src="/Public/Static/js/file.js?r=72229" type="text/javascript" ></script>
<script src="/Public/Static/js/jquery.SuperSlide.2.1.1.js?r=72229" type="text/javascript" ></script>
<script src="/Public/Static/js/divselect.js?r=72229" type="text/javascript" ></script>
eot;
		echo $str;
		echo create_form(get_model_attr('article'), null);
	}
	function send_mail() {
		$result = send_mail(array(
			'to'       => '735579768@qq.com',
			'subject'  => '邮件主题',
			'fromname' => '我是赵克立你好哦',

		));
		if ($result === true) {
			echo '发送成功';
		} else {
			echo $result;
		}
	}
	function pay() {
		$alipayconf = array(
			//必填
			'order'         => '111111111111',
			'ordername'     => '订单名称',
			'money'         => 100,
			//必填

			//选填
			'goodsurl'      => $goodsurl,
			'receivename'   => $receivename,
			'receiveadr'    => $receiveadr,
			'receivezip'    => $receivezip, //邮编
			'receivephone'  => $receivephone,
			'receivemobile' => $receivemobile,
			//选填

		);
		$alipay = A('Pay');
		$alipay->dopay($alipayconf);

	}
}