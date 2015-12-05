<?php
// cvn2加密 1：加密 0:不加密
const SDK_CVN2_ENC = 0;
// 有效期加密 1:加密 0:不加密
const SDK_DATE_ENC = 0;
// 卡号加密 1：加密 0:不加密
const SDK_PAN_ENC = 0;
//商户id
const MEMBER_ID=UNIONPAY_MEMBER_ID; 
 
// ######(以下配置为PM环境：入网测试环境用，生产环境配置见文档说明)#######
// 签名证书路径
//echo str_replace('\utf8\func','',dirname(__FILE__)). '\PM_700000000000001_acp.pfx';

define('SDK_SIGN_CERT_PATH',UNIONPAY_PATH.'/lib/PM_700000000000001_acp.pfx');
//define('SDK_SIGN_CERT_PATH',UNIONPAY_PATH.'/lib/jiuwozhifu.pfx');
//const SDK_SIGN_CERT_PATH = 'D:/wwwroot/0yuanwang.com/zhifusdk/jiuwozhifu.pfx';

// 签名证书密码
const SDK_SIGN_CERT_PWD = '000000';
//const SDK_SIGN_CERT_PWD = '735579';

// 密码加密证书（这条用不到的请随便配）
define('SDK_ENCRYPT_CERT_PATH',UNIONPAY_PATH.'/lib/verify_sign_acp.cer');

// 验签证书路径（请配到文件夹，不要配到具体文件）
define('SDK_VERIFY_CERT_DIR',UNIONPAY_PATH.'/lib/');

// 前台请求地址
const SDK_FRONT_TRANS_URL = 'https://101.231.204.80:5000/gateway/api/frontTransReq.do';
//const SDK_FRONT_TRANS_URL = 'https://gateway.95516.com/gateway/api/frontTransReq.do';

// 后台请求地址
const SDK_BACK_TRANS_URL = 'https://101.231.204.80:5000/gateway/api/backTransReq.do';
//const SDK_BACK_TRANS_URL = 'https://gateway.95516.com/gateway/api/backTransReq.do';

// 批量交易
const SDK_BATCH_TRANS_URL = 'https://101.231.204.80:5000/gateway/api/batchTrans.do';
//const SDK_BATCH_TRANS_URL = 'https://gateway.95516.com/gateway/api/batchTrans.do';

//单笔查询请求地址
const SDK_SINGLE_QUERY_URL = 'https://101.231.204.80:5000/gateway/api/queryTrans.do';
//const SDK_SINGLE_QUERY_URL = 'https://gateway.95516.com/gateway/api/queryTrans.do';

//文件传输请求地址
const SDK_FILE_QUERY_URL = 'https://101.231.204.80:9080/';
//const SDK_FILE_QUERY_URL = 'https://filedownload.95516.com/';

//有卡交易地址
const SDK_Card_Request_Url = 'https://101.231.204.80:5000/gateway/api/cardTransReq.do';
//const SDK_Card_Request_Url = 'https://gateway.95516.com/gateway/api/cardTransReq.do';

//App交易地址
const SDK_App_Request_Url = 'https://101.231.204.80:5000/gateway/api/appTransReq.do';
//const SDK_App_Request_Url = 'https://gateway.95516.com/gateway/api/appTransReq.do';


// 前台通知地址 (商户自行配置通知地址)
define('SDK_FRONT_NOTIFY_URL', C('WEBDOMIN').C('return_url'));
// 后台通知地址 (商户自行配置通知地址)
define('SDK_BACK_NOTIFY_URL',C('WEBDOMIN').C('notify_url'));

//文件下载目录 
define('SDK_FILE_DOWN_PATH',UNIONPAY_PATH.'/lib/files/');

//日志 目录 
define('SDK_LOG_FILE_PATH',UNIONPAY_PATH.'/lib/logs/');

//日志级别
const SDK_LOG_LEVEL = 'INFO';
	
?>