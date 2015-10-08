<?php
//Author: 枫叶 <735579768@qq.com> <http://www.zhaokeli.com>
namespace Addon\Admin\Controller;
use Admin\Controller\AdminController;
if(!defined("ACCESS_ROOT"))die("Invalid access");
class InfoController extends AdminController{
	public function index(){
		$this->display('Addon/index/index');
		}
}
