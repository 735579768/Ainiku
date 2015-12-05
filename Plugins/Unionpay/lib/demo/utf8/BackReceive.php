﻿<?php
include_once $_SERVER ['DOCUMENT_ROOT'] . '/zhifusdk/utf8/func/common.php';
include_once $_SERVER ['DOCUMENT_ROOT'] . '/zhifusdk/utf8/func/secureUtil.php';
include_once $_SERVER ['DOCUMENT_ROOT'] . '/zhifusdk/utf8/func/log.class.php';
$log = new PhpLog ( SDK_LOG_FILE_PATH, "PRC", SDK_LOG_LEVEL );
$log->LogInfo ( "-------后台交易成功数据".json_encode($_POST) );
?>
<!DOCTYPE unspecified PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>响应页面</title>

<style type="text/css">
body table tr td {
	font-size: 14px;
	word-wrap: break-word;
	word-break: break-all;
	empty-cells: show;
}
</style>
</head>
<body>
	<table width="800px" border="1" align="center">
		<tr>
			<th colspan="2" align="center">响应结果</th>
		</tr>
	
			<?php
			foreach ( $_POST as $key => $val ) {
				?>
			<tr>
			<td width='30%'><?php echo isset($mpi_arr[$key]) ?$mpi_arr[$key] : $key ;?></td>
			<td><?php echo $val ;?></td>
		</tr>
			<?php }?>
			<tr>
			<td width='30%'>验证签名</td>
			<td><?php			
			if (isset ( $_POST ['signature'] )) {
				file_put_contents(dirname(__FILE__).'\\',json_encode($_POST));
				$log = new PhpLog ( SDK_LOG_FILE_PATH, "PRC", SDK_LOG_LEVEL );
				$log->LogInfo ( "============支付成功后台接心数据===============" );
				$log->LogInfo (json_encode($_POST) );
				echo verify ( $_POST ) ? '验签成功' : '验签失败';
				$orderId = $_POST ['orderId']; //其他字段也可用类似方式获取
			} else {
				echo '签名为空';
			}
			?></td>
		</tr>
	</table>
</body>
</html>
