<?php
if(!defined("ACCESS_ROOT"))die("Invalid access");
return array(
    THINK_PATH.'Common/functions.php',
    COMMON_PATH.'Common/function.php',
    CORE_PATH . 'Think'.EXT,
    CORE_PATH . 'Hook'.EXT,
    CORE_PATH . 'App'.EXT,
    CORE_PATH . 'Dispatcher'.EXT,
    CORE_PATH . 'Model'.EXT,
    CORE_PATH . 'Log'.EXT,
    CORE_PATH . 'Log/Driver/File'.EXT,
    CORE_PATH . 'Route'.EXT,
    CORE_PATH . 'Controller'.EXT,
    CORE_PATH . 'View'.EXT,
    CORE_PATH . 'Storage'.EXT,
    CORE_PATH . 'Storage/Driver/File'.EXT,
    CORE_PATH . 'Exception'.EXT,
    BEHAVIOR_PATH . 'ParseTemplateBehavior'.EXT,
    BEHAVIOR_PATH . 'ContentReplaceBehavior'.EXT,
	
	


	//COMMON_PATH.'Common/addons.php',
	//COMMON_PATH.'Common/form.php',
	//COMMON_PATH.'Common/getinfo.php',
	//COMMON_PATH.'Common/goods.php',
	//COMMON_PATH.'Common/image.php',
	//COMMON_PATH.'Common/savefilter.php',
);