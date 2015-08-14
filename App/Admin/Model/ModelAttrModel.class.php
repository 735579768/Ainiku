<?php
namespace Admin\Model;
use Think\Model;
if(!defined("ACCESS_ROOT"))die("Invalid access");
class ModelAttrModel extends BaseModel {
	    protected $_validate = array(
        array('title', 'require', '标题不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
//		array('name', '','已经存在同样标识', self::EXISTS_VALIDATE, 'unique'),
//		array('field', '','已经存在同样字段', self::EXISTS_VALIDATE, 'unique'),
    );
}