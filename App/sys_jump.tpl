<?php
    if(C('LAYOUT_ON')) {
        echo '{__NOLAYOUT__}';
    }
?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>页面跳转</title>
</head>
<style>
.system-message{ padding:10px;font:14px/1.7 microsoft yahei, Consolas, "Andale Mono", "Lucida Console", "Lucida Sans Typewriter", Monaco, "Courier New", monospace; border:solid 1px #ccc;margin:0px auto; margin-top:150px; width:500px; height:200px;box-shadow:2px 3px 8px #ccc;}
.system-message .system-title{ text-align:center; font-size:25px; font-weight:bolder;}
.sys-success,.sys-error{ margin:0px auto; height:70px; width:70px;}
.system-message .sys-success{ background:url(__STATIC__/images/message.gif) -70px -40px no-repeat;}
.system-message .sys-error{ background:url(__STATIC__/images/message.gif) 0px -40px no-repeat; }
.system-message h1{ font-style:normal; font-size:14px;}
.system-message .jump{ text-align:center;}
.system-message .errorstr,.system-message .successstr{ border-top:solid 1px #ccc; text-align:center; width:80%; margin:0px auto; padding:20px 0px; font-size:20px;}
.errorstr{ color:#f00;}
</style>
<body>
<div class="system-message">
<?php if(isset($message)) {?>
<div class="system-title sys-success"></div>
<p class="successstr"><?php echo($message); ?></p>
<?php }else{?>
<div class="system-title sys-error" style="color:#f00;"></div>
<p class="errorstr"><?php echo($error); ?></p>
<?php }?>
<p class="jump">
页面自动<a id="href" href="<?php echo($jumpUrl); ?>">跳转</a> 等待时间： <b id="wait"><?php echo($waitSecond); ?></b>
</p>
</div>
<script type="text/javascript">
(function(){
var wait = document.getElementById('wait'),href = document.getElementById('href').href;
var interval = setInterval(function(){
	var time = --wait.innerHTML;
	if(time <= 0) {
		location.href = href;
		clearInterval(interval);
	};
}, 1000);
})();
</script>
</body>
</html>
