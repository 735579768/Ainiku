<?php
namespace Admin\Controller;
if(!defined("ACCESS_ROOT"))die("Invalid access");
class AjaxController extends AdminController {
 function admininfo(){
	 echo hook('Admininfo');
	 die();
	 }
}