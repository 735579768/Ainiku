<?php
namespace Common\Controller;
use Think\Controller;

class CustomFormController extends Controller {
	private $filename;
	public function __construct($metch, $name, $value) {
		parent::__construct();
		$this->filename = $metch;
		$data['name']   = $name;
		$data['value']  = $value;
		$this->assign('data', $data);
	}
	//云标签表单
	public function tag($name = null, $value = null) {
		//查找系统标签
		$_list = M('tag')->order('id desc')->select();
		$this->assign('_list', $_list);
		return $this->fetch(T('Common@Widget/CustomForm/' . $this->filename));
	}
	public function testmail($name = null, $value = null) {
		return $this->fetch(T('Common@Widget/CustomForm/' . $this->filename));
	}
}