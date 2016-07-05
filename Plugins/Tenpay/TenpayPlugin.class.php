<?php
namespace Plugins\Tenpay;
define('TENPAY_PATH', str_replace('\\', '/', dirname(__FILE__)));
// require_once path_a('/Plugins/Plugin.class.php');
class TenpayPlugin extends \Plugins\Plugin {
	protected $config = array(
		'version' => '1.0',
		'author'  => 'qiaokeli',
		'name'    => '财付通',
		'descr'   => '财付通',
	);
	//钩子默认的调用方法
	public function run($a, $b) {
		$this->assign('a', $a);
		$this->assign('b', $b);
		$this->display('content');
	}

	public function dopay($money = null, $order = null, $ordername = null) {
		//取插件配置参数
		$conf = F('pluginTenpay');
		if (empty($conf) || APP_DEBUG) {
			$data = M('Addons')->field('param')->where("mark='Tenpay'")->find();
			$conf = json_decode($data['param'], true);
			F('pluginTenpay', $conf);
		}
		define('SHANGHU_ID', $conf['partner']);
		define('SHANGHU_KEY', $conf['key']);

		require_once TENPAY_PATH . "/lib/classes/RequestHandler.class.php";
		require_once TENPAY_PATH . "/lib/tenpay_config.php";

/* 获取提交的订单号 */
		$out_trade_no = $order;
/* 获取提交的商品名称 */
		$product_name = $ordername;
/* 获取提交的商品价格 */
		$order_price = $money;
/* 获取提交的备注信息 */
		$remarkexplain = '没有备注';
/* 支付方式  1:即时到帐，2:中介担保,3:后台选择*/
		$trade_mode = 1;

		$strDate = date("Ymd");
		$strTime = date("His");

/* 商品价格（包含运费），以分为单位 */
		$total_fee = $order_price * 100;

/* 商品名称 */
		$desc = "商品：" . $product_name . ",备注:" . $remarkexplain;

/* 创建支付请求对象 */
		$reqHandler = new RequestHandler();
		$reqHandler->init();
		$reqHandler->setKey($key);
		$reqHandler->setGateUrl("https://gw.tenpay.com/gateway/pay.htm");

//----------------------------------------
		//设置支付参数
		//----------------------------------------
		$reqHandler->setParameter("partner", $partner);
		$reqHandler->setParameter("out_trade_no", $out_trade_no);
		$reqHandler->setParameter("total_fee", $total_fee); //总金额
		$reqHandler->setParameter("return_url", $return_url);
		$reqHandler->setParameter("notify_url", $notify_url);
		$reqHandler->setParameter("body", $desc);
		$reqHandler->setParameter("bank_type", "DEFAULT"); //银行类型，默认为财付通
		//用户ip
		$reqHandler->setParameter("spbill_create_ip", $_SERVER['REMOTE_ADDR']); //客户端IP
		$reqHandler->setParameter("fee_type", "1"); //币种
		$reqHandler->setParameter("subject", $desc); //商品名称，（中介交易时必填）

//系统可选参数
		$reqHandler->setParameter("sign_type", "MD5"); //签名方式，默认为MD5，可选RSA
		$reqHandler->setParameter("service_version", "1.0"); //接口版本号
		$reqHandler->setParameter("input_charset", "utf-8"); //字符集
		$reqHandler->setParameter("sign_key_index", "1"); //密钥序号

//业务可选参数
		$reqHandler->setParameter("attach", ""); //附件数据，原样返回就可以了
		$reqHandler->setParameter("product_fee", ""); //商品费用
		$reqHandler->setParameter("transport_fee", "0"); //物流费用
		$reqHandler->setParameter("time_start", date("YmdHis")); //订单生成时间
		$reqHandler->setParameter("time_expire", ""); //订单失效时间
		$reqHandler->setParameter("buyer_id", ""); //买方财付通帐号
		$reqHandler->setParameter("goods_tag", ""); //商品标记
		$reqHandler->setParameter("trade_mode", $trade_mode); //交易模式（1.即时到帐模式，2.中介担保模式，3.后台选择（卖家进入支付中心列表选择））
		$reqHandler->setParameter("transport_desc", ""); //物流说明
		$reqHandler->setParameter("trans_type", "1"); //交易类型
		$reqHandler->setParameter("agentid", ""); //平台ID
		$reqHandler->setParameter("agent_type", ""); //代理模式（0.无代理，1.表示卡易售模式，2.表示网店模式）
		$reqHandler->setParameter("seller_id", ""); //卖家的商户号

//请求的URL
		$reqUrl = $reqHandler->getRequestURL();

//获取debug信息,建议把请求和debug信息写入日志，方便定位问题
		/**/
		$debugInfo = $reqHandler->getDebugInfo();

		$html = <<<eot
<form id="tenpay_form" action="{$reqHandler->getGateUrl()}" method="post" target="_blank">
eot;

		$params = $reqHandler->getAllParameters();
		foreach ($params as $k => $v) {
			$html .= "<input type=\"hidden\" name=\"{$k}\" value=\"{$v}\" />\n";
		}
		$html .= <<<eot
<input type="submit" value="财付通支付">
</form>
<script>
document.getElementById('tenpay_form').submit();
</script>
eot;
		return $html;
	}
	private function yanzheng() {
		require_once TENPAY_PATH . "/lib/classes/ResponseHandler.class.php";
		require_once TENPAY_PATH . "/lib/classes/function.php";
		require_once TENPAY_PATH . "/lib/tenpay_config.php";

		log_result("进入前台回调页面");

		/* 创建支付应答对象 */
		$resHandler = new ResponseHandler();
		$resHandler->setKey($key);

		//判断签名
		if ($resHandler->isTenpaySign()) {
			return $resHandler;
		} else {
			return false;
		}

	}
	public function return_url() {
		$resHandler = $this->yanzheng();
		if ($resHandler) {
			//通知id
			$notify_id = $resHandler->getParameter("notify_id");
			//商户订单号
			$out_trade_no = $resHandler->getParameter("out_trade_no");
			//财付通订单号
			$transaction_id = $resHandler->getParameter("transaction_id");
			//金额,以分为单位
			$total_fee = $resHandler->getParameter("total_fee");
			//如果有使用折扣券，discount有值，total_fee+discount=原请求的total_fee
			$discount = $resHandler->getParameter("discount");
			//支付结果
			$trade_state = $resHandler->getParameter("trade_state");
			//交易模式,1即时到账
			$trade_mode = $resHandler->getParameter("trade_mode");
			if ("1" == $trade_mode) {
				if ("0" == $trade_state) {
					$orderId = $out_trade_no;
					$data    = array(
						'status'   => 1,
						'str'      => '财付通即时到帐支付成功',
						'mark'     => '财付通',
						'order_sn' => $orderId,
					);
					return $data;
				} else {
					//
					return array(
						'status' => 0,
						'mark'   => '财付通',
						'str'    => '财付通即时到帐支付失败',
					);
				}
			} elseif ("2" == $trade_mode) {
				if ("0" == $trade_state) {
					$orderId = $out_trade_no;
					$data    = array(
						'status'   => 1,
						'str'      => '财付通中介担保支付成功',
						'mark'     => '财付通',
						'order_sn' => $orderId,
					);
					return $data;

				} else {
					//当做不成功处理
					return array(
						'status' => 0,
						'mark'   => '财付通',
						'str'    => '财付通中介担保支付失败',
					);
					//echo "<br/>" . "财付通中介担保支付失败" . "<br/>";
				}
			}
		} else {
			return array(
				'status' => 0,
				'mark'   => '财付通',
				'str'    => '验签失败',
			);
		}
	}
	public function notify_url() {
		$resHandler = $this->yanzheng();
		if ($resHandler) {
			//通知id
			$notify_id = $resHandler->getParameter("notify_id");
			//商户订单号
			$out_trade_no = $resHandler->getParameter("out_trade_no");
			//财付通订单号
			$transaction_id = $resHandler->getParameter("transaction_id");
			//金额,以分为单位
			$total_fee = $resHandler->getParameter("total_fee");
			//如果有使用折扣券，discount有值，total_fee+discount=原请求的total_fee
			$discount = $resHandler->getParameter("discount");
			//支付结果
			$trade_state = $resHandler->getParameter("trade_state");
			//交易模式,1即时到账
			$trade_mode = $resHandler->getParameter("trade_mode");
			$orderId    = $out_trade_no;
			$info       = M('Order')->where("order_sn=$orderId")->save(array(
				'pay_time'     => NOW_TIME,
				'pay_trade_no' => $transaction_id,
				'pay_type'     => '财付通',
				'order_status' => 2,
			));
		}
	}
	public function getConfig() {
		return $this->config;
	}
	public function install() {
		//向后台添加菜单，如果不添加的话直接返回真
		$data = array(
			'title' => '财付通', //插件后台菜单名字
			'pid'   => ADDONS_MENU, //不用改变
			'url'   => 'Addons/plugin?pn=Tenpay&pm=set', //填写后台菜单url名称和方法
			'group' => '已装插件', //不用改变
			'type'  => 'Tenpay', //填写自己的插件名字
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
		$map['type'] = 'Tenpay';
		if (M('Menu')->where($map)->delete()) {
			return true;
		} else {
			return false;
		}
	}
	public function tijiao() {
		$this->success('提交成功');
	}
	public function set() {
		$this->meta_title = '财付通设置';
		//插件工菜单后台设置,没有的话直接返回真
		if (IS_POST) {
			$data = array(
				'update_time' => NOW_TIME,
				'partner'     => I('post.partner'), //商户id
				'key'         => I('key'),
			);
			$model  = M('Addons');
			$result = $model->where("mark='Tenpay'")->save(array('param' => json_encode($data)));
			if (0 < $result) {
				$this->success('保存成功');
			} else {
				$this->error('保存失败');
			}
		} else {
			$data = M('Addons')->field('param')->where("mark='Tenpay'")->find();
			$this->assign('info', json_decode($data['param'], true));
			$str = $this->fetch('config');
			return $str;
		}
	}
}
