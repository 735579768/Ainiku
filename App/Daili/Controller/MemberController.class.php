<?php
namespace Daili\Controller;
class MemberController extends DailiController {
	public function __construct() {
		parent::__construct();
		$this->model_name = 'Member';
		//$this->primarykey='id';
	}
	public function index() {
		$title   = I('title');
		$groupid = I('groupid');
		$map     = array();
		if (!empty($title)) {
			$map['username'] = array('like', '%' . $title . '%');
		}

		$map['status']    = array('egt', 0);
		$map['member_id'] = array('NEQ', 1);
		if (!empty($groupid)) {
			$map['member_group_id'] = $groupid;
		}

		$this->pages(array(
			'model' => 'Member',
			'where' => $map,
			'order' => 'status asc,member_id desc',
		));
		$this->meta_title = '用户列表';
		$this->display();
	}
	public function recycling() {
		$title           = I('title');
		$map['username'] = array('like', '%' . $title . '%');
		$map['status']   = array('lt', 0);
		$this->pages(array(
			'model' => 'Member',
			'where' => $map,
			'order' => 'update_time desc,member_id desc',
		));
		$this->meta_title = '用户回收站';
		$this->display();
	}
	public function log() {
		$this->pages(
			array(
				'model' => 'MemberLog',
				'join'  => array(
					__DB_PREFIX__ . 'member as b on b.member_id=' . __DB_PREFIX__ . 'member_log.member_id',
					__DB_PREFIX__ . 'member_group as c on c.member_group_id=b.member_group_id',
				),
				'field' => __DB_PREFIX__ . 'member_log.member_log_id as member_log_id,b.username,c.title as grouptitle,' . __DB_PREFIX__ . 'member_log.ip as ip,' . __DB_PREFIX__ . 'member_log.adr as adr,' . __DB_PREFIX__ . 'member_log.create_time as create_time',
				'order' => __DB_PREFIX__ . 'member_log.member_log_id desc',
			)
		);
		$this->meta_title = '用户登陆日志';
		$this->display();
	}
//	//用户行为日志
	//	public function actionlog(){
	//		$this->pages(
	//		array(
	//			'model'=>'MemberAction',
	//			'join'=>array(
	//			'__DB_PREFIX__.'member as b on b.id=__DB_PREFIX__.'member_action.uid',
	//			'__DB_PREFIX__.'menu as c on c.id=__DB_PREFIX__.'member_action.menuid'
	//					),
	//			'field'=>'__DB_PREFIX__.'member_action.id as id,b.username,c.title as menutitle,__DB_PREFIX__.'member_action.create_time as create_time'
	//		)
	//		);
	//		$this->meta_title='用户操作日志';
	//		$this->display();
	//		}
	public function dellog($member_log_id = null) {
		$id  = I('get.member_log_id');
		$idd = M('Member_log')->where("member_log_id in ($member_log_id)")->delete();
		if (0 < $idd) {
			$this->success(L('_DELETE_SUCCESS_'));
		} else {
			$this->error(L('_CAOZUO_FAIL_'));
		}
	}
//	public function delactionlog(){
	//    	$id=I('get.id');
	//    	$idd=M('Member_action')->where("1")->delete();
	//    	if(0<$idd){
	//    	  $this->success('已经全部清空',U('Member/actionlog'));
	//    	}else{
	//    	  $this->error(L('_CAOZUO_FAIL_'));
	//    	}
	//		}
	public function add($member_group_id = '', $username = '', $password = '', $repassword = '', $email = '') {
		if (IS_POST) {
			/* 检测密码 */
			if ($password != $repassword) {
				$this->error('密码和重复密码不一致！');
			}

			/* 调用注册接口注册用户 */
			$User = D('Member');
			$uid  = $User->register($member_group_id, $username, $password, $email);
			if (0 < $uid) {
				//注册成功
				$this->success(L('_ADD_SUCCESS_'), U('index'));
			} else {
				//注册失败，显示错误信息
				$this->error($this->showRegError($uid));
			}
		} else {
			//$field=Api('Model/memberModel',array('add'));
			$field = get_model_attr('member');
			$this->assign('fieldarr', $field);
			//$this->assign('data',$data);
			$this->meta_title = '新增用户';
			$this->display('edit');
		}
	}
	//编辑用户信息
	function edit($member_id = null) {
		if ($member_id === null) {
			$member_id = I('post.member_id');
		}

		if (empty($member_id)) {
			$this->error(L('_ID_NOT_NULL_'));
		}

		if (IS_POST) {
			//先保存主用户表中的数据
			$model = D('Member');
			if ($model->create()) {
				$result = $model->save();
				if (0 < $result) {
					$this->success(L('_UPDATE_SUCCESS_'));
				} else {
					$this->error(L('_UPDATE_FAIL_'));
				}
			} else {
				$this->error($this->showRegError($model->geterror()));
			}

		} else {
			$field = get_model_attr('member');
			$data  = M('Member')->find($member_id);
			$this->assign('fieldarr', $field);
			$this->assign('data', $data);
			$this->meta_title = '编辑用户信息';
			$this->display();
		}

	}
	/**
	 * 修改密码初始化
	 * @author huajie <banhuajie@163.com>
	 */
	public function updatepwd($member_id = '') {
		if (IS_POST) {
			$model = D('Member');
			if ($model->create()) {
				$model->password = ainiku_ucenter_md5($model->password);
				$result          = $model->save();
				if (0 < $result) {
					$this->success(L('_UPDATE_SUCCESS_'));
				} else {
					$this->error('密码相同,没有更改');
				}
			} else {
				$this->error($this->showRegError($model->geterror()));
			}
		}
		$member_id        = empty($member_id) ? UID : $member_id;
		$data             = M('Member')->find($member_id);
		$this->data       = $data;
		$this->meta_title = '修改密码';
		$this->display();
	}

