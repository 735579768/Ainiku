<?php
// | Author: 枫叶 <735579768@qq.com> <http://www.zhaokeli.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;
defined("ACCESS_ROOT") || die("Invalid access");
/**
 * 后台分类管理控制器
 * @author 枫叶 <735579768@qq.com>
 */
class CategoryController extends AdminController {

	/**
	 * 分类管理列表
	 * @author 枫叶 <735579768@qq.com>
	 */
	public function index() {
		$tree = D('Category')->getTree(0, true);
		$this->assign('_TREE_', $tree);
		$this->meta_title = '文档分类管理';
		$this->display();
	}

	/**
	 * 显示分类树，仅支持内部调
	 * @param  array $tree 分类树
	 * @author 枫叶 <735579768@qq.com>
	 */
	public function tree($tree = null) {
		$this->assign('_TREE_', $tree);
		$this->display('tree');
	}

	/* 编辑分类 */
	public function edit($category_id = null, $pid = 0) {
		$model = D('Category');
		if (IS_POST) {
			//提交表单
			F('sys_category_' . I('category_type') . '_tree', null);

			if ($model->create()) {
				$result = $model->save();
				if (0 < $result) {
					$this->success(L('_UPDATE_SUCCESS_'), U('index', array('category_type' => I('category_type'))));
				} else {
					$this->success(L('_UPDATE_FAIL_'));
				}
			} else {
				$error = $model->getError();
				$this->error(empty($error) ? L('_UNKNOWN_ERROR_') : $error);
			}

		} else {
			/* 获取分类信息 */
			$data = $category_id ? $model->info($category_id) : '';
			//$field=Api('Model/categoryModel');
			$field = getModelAttr('category');
			$this->assign('fieldarr', $field);
			$this->assign('data', $data);
			$this->meta_title = '编辑分类';
			$this->display();
		}
	}

	/* 新增分类 */
	public function add($pid = 0) {
		$Category = D('Category');
		if (IS_POST) {
			//提交表单
			F('sys_category_' . I('category_type') . '_tree', null);
			if (false !== $Category->update()) {
				$this->success(L('_ADD_SUCCESS_'), U('index', array('category_type' => I('category_type'))));
			} else {
				$error = $Category->getError();
				$this->error(empty($error) ? L('_UNKNOWN_ERROR_') : $error);
			}

		} else {
			//$field=Api('Model/categoryModel');
			$field = getModelAttr('category');

			$data['pid']           = $pid;
			$data['category_type'] = I('category_type');
			$this->assign('fieldarr', $field);
			$this->assign('data', $data);
			$this->meta_title = '新增分类';
			$this->display('edit');
		}
	}

	/**
	 * 删除一个分类
	 * @author huajie <banhuajie@163.com>
	 */
	public function del($category_id = '') {
		$id = I('get.category_id');
		if (empty($id)) {
			$this->error('参数错误!');
		}

		//判断该分类下有没有子分类，有则不允许删除
		$child = M('Category')->where(array('pid' => $id))->field('category_id')->find();
		if (!empty($child)) {
			$this->error('请先删除该分类下的子分类');
		}

		//判断该分类下有没有内容
		$document_list = M('Article')->where(array('category_id' => $category_id))->field('category_id')->select();
		if (!empty($document_list)) {
			$this->error('请先删除该分类下的文章（包含回收站）');
		}

		//删除该分类信息
		$res = M('Category')->delete($category_id);
		if (0 < $res) {
			F('sys_category_' . I('category_type') . '_tree', null);
			//$this->success('删除分类成功！',U('index',array('category_type'=>I('category_type'))));
			$this->success('删除分类成功！');
		} else {
			$this->error('删除分类失败！');
		}
	}
}
