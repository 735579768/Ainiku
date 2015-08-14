<?php
namespace Home\Controller;
use Think\Controller;
if(!defined("ACCESS_ROOT"))die("Invalid access");
class PayController extends HomeController {
    public function _initialize(){
		   parent::_initialize();
		   	import('Ainiku.Alipay.core');
			import('Ainiku.Alipay.md5');
			import('Ainiku.Alipay.notify');
			import('Ainiku.Alipay.submit');   
    }
	//支付示例功能
	public function testpay(){
		$this->display();
		}
	public function dopay($conf=array()){
		$paytype=C('ALIPAYAPI');
		if($paytype==='shuang'){
			//双接口
			$this->doShuangPay($conf);
		}else if($paytype==='danbao'){
			//担保交易
			$this->doTradePay($conf);
		}else if($paytype==='jishi'){
			//即时到账
			$this->doDirectPay($conf);
		}
		die();	
		}
	/********************担保交易***************************/
	public function doTradePay($conf=array()){
		$alipayconf=array(
			//必填
			//'sellerid'=>C('ALIPAYSAFEID'),//合作身份者pid
			//'sellerkey'=>C('ALIPAYVERIFY'),//安全检验码
			//'selleruname'=>C('ALIPAYUNAME'),//收款账号
			
			'order'=>$order,//商户订单号
			'ordername'=>$ordername,//订单名称
			'money'=>$money,//金额
			'goodsnum'=>1,//商品数量
			//必填
			
			//选填
			'orderdescr'=>$orderdescr,//订单描述
			'goodsurl'=>$goodsurl,//订单详情地址
			'receivename'=>$receivename,
			'receiveadr'=>$receiveadr,
			'receivezip'=>$receivezip,//邮编
			'receivephone'=>$receivephone,
			'receivemobile'=>$receivemobile
			//选填
			
		);
		$alipayconf=array_merge($alipayconf,$conf);
		
		$alipay_config['partner']=$alipayconf['sellerid'];// ;//合作身份者id
		$alipay_config['key']=$alipayconf['sellerkey'];// ;//安全检验码
		$alipay_config['sign_type']    = strtoupper('MD5');//签名方式 不需修改
		$alipay_config['input_charset']= strtolower('utf-8');
		//ca证书路径地址，用于curl中ssl校验
		//请保证cacert.pem文件在当前文件夹目录中
		$alipay_config['cacert']    = getcwd().'\\cacert.pem';
		$alipay_config['transport']    = 'http';



		 //必填
        $payment_type = "1";//支付类型    
        $notify_url = C('WEBDOMIN').U('Pay/tradeNotifyurl');//服务器异步通知页面路径 
        $return_url = C('WEBDOMIN').U('Pay/tradeReturnurl'); //页面跳转同步通知页面路径
        $seller_email = $alipayconf['selleruname'];//;//卖家支付宝帐户
       
	    $out_trade_no = $alipayconf['order'];//商户订单号    
        $subject = $alipayconf['ordername'];//订单名称
		$body = $alipayconf['orderdescr'];//订单描述
        
		$price = $alipayconf['money'];//付款金额
        $quantity =$alipayconf['goodsnum'];//商品数量
        $logistics_fee = "0.00";//物流费用即运费
        $logistics_type = "EXPRESS";//物流类型
        $logistics_payment = "SELLER_PAY";//物流支付方式
        //，两个值可选：SELLER_PAY（卖家承担运费）、BUYER_PAY（买家承担运费）
        
		//必填
		
		
        //选填
        $show_url = $alipayconf['orderdescr'];//商品展示地址
        $receive_name = $alipayconf['receivename'];//收货人姓名
        $receive_address = $alipayconf['receiveadr'];//收货人地址
        $receive_zip = $alipayconf['receivezip'];//收货人邮编
        $receive_phone = $alipayconf['receivephone'];//收货人电话号码
        $receive_mobile = $alipayconf['receivemobile'];//收货人手机号码
		//选填
		
		//构造要请求的参数数组，无需改动
		$parameter = array(
				"service" => "create_partner_trade_by_buyer",
				"partner" => trim($alipay_config['partner']),
				"payment_type"	=> $payment_type,
				"notify_url"	=> $notify_url,
				"return_url"	=> $return_url,
				"seller_email"	=> $seller_email,
				"out_trade_no"	=> $out_trade_no,
				"subject"	=> $subject,
				"price"	=> $price,
				"quantity"	=> $quantity,
				"logistics_fee"	=> $logistics_fee,
				"logistics_type"	=> $logistics_type,
				"logistics_payment"	=> $logistics_payment,
				"body"	=> $body,
				"show_url"	=> $show_url,
				"receive_name"	=> $receive_name,
				"receive_address"	=> $receive_address,
				"receive_zip"	=> $receive_zip,
				"receive_phone"	=> $receive_phone,
				"receive_mobile"	=> $receive_mobile,
				"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
		);

		//建立请求
		$alipaySubmit = new \Ainiku\Alipay\AlipaySubmit($alipay_config);
		$html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
		echo $html_text;
		exit();		
		}
		/********************即时到账***************************/
		function doDirectPay($conf=array()){
		$alipayconf=array(
			//必填
			//'sellerid'=>C('ALIPAYSAFEID'),//合作身份者pid
			//'sellerkey'=>C('ALIPAYVERIFY'),//安全检验码
			//'selleruname'=>C('ALIPAYUNAME'),//收款账号
			
			'order'=>$order,//商户订单号
			'ordername'=>$ordername,//订单名称
			'money'=>$money,//金额
			//必填
			
			//选填
			'orderdescr'=>$orderdescr,//订单描述
			'goodsurl'=>$goodsurl,//订单详情地址
			//选填
			
		);
		$alipayconf=array_merge($alipayconf,$conf);
		
		$alipay_config['partner']=$alipayconf['sellerid'];// ;//合作身份者id
		$alipay_config['key']=$alipayconf['sellerkey'];// ;//安全检验码
		$alipay_config['sign_type']    = strtoupper('MD5');//签名方式 不需修改
		$alipay_config['input_charset']= strtolower('utf-8');
		//ca证书路径地址，用于curl中ssl校验
		//请保证cacert.pem文件在当前文件夹目录中
		$alipay_config['cacert']    = getcwd().'\\cacert.pem';
		$alipay_config['transport']    = 'http';



		 //必填
        $payment_type = "1";//支付类型    
        $notify_url = C('WEBDOMIN').U('Pay/directNotifyurl');//服务器异步通知页面路径 
        $return_url = C('WEBDOMIN').U('Pay/directReturnurl'); //页面跳转同步通知页面路径
        $seller_email = $alipayconf['selleruname'];//;//卖家支付宝帐户
       
	    $out_trade_no = $alipayconf['order'];//商户订单号    
        $subject = $alipayconf['ordername'];//订单名称
		$body = $alipayconf['orderdescr'];//订单描述
        
		$total_fee = $alipayconf['money'];//付款金额
		$anti_phishing_key = "";//防钓鱼时间戳
		$exter_invoke_ip = "";//客户端的IP地址
		//必填
        $show_url = $alipayconf['orderdescr'];//商品展示地址
		
//构造要请求的参数数组，无需改动
$parameter = array(
		"service" => "create_direct_pay_by_user",
		"partner" => trim($alipay_config['partner']),
		"payment_type"	=> $payment_type,
		"notify_url"	=> $notify_url,
		"return_url"	=> $return_url,
		"seller_email"	=> $seller_email,
		"out_trade_no"	=> $out_trade_no,
		"subject"	=> $subject,
		"total_fee"	=> $total_fee,
		"body"	=> $body,
		"show_url"	=> $show_url,
		"anti_phishing_key"	=> $anti_phishing_key,
		"exter_invoke_ip"	=> $exter_invoke_ip,
		"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
);

//建立请求
$alipaySubmit = new \Ainiku\Alipay\AlipaySubmit($alipay_config);
$html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
echo $html_text;
exit();					
			}
	/********************双标准接口***************************/
	public function doShuangPay($conf=array()){
		$alipayconf=array(
			//必填
			//'sellerid'=>C('ALIPAYSAFEID'),//合作身份者pid
			//'sellerkey'=>C('ALIPAYVERIFY'),//安全检验码
			//'selleruname'=>C('ALIPAYUNAME'),//收款账号
			
			'order'=>$order,//商户订单号
			'ordername'=>$ordername,//订单名称
			'money'=>$money,//金额
			'goodsnum'=>1,//商品数量
			//必填
			
			//选填
			'orderdescr'=>$orderdescr,//订单描述
			'goodsurl'=>$goodsurl,//订单详情地址
			'receivename'=>$receivename,
			'receiveadr'=>$receiveadr,
			'receivezip'=>$receivezip,//邮编
			'receivephone'=>$receivephone,
			'receivemobile'=>$receivemobile
			//选填
			
		);
		$alipayconf=array_merge($alipayconf,$conf);
		
		$alipay_config['partner']=$alipayconf['sellerid'];// ;//合作身份者id
		$alipay_config['key']=$alipayconf['sellerkey'];// ;//安全检验码
		$alipay_config['sign_type']    = strtoupper('MD5');//签名方式 不需修改
		$alipay_config['input_charset']= strtolower('utf-8');
		//ca证书路径地址，用于curl中ssl校验
		//请保证cacert.pem文件在当前文件夹目录中
		$alipay_config['cacert']    = getcwd().'\\cacert.pem';
		$alipay_config['transport']    = 'http';



		 //必填
        $payment_type = "1";//支付类型    
        $notify_url = C('WEBDOMIN').U('Pay/shuangNotifyurl');//服务器异步通知页面路径 
        $return_url = C('WEBDOMIN').U('Pay/shuangReturnurl'); //页面跳转同步通知页面路径
        $seller_email = $alipayconf['selleruname'];//;//卖家支付宝帐户
       
	    $out_trade_no = $alipayconf['order'];//商户订单号    
        $subject = $alipayconf['ordername'];//订单名称
		$body = $alipayconf['orderdescr'];//订单描述
        
		$price = $alipayconf['money'];//付款金额
        $quantity =$alipayconf['goodsnum'];//商品数量
        $logistics_fee = "0.00";//物流费用即运费
        $logistics_type = "EXPRESS";//物流类型
        $logistics_payment = "SELLER_PAY";//物流支付方式
        //，两个值可选：SELLER_PAY（卖家承担运费）、BUYER_PAY（买家承担运费）
        
		//必填
		
		
        //选填
        $show_url = $alipayconf['orderdescr'];//商品展示地址
        $receive_name = $alipayconf['receivename'];//收货人姓名
        $receive_address = $alipayconf['receiveadr'];//收货人地址
        $receive_zip = $alipayconf['receivezip'];//收货人邮编
        $receive_phone = $alipayconf['receivephone'];//收货人电话号码
        $receive_mobile = $alipayconf['receivemobile'];//收货人手机号码
		//选填
//构造要请求的参数数组，无需改动
$parameter = array(
		"service" => "trade_create_by_buyer",
		"partner" => trim($alipay_config['partner']),
		"payment_type"	=> $payment_type,
		"notify_url"	=> $notify_url,
		"return_url"	=> $return_url,
		"seller_email"	=> $seller_email,
		"out_trade_no"	=> $out_trade_no,
		"subject"	=> $subject,
		"price"	=> $price,
		"quantity"	=> $quantity,
		"logistics_fee"	=> $logistics_fee,
		"logistics_type"	=> $logistics_type,
		"logistics_payment"	=> $logistics_payment,
		"body"	=> $body,
		"show_url"	=> $show_url,
		"receive_name"	=> $receive_name,
		"receive_address"	=> $receive_address,
		"receive_zip"	=> $receive_zip,
		"receive_phone"	=> $receive_phone,
		"receive_mobile"	=> $receive_mobile,
		"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
);

//建立请求
$alipaySubmit = new \Ainiku\Alipay\AlipaySubmit($alipay_config);
$html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
echo $html_text;
exit();
		}
	public function tradenNotifyurl(){
		//计算得出通知验证结果
		$alipayNotify = new \Ainiku\Alipay\AlipayNotify($alipay_config);
		$verify_result = $alipayNotify->verifyNotify();
		if($verify_result) {
			//验证成功
			$out_trade_no = $_POST['out_trade_no'];//商户订单号
			$trade_no = $_POST['trade_no'];//支付宝交易号
			$trade_status = $_POST['trade_status'];//交易状态
			//初始化模型更新订单状态
			$model=D('order');
			$model->where("ordernum='$out_trade_no'")->save(array('status'=>$trade_status,'alipaynum'=>$trade_no));
				
			if($_POST['trade_status'] == 'WAIT_BUYER_PAY') {
//该判断表示买家已在支付宝交易管理中产生了交易记录，但没有付款
			}else if($_POST['trade_status'] == 'WAIT_SELLER_SEND_GOODS') {
//该判断表示买家已在支付宝交易管理中产生了交易记录且付款成功，但卖家没有发货
			}else if($_POST['trade_status'] == 'WAIT_BUYER_CONFIRM_GOODS'){
//该判断表示卖家已经发了货，但买家还没有做确认收货的操作
			}else if($_POST['trade_status'] == 'TRADE_FINISHED') {
//该判断表示买家已经确认收货，这笔交易完成
			}else {
				//其它状态
				}
		}else{
			//验证失败
    		echo "fail";
			}
		}
	public function tradeReturnurl(){
		//计算得出通知验证结果
		$alipayNotify = new \Ainiku\Alipay\AlipayNotify($alipay_config);
		$verify_result = $alipayNotify->verifyNotify();
		if($verify_result) {
			$out_trade_no = $_GET['out_trade_no'];//商户订单号
			$trade_no = $_GET['trade_no'];//支付宝交易号
			$trade_status = $_GET['trade_status'];//交易状态
			//验证成功
			 if($_GET['trade_status'] == 'WAIT_SELLER_SEND_GOODS') {
			 }else{
				 echo "trade_status=".$_GET['trade_status'];
				 }
			$success=A('Alipay');
			$suc->suc($out_trade_no,$trade_no);
			echo "验证成功<br />";
			echo "trade_no=".$trade_no;	
		}else{
			echo "验证失败";
			}
		}
	
