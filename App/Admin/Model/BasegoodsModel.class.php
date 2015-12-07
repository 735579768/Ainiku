<?php
// | Author: 枫叶 <735579768@qq.com> <http://www.zhaokeli.com>
// +----------------------------------------------------------------------
namespace Admin\Model;
use Think\Model;

defined("ACCESS_ROOT") || die("Invalid access");

/**
 * 用户模型
 * @author 寞枫 <735579768@qq.com>
 */

class BasegoodsModel extends Model {
	/* 用户模型自动验证 */
//    protected $_validate = array(
	//		array('title', 'require', '标题不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH)
	//    );
	protected $_auto = array(
		array('create_time', NOW_TIME, self::MODEL_INSERT),
		array('update_time', NOW_TIME, self::MODEL_BOTH),
		//array('status', '1', self::MODEL_INSERT),
	);
//处理checkbox数据
	public function create($data = '', $type = '') {
		$data = parent::create($data, $type);
		if (!empty($data['attribute'])) {
			$ma = M('GoodsAttribute');
			if ($ma->create($data['attribute'])) {
			} else {

			}
		}
		return $data;
	}
}
