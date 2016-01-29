<?php
namespace Admin\Controller;
defined("ACCESS_ROOT") || die("Invalid access");
class ArticleController extends AdminController {
	/**
	 * 配置管理
	 * @author 枫叶 <735579768@qq.com>
	 */
	public function index() {
		$field          = getModelAttr('article', 'category_id');
		$field['title'] = '分类';
		$this->assign('fieldarr', $field);

		//附加属性
		//$field1=Api('Model/articleModel');
		$field1             = getModelAttr('article', 'position');
		$field1['type']     = 'select';
		$field1['title']    = '位置';
		$field1['extra'][0] = '全部';
		$field1['value']    = I('position');
		$this->assign('fieldarr1', $field1);

		$this->assign('data', null);
		/* 查询条件初始化 */
		$map                  = array();
		$map['status']        = 1;
		$map['category_type'] = 'article';
		$title                = I('title');
		$category_id          = I('category_id');
		$position             = I('position');
		if ($position !== '0' && $position !== '') {
			$map['position'] = array('like', '%' . $position . '%');
		}

		if (!empty($title)) {
			$map['title'] = array('like', '%' . $title . '%');
		}

		if (!empty($category_id)) {
			$allid              = getCategoryAllChild($category_id);
			$map['category_id'] = array('in', "$allid");
		}

		$field = 'article_id,title,pic,position,category_id,status,update_time,create_time';
		$list  = $this->pages(array(
			'field' => $field,
			'order' => 'article_id desc',
			'model' => 'article',
			'where' => $map,
		));
		$this->meta_title = '文章列表';
		$this->display();
	}
	function recycle() {
		$title         = I('title');
		$map['title']  = array('like', '%' . $title . '%');
		$map['status'] = -1;
		$this->pages(array(
			'model' => 'Article',
			'where' => $map,
		));
		$this->meta_title = '文档回收站';
		$this->display();
	}
	function draftbox() {
		$title         = I('title');
		$map['title']  = array('like', '%' . $title . '%');
		$map['status'] = 2;
		$this->pages(array(
			'model' => 'Article',
			'where' => $map,
		));
		$this->meta_title = '草稿箱';
		$this->display();
	}
	function add() {
		if (IS_POST) {
			$model = D('article');
			if ($model->create()) {
				//	$model->position=implode(',',I('position'));
				$result = 0;
				$status = I('status');
				$idd    = I('article_id');
				//判断id是不是为空
				if (!empty($idd)) {$this->edit($idd);die();}
				//去保存草稿
				if ($status == '2') {$this->savedraftbox();}
				if (!$model->pic) {
					$model->pic = $this->getFirstPicture($model->content);
				}
				$result = $model->add();
				if (0 < $result) {
					$this->success('添加文章成功', U('index', array('category_id' => I('category_id'))));
				} else {
					$this->error('添加文章失败');
				}
			} else {
				$this->error($model->getError());
			}
		} else {
			//$field=Api('Model/articleModel');
			$field            = getModelAttr('article');
			$this->meta_title = '添加文章';
			$this->assign('fieldarr', $field);
			$this->assign('data', $data);
			$this->display('edit');
		}
	}
	public function savedraftbox() {
		$model  = D('article');
		$result = 0;
		$status = I('status');
		$idd    = I('article_id');
		if ($model->create()) {
			//$model->position=implode(',',I('position'));
			if ($status == '2' && !empty($idd)) {
				$result = $model->save();
			} else {
				$result = $model->add();
			}
			// if(0<$result){
			$this->ajaxreturn(array(
				'info'       => '草稿保存成功',
				'status'     => 1,
				'article_id' => $idd ? $idd : $result,
				'url'        => '',
			));

			//	}
			exit();
		}
	}
	/**
	 *提取字符串中的第一个图片
	 */
	private function getFirstPicture($str = '') {
		preg_match('/<img.*?src\=[\'|\"](.*?)[\'|\"].*?>/', $str, $match);
		if ($match) {
			$info = M('Picture')->field('id')->where("path='{$match[1]}'")->find();
			return empty($info) ? 0 : $info['id'];
		} else {
			return 0;
		}
	}
	/**
	 * 编辑文章
	 * @author 枫叶 <735579768@qq.com>
	 */
	public function edit($article_id = 0) {
		if (IS_POST) {
			$status = I('status');
			//去保存草稿
			if ($status == '2') {$this->savedraftbox();}
			$model = D('Article');
			if ($model->create()) {
				//$model->position=implode(',',I('position'));
				if (!$model->pic) {
					$model->pic = $this->getFirstPicture($model->content);
				}
				if ($model->save()) {
					$this->success(L('_UPDATE_SUCCESS_'), __PAGEURL__);
				} else {
					$this->error(L('_UPDATE_FAIL_'));
				}
			} else {
				$this->error($model->getError());
			}
		} else {
			$info = array();
			/* 获取数据 */
			$info = M('Article')->field(true)->find($article_id);
			if (false === $info) {
				$this->error('获取文章信息错误');
			}
			//$field=Api('Model/articleModel');
			$field = getModelAttr('article');
			$this->assign('data', $info);
			$this->assign('fieldarr', $field);
			$this->meta_title = '编辑文章';
			$this->display();
		}
	}

