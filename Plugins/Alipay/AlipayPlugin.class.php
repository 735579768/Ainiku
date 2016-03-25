<?php
/**********使用方法********
插件配置好参数后在支付页面提交的参数如下
order 单号
ordername 单号名称
money  交易金额
支付完成后支付宝会异步向你填的通知路径发送一个post请求并附带参数如下
$out_trade_no = $_POST['out_trade_no'];//商户订单号
$trade_no = $_POST['trade_no'];//支付宝交易号
$trade_status = $_POST['trade_status'];//交易状态

同时完成支付后页面会自动跳转到你配置的跳转页面(比如会跳转到你的成功支付页面)

为啦安全起见在这两个页面要先验证下是不是支付宝的请求
//计算得出通知验证结果
$alipayNotify = new \Ainiku\Alipay\AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyNotify();
if($verify_result) {
//验证成功后把信息添加到你的数据库
$out_trade_no = $_POST['out_trade_no'];//商户订单号
$trade_no = $_POST['trade_no'];//支付宝交易号
$trade_status = $_POST['trade_status'];//交易状态//各个状态请查看api或插件下面的示例处理函数
}
 **/
namespace Plugins\Alipay;
require_once pathA('/Plugins/Plugin.class.php');
class AlipayPlugin extends \Plugins\Plugin {
	protected $config = array(
		'version' => '1.0',
		'author'  => 'qiaokeli',
		'name'    => '支付宝',
		'descr'   => '支付宝',
	);
	//测试支付功能
	public function testpay() {
		$this->display('testpay');
		die();
	}
	//实现支付功能
	public function dopay($money = null, $order = null, $ordername = null, $aliarr = '') {
		if (IS_POST) {
			empty($money) && $money         = doubleval(I('money'));
			empty($order) && $order         = I('order');
			empty($ordername) && $ordername = I('ordername');
			//$money=100;
			//$order='1512051222437';
			//$ordername='1512051222437';
			if (empty($order)) {
				$this->error('请输入单号');
			}
			if (empty($ordername)) {
				$this->error('请输入单号名称');
			}
			if (empty($money) || !is_numeric($money) || $money < 0) {
				$this->error('输入的金额不合法请重新输入');
			}
			//取插件配置参数
			$conf = F('pluginalipay');
			if (empty($conf) || APP_DEBUG) {
				$data = M('Addons')->field('param')->where("mark='Alipay'")->find();
				$conf = json_decode($data['param'], true);
				F('pluginalipay', $conf);
			}
			$conf['api'] = json_decode($conf['api']);
			$aliarr      = explode('_', $aliarr);
			if (in_array($aliarr[1], $conf[api])) {
				$conf['api'] = $aliarr;
			} else {
				die('支付宝接口没有配置');
			}
			$alipayconf = array(
//必填
				'sellerid'    => $conf['appid'], //合作身份者pid
				'sellerkey'   => $conf['appkey'], //安全检验码
				'selleruname' => $conf['account'], //收款账号

				'order'       => $order, //单号
				'ordername'   => $ordername,
				'money'       => $money, //交易金额
				'return_url'  => C('WEBDOMIN') . C('return_url'),
				'notify_url'  => C('WEBDOMIN') . C('notify_url'),
//必填
			);

			$alipayconf = array_merge($alipayconf, $conf);
			$alipay     = new \Plugins\Alipay\Pay($alipayconf);
			return $alipay->dopay($alipayconf);
		} else {
			die('参数不对');
		}

	}
	private function yanzheng($type = 1) {
		//取插件配置参数
		$conf = F('pluginalipay');
		if (empty($conf) || APP_DEBUG) {
			$data = M('Addons')->field('param')->where("mark='Alipay'")->find();
			$conf = json_decode($data['param'], true);
			F('pluginalipay', $conf);
		}
		$conf['api']                        = json_decode($conf['api']);
		$alipay_config['partner']           = $conf['appid'];
		$alipay_config['seller_id']         = $conf['account'];
		$alipay_config['key']               = $conf['appkey'];
		$alipay_config['notify_url']        = C('WEBDOMIN') . C('notify_url');
		$alipay_config['return_url']        = C('WEBDOMIN') . C('return_url');
		$alipay_config['sign_type']         = strtoupper('MD5');
		$alipay_config['input_charset']     = strtolower('utf-8');
		$alipay_config['cacert']            = getcwd() . '\\Plugins\\Alipay\\cacert.pem';
		$alipay_config['transport']         = 'http';
		$alipay_config['payment_type']      = "1";
		$alipay_config['service']           = "create_direct_pay_by_user";
		$alipay_config['anti_phishing_key'] = '';

// 客户端的IP地址 非局域网的外网IP地址，如：221.0.0.1
		$alipay_config['exter_invoke_ip'] = "";
		//计算得出通知验证结果
		import('Ainiku.Alipay.core');
		import('Ainiku.Alipay.md5');
		import('Ainiku.Alipay.notify');
		import('Ainiku.Alipay.submit');
		$alipayNotify = new \Ainiku\Alipay\AlipayNotify($alipay_config);
		if ($type == 1) {
			return $alipayNotify->verifyReturn();
		} else {
			return $alipayNotify->verifyNotify();
		}

	}
	public function return_url() {
		if ($this->yanzheng()) {
			//验证成功后把信息添加到你的数据库
			$order_sn             = I('out_trade_no'); //商户订单号
			$data['trade_no']     = I('trade_no'); //支付宝交易号
			$data['trade_status'] = I('trade_status'); //交易状态//各个状态请查看api或插件下面的示例处理函数
			$money                = I('total_fee', 0, 'floatval');
			//$money = I('post.price', 0, 'floatval');

			$data = array(
				'status'   => 1,
				'pay_type' => '支付宝',
				'str'      => '验签成功',
				'money'    => $money,
				'order_sn' => $order_sn,
			);
			return $data;
		} else {
			return array(
				'status'   => 0,
				'pay_type' => '支付宝',
				'str'      => '验签失败',
			);
		}
	}
	public function notify_url() {
		if ($this->yanzheng(2)) {
			//验证成功后把信息添加到你的数据库
			$order_sn     = I('out_trade_no'); //商户订单号
			$trade_no     = I('trade_no'); //支付宝交易号
			$trade_status = I('trade_status'); //交易状态//各个状态请查看api或插件下面的示例处理函数
			if ($trade_status == 'TRADE_SUCCESS' || $trade_status == 'TRADE_FINISHED' || $trade_status == 'WAIT_SELLER_SEND_GOODS') {
/*				//设置为已经支付
$info = M('Order')->where("order_sn=$order_sn")->save(array(
'pay_time'     => NOW_TIME,
'pay_type'     => '支付宝',
'pay_trade_no' => $trade_no,
'order_status' => 2,
));*/
				$money = I('post.total_fee', 0, 'floatval');
				//$money = I('post.price', 0, 'floatval');
				return array(
					'status'   => 1,
					'str'      => '验签成功',
					'pay_type' => '支付宝',
					'money'    => $money,
					'order_sn' => $order_sn,
					'extra'    => '',
				);
			}
		} else {
			return array(
				'status'   => 0,
				'pay_type' => '支付宝',
				'str'      => '验签失败',
			);
		}
	}
	//钩子默认的调用方法
	public function run() {
		$this->display('content');
	}
	public function getConfig() {
		return $this->config;
	}
	public function install() {
		//向后台添加菜单，如果不添加的话直接返回真
		$data = array(
			'title' => '支付宝', //插件后台菜单名字
			'pid'   => ADDONS_MENU, //不用改变
			'url'   => 'Addons/plugin?pn=Alipay&pm=set', //填写后台菜单url名称和方法
			'group' => '已装插件', //不用改变
			'type'  => 'Alipay', //填写自己的插件名字
		);
		//添加到数据库
		if (M('Menu')->add($data)) {
			return true;
		} else {
			return false;
		}
	}
	public function uninstall() {
		//删除后台添加的菜单，如果没有直接返回真
		$map['type'] = 'Alipay';
		if (M('Menu')->where($map)->delete()) {
			return true;
		} else {
			return false;
		}
	}
	public function set() {
		$this->meta_title = '支付宝设置';
		//插件工菜单后台设置,没有的话直接返回真
		if (IS_POST) {
			$data = array(
				'update_time' => NOW_TIME,
				'account'     => I('post.ALIPAYUNAME'), //账号
				'appid'       => I('post.ALIPAYSAFEID'), //验证key
				'appkey'      => I('post.ALIPAYVERIFY'), //验证密钥
				'api'         => json_encode(I('post.ALIPAYAPI')), //接口类型
			);
			$model  = M('Addons');
			$result = $model->where("mark='Alipay'")->save(array('param' => json_encode($data)));
			if (0 < $result) {
				$this->success('保存成功');
			} else {
				$this->error('保存失败');
			}
		} else {
			$data = M('Addons')->field('param')->where("mark='Alipay'")->find();
			$this->assign('info', json_decode($data['param'], true));
			$str = $this->fetch('config');
			return $str;
		}
	}
/*	//接口通知页面
public function suc($ordernum = "2014100422482039314", $tradestatus = 'no') {
if (empty($ordernum)) {
$this->error('非法请求');
}

$info = D('order')->where("ordernum='$ordernum' and chongzhi=0")->find();
if (empty($info)) {
$this->error('非法请求');
}

$status = array(
'no'                       => '未知状态',
'WAIT_BUYER_PAY'           => '等待买家付款',
'WAIT_SELLER_SEND_GOODS'   => '等待卖家发货',
'WAIT_BUYER_CONFIRM_GOODS' => '卖家已发货，等待买家确认收货',
'TRADE_FINISHED'           => '充值成功',
'TRADE_SUCCESS'            => '充值成功',
);
if ($info['chongzhi'] == '0' and ($info['status'] == 'TRADE_SUCCESS' or $info['status'] == 'TRADE_FINISHED')) {
//添加在线充值日志
addlog(UID, '在线充值', $info['money']);

//会员添加钱数
$result = M('member')->where("id=" . UID)->setInc('points', $info['money']);
if (0 < $result) {
//当前订单状态完成
D('order')->where("ordernum='$ordernum'")->setField('chongzhi', 1);
}
}
$this->assign('info', $info);
$this->assign('status', $status[$info['status']]);
$this->display();
}
////////////////////各个接口的通知函数/////////////////////////////////////////////////////////////////////////////
public function tradenNotifyurl() {
//计算得出通知验证结果
$alipayNotify  = new \Ainiku\Alipay\AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyNotify();
if ($verify_result) {
//验证成功
$out_trade_no = $_POST['out_trade_no']; //商户订单号
$trade_no     = $_POST['trade_no']; //支付宝交易号
$trade_status = $_POST['trade_status']; //交易状态
//初始化模型更新订单状态
$model = D('order');
$model->where("ordernum='$out_trade_no'")->save(array('status' => $trade_status, 'alipaynum' => $trade_no));

if ($_POST['trade_status'] == 'WAIT_BUYER_PAY') {
//该判断表示买家已在支付宝交易管理中产生了交易记录，但没有付款
} else if ($_POST['trade_status'] == 'WAIT_SELLER_SEND_GOODS') {
//该判断表示买家已在支付宝交易管理中产生了交易记录且付款成功，但卖家没有发货
} else if ($_POST['trade_status'] == 'WAIT_BUYER_CONFIRM_GOODS') {
//该判断表示卖家已经发了货，但买家还没有做确认收货的操作
} else if ($_POST['trade_status'] == 'TRADE_FINISHED') {
//该判断表示买家已经确认收货，这笔交易完成
} else {
//其它状态
}
} else {
//验证失败
echo "fail";
}
}
public function tradeReturnurl() {
//计算得出通知验证结果
$alipayNotify  = new \Ainiku\Alipay\AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyNotify();
if ($verify_result) {
$out_trade_no = $_GET['out_trade_no']; //商户订单号
$trade_no     = $_GET['trade_no']; //支付宝交易号
$trade_status = $_GET['trade_status']; //交易状态
//验证成功
if ($_GET['trade_status'] == 'WAIT_SELLER_SEND_GOODS') {
} else {
echo "trade_status=" . $_GET['trade_status'];
}
$success = A('Alipay');
$suc->suc($out_trade_no, $trade_no);
echo "验证成功<br />";
echo "trade_no=" . $trade_no;
} else {
echo "验证失败";
}
}

public function directNotifyurl() {
//计算得出通知验证结果
$alipayNotify  = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyNotify();

if ($verify_result) {
//验证成功
$out_trade_no = $_POST['out_trade_no'];
$trade_no     = $_POST['trade_no'];
$trade_status = $_POST['trade_status'];
//初始化模型更新订单状态
$model = D('order');
$model->where("ordernum='$out_trade_no'")->save(array('status' => $trade_status, 'alipaynum' => $trade_no));
if ($_POST['trade_status'] == 'TRADE_FINISHED') {
//该种交易状态只在两种情况下出现
//1、开通了普通即时到账，买家付款成功后。
//2、开通了高级即时到账，从该笔交易成功时间算起，过了签约时的可退款时限（如：三个月以内可退款、一年以内可退款等）后。
} else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
//该种交易状态只在一种情况下出现——开通了高级即时到账，买家付款成功后
}

echo "success";
} else {
//验证失败
echo "fail";
}
}
public function directReturnurl() {
//计算得出通知验证结果
$alipayNotify  = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyReturn();
if ($verify_result) {
//验证成功
$out_trade_no = $_POST['out_trade_no'];
$trade_no     = $_POST['trade_no'];
$trade_status = $_POST['trade_status'];
if ($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
//判断该笔订单是否在商户网站中已经做过处理
//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
//如果有做过处理，不执行商户的业务程序
} else {
echo "trade_status=" . $_GET['trade_status'];
}
$success = A('Alipay');
$suc->suc($out_trade_no, $trade_no);
echo "验证成功<br />";
} else {
echo "验证失败";
}
}

public function shuangNotifyurl() {
//计算得出通知验证结果
$alipayNotify  = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyReturn();
if ($verify_result) {
//验证成功
$out_trade_no = $_POST['out_trade_no'];
$trade_no     = $_POST['trade_no'];
$trade_status = $_POST['trade_status'];
//初始化模型更新订单状态
$model = D('order');
$model->where("ordernum='$out_trade_no'")->save(array('status' => $trade_status, 'alipaynum' => $trade_no));
if ($_POST['trade_status'] == 'WAIT_BUYER_PAY') {
//该判断表示买家已在支付宝交易管理中产生了交易记录，但没有付款

//判断该笔订单是否在商户网站中已经做过处理
//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
//如果有做过处理，不执行商户的业务程
echo "success";
} else if ($_POST['trade_status'] == 'WAIT_SELLER_SEND_GOODS') {
//该判断表示买家已在支付宝交易管理中产生了交易记录且付款成功，但卖家没有发货

//判断该笔订单是否在商户网站中已经做过处理
//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
//如果有做过处理，不执行商户的业务程序
echo "success";
} else if ($_POST['trade_status'] == 'WAIT_BUYER_CONFIRM_GOODS') {
//该判断表示卖家已经发了货，但买家还没有做确认收货的操作

//判断该笔订单是否在商户网站中已经做过处理
//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
//如果有做过处理，不执行商户的业务程序
echo "success";
} else if ($_POST['trade_status'] == 'TRADE_FINISHED') {
//该判断表示买家已经确认收货，这笔交易完成

//判断该笔订单是否在商户网站中已经做过处理
//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
//如果有做过处理，不执行商户的业务程序
echo "success";
} else {
//其他状态判断
echo "success";
}
echo "验证成功<br />";
} else {
echo "验证失败";
}

}
public function shuangReturnurl() {
//计算得出通知验证结果
$alipayNotify  = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyReturn();
if ($verify_result) {
//验证成功
$out_trade_no = $_POST['out_trade_no'];
$trade_no     = $_POST['trade_no'];
$trade_status = $_POST['trade_status'];
if ($_GET['trade_status'] == 'WAIT_SELLER_SEND_GOODS') {
//判断该笔订单是否在商户网站中已经做过处理
//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
//如果有做过处理，不执行商户的业务程序
} else if ($_GET['trade_status'] == 'TRADE_FINISHED') {
//判断该笔订单是否在商户网站中已经做过处理
//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
//如果有做过处理，不执行商户的业务程序
} else {
echo "trade_status=" . $_GET['trade_status'];
}
} else {
echo "验证失败";
}
}*/
}
