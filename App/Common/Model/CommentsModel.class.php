<?php
namespace Common\Model;
use Think\Model;

/**
 * 用户模型
 * @author 寞枫 <735579768@qq.com>
 */

class CommentsModel extends Model {
	/* 用户模型自动验证 */
	protected $_validate = array(

		/* 验证标识符 */
		array('name', 'require', '姓名不能空', self::EXISTS_VALIDATE),
		array('email', 'require', '邮箱不能空', self::EXISTS_VALIDATE),
		array('email', 'email', '邮箱格式不对', self::EXISTS_VALIDATE),
		array('content', 'require', '评论内容不能空', self::EXISTS_VALIDATE),
	);
	protected $_auto = array(
		array('status', '1'), // 新增的时候把status字段设置为1
		array('create_time', 'time', 1, 'function'), // 写入当前时间戳
		array('ip', 'get_client_ip', self::MODEL_INSERT, 'function', 0),
		array('content', 'filter_html', self::MODEL_INSERT, 'function'),
	);

}