	//移动文档
	public function move() {
		if (IS_POST) {
			$catid = I('category_id');
			if (!empty($catid)) {
				$id                = I('id');
				$map               = array();
				$map['article_id'] = array('in', $id);
				$result            = M('article')->where($map)->save(array('category_id' => $catid));

//				$sql="update __DB_PREFIX__.'Article set category_id=$catid where id in($id)";
				//				$result=M('article')->query($sql);
				if (0 < $result) {
					$this->success('文档移动成功', U('index', array('category_id' => $catid)));
				} else {
					$this->error('文档移动失败');
				}
			}
			//分类树
			$catelist = F('sys_category_tree');
			if (empty($catelist)) {
				$catelist = A_getCatelist();
				F('sys_category_tree', $catelist);
			}
			unset($catelist[0]);
			$field = array(
				array(
					'field'   => 'category_id',
					'name'    => 'category_id',
					'type'    => 'select',
					'title'   => '所属分类',
					'note'    => '',
					'extra'   => $catelist,
					'is_show' => 3,
				),
			);
			$this->assign('fieldarr', $field);
			$this->assign('data', null);
			$this->meta_title = '移动文档';
			$this->display();
		} else {
			redirect(U('/'));
		}

	}
	//放到回收站
	function del() {
		//  $article_id=$id;//I('get.article_id');
		$article_id = isset($_REQUEST['article_id']) ? I('get.article_id') : I("id");
		if (empty($article_id)) {
			$this->error('请先进行选择');
		}

		$result = M('Article')->where("article_id in($article_id)")->save(array('status' => -1));
		if (0 < $result) {
			$this->success(L('_TO_RECYCLE_'), U('recycle'));
		} else {
			$this->error(L('_CAOZUO_FAIL_'));
		}
	}
	function dele() {
		$article_id = isset($_REQUEST['article_id']) ? I('get.article_id') : I("id"); //I('get.article_id');
		if (empty($article_id)) {
			$this->error('请先进行选择');
		}

		$model  = M('Article');
		$result = $model->where("article_id in ($article_id)")->delete();
		if (result) {
			$this->success(L('_CHEDI_DELETE_'), U('recycle'));
		} else {
			$this->error(L('_CAOZUO_FAIL_'));
		}
	}
	function huifu() {
		//$article_id=$id;//I('get.article_id');
		$article_id = isset($_REQUEST['article_id']) ? I('get.article_id') : I("id");
		if (empty($article_id)) {
			$this->error('请先进行选择');
		}

		$uid = M('Article')->where("article_id in($article_id)")->save(array('status' => 1));
		if (0 < $uid) {
			$this->success(L('_TO_HUIFU_'), U('index'));
		} else {
			$this->error(L('_CAOZUO_FAIL_'));
		}
	}
	function delall() {
		$result = M('Article')->where("status=-1")->delete();
		if (result) {
			$this->success(L('_CLEAR_NULL_'), U('recycle'));
		} else {
			$this->error(L('_CAOZUO_FAIL_'));
		}
	}
}