	public function directNotifyurl(){
		//计算得出通知验证结果
		$alipayNotify = new AlipayNotify($alipay_config);
		$verify_result = $alipayNotify->verifyNotify();
		
		if($verify_result) {//验证成功
			$out_trade_no = $_POST['out_trade_no'];
			$trade_no = $_POST['trade_no'];
			$trade_status = $_POST['trade_status'];
			//初始化模型更新订单状态
			$model=D('order');
			$model->where("ordernum='$out_trade_no'")->save(array('status'=>$trade_status,'alipaynum'=>$trade_no));
			if($_POST['trade_status'] == 'TRADE_FINISHED') {
//该种交易状态只在两种情况下出现
//1、开通了普通即时到账，买家付款成功后。
//2、开通了高级即时到账，从该笔交易成功时间算起，过了签约时的可退款时限（如：三个月以内可退款、一年以内可退款等）后。	
			}else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
//该种交易状态只在一种情况下出现——开通了高级即时到账，买家付款成功后	
			}
			
			echo "success";	
		}else{
		    //验证失败
   			 echo "fail";	
			}
	}
	public function directReturnurl(){
		//计算得出通知验证结果
		$alipayNotify = new AlipayNotify($alipay_config);
		$verify_result = $alipayNotify->verifyReturn();
		if($verify_result) {//验证成功
			$out_trade_no = $_POST['out_trade_no'];
			$trade_no = $_POST['trade_no'];
			$trade_status = $_POST['trade_status'];
			if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
//判断该笔订单是否在商户网站中已经做过处理
//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
//如果有做过处理，不执行商户的业务程序
			}else {
			  echo "trade_status=".$_GET['trade_status'];
			}
			$success=A('Alipay');
			$suc->suc($out_trade_no,$trade_no);
			echo "验证成功<br />";		
		}else{
			 echo "验证失败";
			}
	}
	
	public function shuangNotifyurl(){
		//计算得出通知验证结果
		$alipayNotify = new AlipayNotify($alipay_config);
		$verify_result = $alipayNotify->verifyReturn();
		if($verify_result) {//验证成功
			$out_trade_no = $_POST['out_trade_no'];
			$trade_no = $_POST['trade_no'];
			$trade_status = $_POST['trade_status'];
			//初始化模型更新订单状态
			$model=D('order');
			$model->where("ordernum='$out_trade_no'")->save(array('status'=>$trade_status,'alipaynum'=>$trade_no));
			if($_POST['trade_status'] == 'WAIT_BUYER_PAY') {
//该判断表示买家已在支付宝交易管理中产生了交易记录，但没有付款

//判断该笔订单是否在商户网站中已经做过处理
//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
//如果有做过处理，不执行商户的业务程
				echo "success";
			}else if($_POST['trade_status'] == 'WAIT_SELLER_SEND_GOODS') {
//该判断表示买家已在支付宝交易管理中产生了交易记录且付款成功，但卖家没有发货

//判断该笔订单是否在商户网站中已经做过处理
//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
//如果有做过处理，不执行商户的业务程序
				echo "success";
			}else if($_POST['trade_status'] == 'WAIT_BUYER_CONFIRM_GOODS') {
//该判断表示卖家已经发了货，但买家还没有做确认收货的操作

//判断该笔订单是否在商户网站中已经做过处理
//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
//如果有做过处理，不执行商户的业务程序
				echo "success";
			}else if($_POST['trade_status'] == 'TRADE_FINISHED') {
//该判断表示买家已经确认收货，这笔交易完成

//判断该笔订单是否在商户网站中已经做过处理
//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
//如果有做过处理，不执行商户的业务程序
				echo "success";
			}else {
//其他状态判断
				echo "success";
			}
			echo "验证成功<br />";
		}else{
			 echo "验证失败";
			}	

	}
	public function shuangReturnurl(){
		//计算得出通知验证结果
		$alipayNotify = new AlipayNotify($alipay_config);
		$verify_result = $alipayNotify->verifyReturn();
		if($verify_result) {//验证成功
			$out_trade_no = $_POST['out_trade_no'];
			$trade_no = $_POST['trade_no'];
			$trade_status = $_POST['trade_status'];		
    if($_GET['trade_status'] == 'WAIT_SELLER_SEND_GOODS') {
//判断该笔订单是否在商户网站中已经做过处理
//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
//如果有做过处理，不执行商户的业务程序
    }
	else if($_GET['trade_status'] == 'TRADE_FINISHED') {
//判断该笔订单是否在商户网站中已经做过处理
//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
//如果有做过处理，不执行商户的业务程序
    }
    else {
      echo "trade_status=".$_GET['trade_status'];
    }
	$success=A('Alipay');
	$suc->suc($out_trade_no,$trade_no);
	echo "验证成功<br />";
	echo "trade_no=".$trade_no;
		}else{
			 echo "验证失败";
			}
	}
}