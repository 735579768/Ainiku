<?php
namespace Admin\Model;
use Think\Model;
if(!defined("ACCESS_ROOT"))die("Invalid access");
class SingleModel extends BaseModel {
	    protected $_validate = array(
        array('title', 'require', '标题不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
		array('title', '','已经存在同样标题', 0, 'unique',1)
    );
	    protected $_auto = array(
      //  array('model', 'arr2str', self::MODEL_BOTH, 'function'),
	    array('name', 'getname',  self::MODEL_BOTH, 'callback', self::MODEL_BOTH),
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_BOTH),
        array('status', '1', self::MODEL_INSERT),
    );
	function getname($name=null){
		if(empty($name)){
			return Pinyin(I('title'));
			}else{
			return $name;	
				}
		}
}