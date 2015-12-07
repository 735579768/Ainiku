<?php
$spname = "财付通双接口测试";
$partner = SHANGHU_ID; //财付通商户号
$key = SHANGHU_KEY; //财付通密钥

$return_url = C('WEBDOMIN') . C('return_url'); //显示支付结果页面,*替换成payReturnUrl.php所在路径
$notify_url = C('WEBDOMIN') . C('notify_url'); //支付完成后的回调处理页面,*替换成payNotifyUrl.php所在路径
?>