<?php

namespace Admin\Model;
use Think\Model;

/**
 * 文件模型
 * 负责文件的下载和上传
 */
defined("ACCESS_ROOT") || die("Invalid access");
class FileModel extends BaseModel{
    /**
     * 文件模型自动完成
     * @var array
     */
    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
		array('uid', UID, self::MODEL_INSERT),
    );
}