	/**
	 * 获取用户注册错误信息
	 * @param  integer $code 错误编码
	 * @return string        错误信息
	 */
	private function showRegError($code = 0) {
		switch ($code) {
		case -1:$error = '用户名长度必须在16个字符以内！';
			break;
		case -2:$error = '用户名被禁止注册！';
			break;
		case -3:$error = '用户名被占用！';
			break;
		case -4:$error = '密码长度必须在5-30个字符之间！';
			break;
		case -5:$error = '邮箱格式不正确！';
			break;
		case -6:$error = '邮箱长度必须在1-32个字符之间！';
			break;
		case -7:$error = '邮箱被禁止注册！';
			break;
		case -8:$error = '邮箱被占用！';
			break;
		case -9:$error = '手机格式不正确！';
			break;
		case -10:$error = '手机被禁止注册！';
			break;
		case -11:$error = '手机号被占用！';
			break;
		default:$error = L('_UNKNOWN_ERROR_');
		}
		return $error;
	}
	//放到回收站
	function del($member_id) {
		$member_id = I('get.member_id');
		if ($member_id == '1') {
			$this->error('超级管理员不能删除');
		}

		$uid = M('Member')->where("member_id in($member_id)")->save(array('status' => -1));
		if (0 < $uid) {
			$this->success(L('_TO_RECYCLE_'), U('recycling'));
		} else {
			$this->error(L('_CAOZUO_FAIL_'));
		}
	}
	function dele($member_id) {
		$member_id = I('get.member_id');
		if ($member_id == '1') {
			$this->error('超级管理员不能删除');
		}

		$uid = M('Member')->where("member_id in ($member_id)")->delete();
		//删除用户扩展属性
		M('MemberAttrvalue')->where("member_id=$member_id")->delete();
		if (0 < $uid) {
			$this->success(L('_CHEDI_DELETE_'));
		} else {
			$this->error(L('_CAOZUO_FAIL_'));
		}
	}
	function huifu($member_id) {
		$id  = I('get.member_id');
		$uid = M('Member')->where("member_id in($member_id)")->save(array('status' => 1));
		if (0 < $uid) {
			$this->success(L('_TO_HUIFU_'), U('Member/index'));
		} else {
			$this->error(L('_CAOZUO_FAIL_'));
		}
	}

}