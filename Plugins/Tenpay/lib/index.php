<?php
   require_once ("classes/RequestHandler.class.php");
   require_once ("tenpay_config.php");
  $curDateTime = date("YmdHis");
 
  
  //date_default_timezone_set(PRC);
		$strDate = date("Ymd");
		$strTime = date("His");
		
		//4位随机数
		$randNum = rand(1000, 9999);
		
		//10位序列号,可以自行调整。
		$strReq = $strTime . $randNum;
		 /* 商家的定单号 */
  	$mch_vno = $curDateTime . $randNum;
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<TITLE>财付通付款通道</TITLE>
<META http-equiv=Content-Type content="text/html; charset=utf-8">
<META 
content=网上购物/网上支付/安全支付/安全购物/购物，安全/支付,安全/财付通/安全,支付/安全，购物/支付,在线/付款,收款/网上,贸易/网上贸易. 
name=description>
<META 
content=网上购物/网上支付/安全支付/安全购物/购物，安全/支付,安全/财付通/安全,支付/安全，购物/支付,在线/付款,收款/网上,贸易/网上贸易. 
name=keywords>
<META content="MSHTML 6.00.3790.2577" name=GENERATOR>
</HEAD>
<BODY topMargin=0>
<div>
    <script language="javascript">
	function payFrm()
	{
		if (directFrm.order_no.value=="")
		{
			alert("提醒：请填写订单编号；如果无特定的订单编号，请采用默认编号！（刷新一下页面就可以了）");
			directFrm.order_no.focus();
			return false;
		}
		if (directFrm.product_name.value=="")
		{
			alert("提醒：请填写商品名称(付款项目)！");
			directFrm.product_name.focus();
			return false;
		}
		if (directFrm.order_price.value=="")
		{
			alert("提醒：请填写订单的交易金额！");
			directFrm.order_price.focus();
			return false;
		}
		
		if (directFrm.remarkexplain.value=="")
		{
			alert("提醒：请填写您的简要说明！");
			directFrm.remarkexplain.focus();
			return false;
		}
		if (directFrm.remarkexplain.value.length>31)
		{
			alert("提醒：超出规定的字数,请重新输入");
  			event.returnValue=false;   
  			return   false;   
		}
		
		return true;
	}
  </script>
    <form action='tenpay.php' method='post' name='directFrm' onSubmit="return payFrm();">
 收款方：<? echo  $spname ?> 
 <br>订单编号：<input type="text" name="order_no" maxlength="50" size="18" readonly value="<?php echo $mch_vno ?>" font style="color:#000000;font-size:14px;">
<br> 商品名称： <input name="product_name" type="text" size="18" maxlength="50" font style="color:#000000;font-size:14px;">
<br> 付款金额：<input type="text" name="order_price" maxlength="50" size="18" onKeyUp="if(isNaN(value))execCommand('undo')" onafterpaste="if(isNaN(value))execCommand('undo')" font style="color:#000000;font-size:14px;">
                      元（格式：500.01）
<br>支付方式：     <input type="radio" name="trade_mode" value="1"  checked="true">
                      即时到帐&nbsp;
                      <input type="radio" name="trade_mode" value="2">
                      中介担保&nbsp;
                      <input type="radio" name="trade_mode" value="3">
                      后台选择   

<br>简要说明：    <textarea name="remarkexplain" cols="40" rows="5" id="remark2"  font style="color:#000000;font-size:14px;"></textarea>
                      <br>
                      请填写您订单的简要说明（30字以内）    
<br> <input name="submit" type="image" src="image/next.gif" alt="使用财付通安全支付" width="103" height="27">                              
    </form>
</div>
</body>
</html>
