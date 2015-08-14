<?php
namespace Admin\Model;
use Think\Model;
if(!defined("ACCESS_ROOT"))die("Invalid access");
class ArticleModel extends BaseModel {
	    protected $_validate = array(
        array('title', 'require', '文章标题不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
		array('title', '','已经存在同样标题的文章', self::EXISTS_VALIDATE, 'unique'),
    	array('meta_title', '1,50', '网页标题不能超过50个字符', self::VALUE_VALIDATE , 'length', self::MODEL_BOTH),
    	array('keywords', '1,255', '网页关键字不能超过255个字符', self::VALUE_VALIDATE , 'length', self::MODEL_BOTH),
    	array('meta_title', '1,255', '网页描述不能超过255个字符', self::VALUE_VALIDATE , 'length', self::MODEL_BOTH)
    );

    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_BOTH),
        //array('status', '1', self::MODEL_INSERT),
    );
}