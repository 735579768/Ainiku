<?php
namespace Home\Widget;
use Think\Controller;

class CommentsWidget extends Controller {
	public function add($id = null) {
		$this->id = $id;
		$this->display('Widget:Comments');
	}
	public function lists($id = null) {
		$list = $this->pages('Comments', "article_id=$id"); //(M('Comments')->select();
		//$list=$this->gettree();
		//查询回复
		if (!empty($list)) {
			foreach ($list as $key => $val) {
				$list[$key]['hylist'] = $this->getHaveHuifu($val);
			}
		}
		$this->assign('_list', $list);
		$this->display('Widget:Commentslist');
	}
	//取一个评论树
	private function gettree($id = null) {
		//查询所有评论
		$info = M('Comments')->select();

		//查询回复
		foreach ($info as $key => $val) {
			$info[$key]['hylist'] = $this->getHaveHuifu($val);
		}
		return $info;
	}

	//查询一个评论是不是有回复如果有的话就返回一个数组没有的话就返回false
	//@info 参数是一个评论信息数组
	private function getHaveHuifu($info) {
		return M('Comments')->where("pid={$info['id']}")->select();
	}

	//取预定界面
	public function yuding($id = null) {
		$this->article_id = $id;
		$this->display('Widget:yuding');
	}
}