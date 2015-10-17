<?php
if(!defined("ACCESS_ROOT"))die("Invalid access");
return array(		
	'REG_EMAIL'=>'^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)+$',
	'REG_MOBILE'=>'^[1][0-9]{10}$',
	'REG_URL'=>'^http(s?):\/\/(?:[A-za-z0-9-]+\.)+[A-za-z]{2,4}(:\d+)?(?:[\/\?#][\/=\?%\-&~`@[\]\':+!\.#\w]*)?$',
	'REG_NUMBER'=>'^\d+$',
	'REG_DOUBLE'=>'^\d+(\.\d+)?$',
	'REG_NULL_LINE'=>'^\s*?\r?\n$',
);
