<?php
namespace Home\Controller;
use Think\Controller;
if(!defined("ACCESS_ROOT"))die("Invalid access");
class BuyController extends LoginController {
 public function checkout(){
 	$this->display('checkout');
 }
 public function addaddress(){
 	$this->success($this->fetch('addaddress'));
 	die();
 }
}
