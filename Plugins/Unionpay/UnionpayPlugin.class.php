<?php
namespace Plugins\Unionpay;
define('UNIONPAY_PATH', str_replace('\\', '/', dirname(__FILE__)));
require_once pathA('/Plugins/Plugin.class.php');
class UnionpayPlugin extends \Plugins\Plugin {
	protected $config = array(
		'version' => '1.0',
		'author'  => 'qiaokeli',
		'name'    => '中国银联',
		'descr'   => '银联支付',
	);
	//钩子默认的调用方法
	public function run($a, $b) {
		$this->assign('a', $a);
		$this->assign('b', $b);
		$this->display('content');
	}
	private function daoru() {

	}
	public function dopay($money = null, $order = null, $ordername = null, $reqReserved = '') {
		//取插件配置参数
		$conf = F('pluginunionpay');
		if (empty($conf) || APP_DEBUG) {
			$data = M('Addons')->field('param')->where("mark='Unionpay'")->find();
			$conf = json_decode($data['param'], true);
			F('pluginunionpay', $conf);
		}
		define('UNIONPAY_MEMBER_ID', $conf['MEMBER_ID']);
		include_once UNIONPAY_PATH . '/lib/utf8/func/SDKConfig.php';
		include_once UNIONPAY_PATH . '/lib/utf8/func/common.php';
		include_once UNIONPAY_PATH . '/lib/utf8/func/PinBlock.php';
		include_once UNIONPAY_PATH . '/lib/utf8/func/PublicEncrypte.php';
		include_once UNIONPAY_PATH . '/lib/utf8/func/secureUtil.php';
		include_once UNIONPAY_PATH . '/lib/utf8/func/httpClient.php';

		/**
		 *	以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己需要，按照技术文档编写。该代码仅供参考
		 */
		// 初始化日志
		//$log = new PhpLog ( SDK_LOG_FILE_PATH, "PRC", SDK_LOG_LEVEL );
		//$log->LogInfo ( "============处理前台请求开始===============" );
		// 初始化日志
		$params = array(
			'version'      => '5.0.0', //版本号
			'encoding'     => 'utf-8', //编码方式
			'certId'       => getSignCertId(), //证书ID
			'txnType'      => '01', //交易类型
			'txnSubType'   => '01', //交易子类 01：预授权、03：担保消费
			'bizType'      => '000201', //业务类型
			'frontUrl'     => SDK_FRONT_NOTIFY_URL, //前台通知地址
			'backUrl'      => SDK_BACK_NOTIFY_URL, //后台通知地址
			'signMethod'   => '01', //签名方法
			'channelType'  => '08', //渠道类型，07-PC，08-手机
			'accessType'   => '0', //接入类型
			'merId'        => MEMBER_ID, //商户代码，请改自己的测试商户号
			'orderId'      => $order, //商户订单号，8-40位数字字母
			'txnTime'      => date('YmdHis'), //订单发送时间
			'txnAmt'       => $money * 100, //交易金额，单位分
			'currencyCode' => '156', //交易币种
			'orderDesc'    => $ordername, //订单描述，可不上送，上送时控件中会显示该信息
			'reqReserved'  => $reqReserved, //请求方保留域，透传字段，查询、通知、对账文件中均会原样出现
		);

		// 签名
		sign($params);
		// 前台请求地址
		$front_uri = SDK_FRONT_TRANS_URL;
		$html_form = create_html($params, $front_uri);
		return $html_form;
	}
	private function yanzheng() {
		include_once UNIONPAY_PATH . '/lib/utf8/func/SDKConfig.php';
		include_once UNIONPAY_PATH . '/lib/utf8/func/common.php';
		include_once UNIONPAY_PATH . '/lib/utf8/func/PinBlock.php';
		include_once UNIONPAY_PATH . '/lib/utf8/func/PublicEncrypte.php';
		include_once UNIONPAY_PATH . '/lib/utf8/func/secureUtil.php';
		include_once UNIONPAY_PATH . '/lib/utf8/func/httpClient.php';
		if (isset($_POST['signature'])) {
			return verify($_POST);
		} else {
			return false;
		}

	}
	public function return_url() {
		//银联通知
		/*		$str = '';
		foreach ($_POST as $key => $val) {
			$str .= isset($mpi_arr[$key]) ? $mpi_arr[$key] : $key . '<br>';
			$str .= $val;
		}
		echo $str;*/

		if ($this->yanzheng()) {
			$orderId = $_POST['orderId'];
			$data    = array(
				'status'   => 1,
				'str'      => '验签成功',
				'pay_type' => '中国银联',
				'money'    => I('post.txnAmt', 0, 'floatval') / 100,
				'order_sn' => I('post.orderId'),
				'extra'    => I('post.reqReserved'),
			);
			return $data;
		} else {
			return array(
				'status'   => 0,
				'pay_type' => '中国银联',
				'str'      => '验签失败',
			);
		}

	}
	public function notify_url() {
		if ($this->yanzheng()) {
			return array(
				'status'   => 1,
				'str'      => '验签成功',
				'pay_type' => '中国银联',
				'money'    => I('post.txnAmt', 0, 'floatval') / 100,
				'order_sn' => I('post.orderId'),
				'extra'    => I('post.reqReserved'),
			);
		} else {
			return array(
				'status'   => 0,
				'pay_type' => '中国银联',
				'str'      => '验签失败',
			);
		}
	}
	public function getConfig() {
		return $this->config;
	}
	public function install() {
		//向后台添加菜单，如果不添加的话直接返回真
		$data = array(
			'title' => '中国银联', //插件后台菜单名字
			'pid'   => ADDONS_MENU, //不用改变
			'url'   => 'Addons/plugin?pn=Unionpay&pm=set', //填写后台菜单url名称和方法
			'group' => '已装插件', //不用改变
			'type'  => 'Unionpay', //填写自己的插件名字
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
		$map['type'] = 'Unionpay';
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
		$this->meta_title = '支付宝设置';
		//插件工菜单后台设置,没有的话直接返回真
		if (IS_POST) {
			$data = array(
				'update_time' => NOW_TIME,
				'MEMBER_ID'   => I('post.MEMBER_ID'), //商户id
			);
			$model  = M('Addons');
			$result = $model->where("mark='Unionpay'")->save(array('param' => json_encode($data)));
			if (0 < $result) {
				$this->success('保存成功');
			} else {
				$this->error('保存失败');
			}
		} else {
			$data = M('Addons')->field('param')->where("mark='Unionpay'")->find();
			$this->assign('info', json_decode($data['param'], true));
			$str = $this->fetch('config');
			return $str;
		}
	}
}
