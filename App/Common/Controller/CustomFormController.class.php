<?php
namespace Common\Controller;
use Think\Controller;

/**
 * 系统自定义表单类
 */
class CustomFormController extends Controller {
	private $filename;
	public function __construct($metch, $name, $value) {
		parent::__construct();
		$this->filename = $metch;
		$data['name']   = $name;
		$data['value']  = $value;
		$this->assign('data', $data);
	}
	/**
	 * 云标签表单
	 * @param  [type] $name  表单name
	 * @param  [type] $value 表单值
	 * @return [type]        [description]
	 */
	public function tag($name = null, $value = null) {
		//查找系统标签
		$_list = M('tag')->order('id desc')->select();
		$this->assign('_list', $_list);
		return $this->fetch(T('Common@Widget/CustomForm/' . $this->filename));
	}
	/**
	 * 发送测试邮件
	 * @param  [type] $name  表单name
	 * @param  [type] $value 表单值
	 * @return [type]        [description]
	 */
	public function testmail($name = null, $value = null) {
		return $this->fetch(T('Common@Widget/CustomForm/' . $this->filename));
	}
}