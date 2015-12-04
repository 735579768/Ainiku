<?php
// | Author: 枫叶 <735579768@qq.com> <http://www.zhaokeli.com>
// +----------------------------------------------------------------------
namespace Home\Model;
use Think\Model;

if(!defined("ACCESS_ROOT"))die("Invalid access");

/**
 * 用户模型
 * @author 寞枫 <735579768@qq.com>
 */

class ConsigneeAddressModel extends Model {
    /* 用户模型自动验证 */
    protected $_validate = array(
      array('consignee_name', 'require', '请填写收货人姓名!'),
      array('consignee_mobile', 'require', '请填写收货人电话!'),
      array('consignee_diqu', 'require', '请填写收货人地址!'),
      array('consignee_detail', 'require', '请填写详细收货地址!'),
      array('consignee_youbian', 'require', '请填写邮政编码!'),
      array('consignee_name', '1,4', '姓名长度不正确', self::EXISTS_VALIDATE, 'length'), //邮箱长度不合法
        /* 验证手机号码 */
        array('consignee_mobile', '/^[1][0-9]{10}$/', '手机格式不正确', self::EXISTS_VALIDATE,'regex'), //手机格式不正确 TODO:
        array('consignee_youbian', '/^\d+$/', '邮政编码格式不正确', self::EXISTS_VALIDATE,'regex'), //手机格式不正确 TODO:
       // array('consignee_mobile', 'checkDenyMobile', '手机禁止注册', self::EXISTS_VALIDATE, 'callback'), //手机禁止注册
       // array('consignee_mobile', '', '手机号已经注册', self::EXISTS_VALIDATE, 'unique'), //手机号被占用
    );
    /* 用户模型自动完成 */
    protected $_auto = array(
      //  array('reg_ip', 'get_client_ip', self::MODEL_INSERT, 'function', 1),
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_BOTH)
        array('uid', UID, self::MODEL_BOTH)
    );
  
}
