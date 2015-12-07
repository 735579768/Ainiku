<?php
namespace Admin\Model;
use Think\Model;
defined("ACCESS_ROOT") || die("Invalid access");
class ModelAttrModel extends BaseModel {
	    protected $_validate = array(
        array('title', 'require', '标题不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
    );
	protected $_auto = array(
        array('name', 'strtolower1', self::MODEL_BOTH, 'callback', 1),
		array('field', 'strtolower1', self::MODEL_BOTH, 'callback', 1)
        //array('status', '1', self::MODEL_INSERT),
    );
	protected function strtolower1($str){
        return str_replace(' ','',strtolower($str));
    }
}