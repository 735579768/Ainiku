<?php
namespace Admin\Model;
use Think\Model;
if(!defined("ACCESS_ROOT"))die("Invalid access");
class ModelModel extends BaseModel {
	    protected $_validate = array(
		array('title','require','模型名字必须填写'), //默认情况下用正则进行验证
		array('title', '','模型名字已经被占用', self::EXISTS_VALIDATE, 'unique'),
		array('name','require','模型标识不能为空'), //默认情况下用正则进行验证
		array('name', '','模型标识已经被占用', self::EXISTS_VALIDATE, 'unique'),
		array('table','require','数据表不能为空'), //默认情况下用正则进行验证
		array('table', '','数据表已经被占用', self::EXISTS_VALIDATE, 'unique')
    );
}