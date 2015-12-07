<?php
namespace Admin\Model;
use Think\Model;
defined("ACCESS_ROOT") || die("Invalid access");
class LinkModel extends BaseModel {
	    protected $_validate = array(
        array('title', 'require', '标题不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
		array('title', '','已经存在同样标题', self::EXISTS_VALIDATE, 'unique')
    );

    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_BOTH),
        //array('status', '1', self::MODEL_INSERT),
    );
}