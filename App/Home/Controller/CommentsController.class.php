<?php
namespace Home\Controller;
use Think\Controller;

class CommentsController extends HomeController {
	public function add($verify = null) {
		if (IS_POST) {
			if (empty($verify)) {$this->error('请输入验证码!');}
			if (!check_verify($verify)) {
				$this->error('验证码输入错误！');
			}
			$model = D('Comments');
			if (!$model->create()) {
				$this->error($model->geterror());
			} else {
				$result = $model->add();
				if (0 < $result) {
					$this->success('添加成功');
				} else {
					$this->error('添加失败');
				}
			}
		} else {
			$this->redirect(U('/'));
		}
	}
}