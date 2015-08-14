<?php
require_once("../API/qqConnectAPI.php");
//require_once("../../Application/User/Api/UserApi.class.php");
$qc = new QC();
echo $qc->qq_callback().'---';
$openid=$qc->get_openid();
echo $openid;
/* 调用注册接口注册用户 */
//$User = new UserApi;
//var_dump($user);
//$uid = $User->QQregister($openid);
//echo $uid;
