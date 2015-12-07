<?php
// | Author: 枫叶 <735579768@qq.com> <http://www.zhaokeli.com>
// +----------------------------------------------------------------------
namespace Home\Model;
use Think\Model;

defined("ACCESS_ROOT") || die("Invalid access");

/**
 * 用户模型
 * @author 寞枫 <735579768@qq.com>
 */

class MemberModel extends Model {
    /* 用户模型自动验证 */
    protected $_validate = array(
        array('member_group_id', 'require', '请选择用户组'),
		/* 验证用户名 */
        array('username', '4,30', '用户名长度不合法', self::MUST_VALIDATE , 'length'), //用户名长度不合法
        array('username', 'checkDenyMember',' 用户名禁止注册', self::MUST_VALIDATE , 'callback'), //用户名禁止注册
        array('username', '', '用户名被占用', self::MUST_VALIDATE , 'unique',3), //用户名被占用
        /* 验证密码 */
        array('password', '5,30',' 密码长度不合法', self::EXISTS_VALIDATE, 'length'), //密码长度不合法
//        /* 验证邮箱 */
        array('email', 'email', '邮箱格式不正确', self::EXISTS_VALIDATE), //邮箱格式不正确
        array('email', '1,32', '邮箱长度不合法', self::EXISTS_VALIDATE, 'length'), //邮箱长度不合法
        array('email', 'checkDenyEmail', '邮箱禁止注册', self::EXISTS_VALIDATE, 'callback'), //邮箱禁止注册
        array('email', '', '邮箱已经注册', self::EXISTS_VALIDATE, 'unique',1), //邮箱被占用
        /* 验证手机号码 */
        array('mobile', '//', '手机格式不正确', self::EXISTS_VALIDATE), //手机格式不正确 TODO:
        array('mobile', 'checkDenyMobile', '手机禁止注册', self::EXISTS_VALIDATE, 'callback'), //手机禁止注册
        array('mobile', '', '手机号已经注册', self::EXISTS_VALIDATE, 'unique'), //手机号被占用
    );
    /* 用户模型自动完成 */
    protected $_auto = array(
        array('reg_ip', 'get_client_ip', self::MODEL_INSERT, 'function', 1),
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_BOTH)
        //array('status', '1', self::MODEL_INSERT),
    );
   /**
     * 检测用户名是不是被禁止注册
     * @param  string $username 用户名
     * @return boolean          ture - 未禁用，false - 禁止注册
     */
    protected function checkDenyMember($username){
        return true; //TODO: 暂不限制，下一个版本完善
    }

    /**
     * 检测邮箱是不是被禁止注册
     * @param  string $email 邮箱
     * @return boolean       ture - 未禁用，false - 禁止注册
     */
    protected function checkDenyEmail($email){
        return true; //TODO: 暂不限制，下一个版本完善
    }

    /**
     * 检测手机是不是被禁止注册
     * @param  string $mobile 手机
     * @return boolean        ture - 未禁用，false - 禁止注册
     */
    protected function checkDenyMobile($mobile){
        return true; //TODO: 暂不限制，下一个版本完善
    }

}
