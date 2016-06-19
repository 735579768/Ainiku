<?php
namespace Admin\Controller;
use \Admin\Controller\AdminController;

defined("ACCESS_ROOT") || die("Invalid access");
class GoodsController extends AdminController {
	/**
	 * 配置管理
	 * @author 枫叶 <735579768@qq.com>
	 */
	public function index() {
		$field          = get_model_attr('Goods', 'category_id');
		$field['title'] = '分类';
		$this->assign('fieldarr', $field);

		//附加属性
		//$field1=Api('Model/GoodsModel');
		$field1             = get_model_attr('Goods', 'position');
		$field1['type']     = 'select';
		$field1['title']    = '位置';
		$field1['extra'][0] = '全部';
		$field1['value']    = I('position');
		$this->assign('fieldarr1', $field1);

		$this->assign('data', null);
		/* 查询条件初始化 */
		$map           = array();
		$map['status'] = array('egt', 0);
		$title         = I('title');
		$category_id   = I('category_id');
		$position      = I('position');
		if ($position !== '0' && $position !== '') {
			$map['position'] = array('like', '%' . $position . '%');
		}

		if (!empty($title)) {
			$map['title'] = array('like', '%' . $title . '%');
		}

		if (!empty($category_id)) {
			$map['category_id'] = $category_id;
		}

		$list = $this->pages(array(
			'model' => 'Goods',
			'where' => $map,
		));
		$this->meta_title = '产品列表';
		$this->display();
	}
	function recycle() {
		$title         = I('title');
		$map['title']  = array('like', '%' . $title . '%');
		$map['status'] = -1;
		$this->pages(array(
			'model' => 'Goods',
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
			'model' => 'Goods',
			'where' => $map,
		));
		$this->meta_title = '草稿箱';
		$this->display();
	}
	function add() {
		if (IS_POST) {
			$model = D('Goods');
			if ($model->create()) {
				//	$model->position=implode(',',I('position'));
				$result = 0;
				$status = I('status');
				$idd    = I('id');
				//判断id是不是为空
				if (!empty($idd)) {$this->edit($idd);die();}
				//去保存草稿
				if ($status == '2') {$this->savedraftbox();}
				if (!$model->pic) {
					$model->pic = $this->getFirstPicture($model->content);
				}
				$result = $model->add();
				//保存产品附加属性表中的信息
				$msg = $this->updateGoodsInfo($result);
				if ($msg['status'] === 0) {
					$this->error($msg['info']);
				} else {
					$this->success($msg['info'], U('index', array('category_id' => I('category_id'))));
				}
			} else {
				$this->error($model->getError());
			}
		} else {
			//$field=Api('Model/GoodsModel');
			$field            = get_model_attr('Goods');
			$this->meta_title = '添加产品';
			$this->assign('fieldarr', $field);
			$this->assign('data', $data);
			$this->display('edit');
		}
	}
	public function savedraftbox() {
		$model  = D('Goods');
		$result = 0;
		$status = I('status');
		$idd    = I('goods_id');
		if ($model->create()) {
			//$model->position=implode(',',I('position'));
			if ($status == '2' && !empty($idd)) {
				$result = $model->save();
			} else {
				$result = $model->add();
			}
			// if(0<$result){
			$this->ajaxreturn(array(
				'info'     => '草稿保存成功',
				'status'   => 1,
				'goods_id' => $idd ? $idd : $result,
				'url'      => '',
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
	 * 编辑产品
	 * @author 枫叶 <735579768@qq.com>
	 */
	public function edit($goods_id = 0) {
		if (IS_POST) {
			$status = I('status');
			//去保存草稿
			if ($status == '2') {$this->savedraftbox();}
			$model = D('Goods');
			if ($model->create()) {
				//$model->position=implode(',',I('position'));
				//取原来的产品类型
				$srcid = get_goods($goods_id);
				$srcid = $srcid['goods_type_id'];
				if (!$model->pic) {
					$model->pic = $this->getFirstPicture($model->content);
				}
				$result = $model->save();
				if (0 < $result) {
					//判断产品类型是否变化啦如果是的话就删除原有信息
					if (I('goods_type_id') != $srcid) {M('GoodsAttribute')->where('goods_id=' . $goods_id)->delete();}
					//保存产品附加属性表中的信息
					$msg = $this->updateGoodsInfo($goods_id);
					if ($msg['status'] === 0) {
						$this->error($msg['info']);
					} else {
						$this->success($msg['info'], U('index', array('category_id' => I('category_id'))));
					}
				} else {
					$this->error($model->geterror());
				}
			} else {
				$this->error($model->getError());
			}
		} else {
			$info = array();
			/* 获取数据 */
			$info = M('Goods')->field(true)->find($goods_id);
			if (false === $info) {
				$this->error('获取产品信息错误');
			}
			//$field=Api('Model/GoodsModel');
			$field = get_model_attr('Goods');
			$this->assign('data', $info);
			$this->assign('fieldarr', $field);
			$this->meta_title = '编辑产品';
			$this->display();
		}
	}
	/**
	 *更新产品的属性信息
	 */
	function updateGoodsInfo($goods_id = null) {
		$msg = array(
			'info'   => L('_CAOZUO_SUCCESS_'),
			'status' => 1,
		);

		$model = D('GoodsAttribute');
		//附加用户信息转为数组
		$data   = I('post.');
		$fields = array();
		//取带_的键值说明是附加属性
		foreach ($data as $key => $value) {
			$pos = strpos($key, '____');
			if ($pos !== false) {
				$fields[$key] = $value;
			}
		}
		//更新字段状态
		//$fieldbool=true;
		foreach ($fields as $key => $val) {
			//查找有没有这个表单值
			$tem = explode('____', $key);
			//$data2[]=$value;
			$map                            = array();
			$map['goods_id']                = $goods_id;
			$map['name']                    = $tem[0];
			$map['goods_type_attribute_id'] = $tem[1];
			$map['update_time']             = NOW_TIME;
			$map['value']                   = $val;

			$temrow = $model->where($map)->find();

			if (empty($temrow)) {
				//添加
				$result = $model->add($map);
				if (0 < $result) {
					$msg['info']   = L('_UPDATE_SUCCESS_');
					$msg['status'] = 1;
				} else {
					$msg['info']   = '添加产品附加属性字段时出错';
					$msg['status'] = 0;
				}
			} else {
				//更新
				$whe['goods_attribute_id'] = $temrow['goods_attribute_id'];
				$result                    = $model->where($whe)->save($map);
				if (0 < $result) {
					$msg['info'] = L('_UPDATE_SUCCESS_');
				} else {
					$msg['info']   = '更新产品附加属性时出错';
					$msg['status'] = 0;
				}
			}
		}
		return $msg;
	}

	//移动文档
	public function move() {
		if (IS_POST) {
			$catid = I('category_id');
			if (!empty($catid)) {
				$id              = I('id');
				$map             = array();
				$map['goods_id'] = array('in', $id);
				$result          = M('Goods')->where($map)->save(array('category_id' => $catid));

//				$sql="update __DB_PREFIX__.'Goods set category_id=$catid where id in($id)";
				//				$result=M('Goods')->query($sql);
				if (0 < $result) {
					$this->success('产品移动成功', U('index'));
				} else {
					$this->error('产品移动失败');
				}
			}
			//分类树
			$catelist = F('sys_category_tree');
			if (empty($catelist)) {
				$catelist = get_cate_list();
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
		//  $goods_id=$id;//I('get.goods_id');
		$goods_id = isset($_REQUEST['goods_id']) ? I('get.goods_id') : I("id");
		if (empty($goods_id)) {
			$this->error('请先进行选择');
		}

		$result = M('Goods')->where("goods_id in($goods_id)")->save(array('status' => -1));
		if (0 < $result) {
			$this->success(L('_TO_RECYCLE_'), U('recycle'));
		} else {
			$this->error(L('_CAOZUO_FAIL_'));
		}
	}
	function dele() {
		$goods_id = isset($_REQUEST['goods_id']) ? I('get.goods_id') : I("id"); //I('get.goods_id');
		if (empty($goods_id)) {
			$this->error('请先进行选择');
		}

		$model  = M('Goods');
		$result = $model->where("goods_id in ($goods_id)")->delete();
		if (result) {
			M('GoodsAttribute')->where('goods_id=' . $goods_id)->delete();
			$this->success(L('_CHEDI_DELETE_'), U('recycle'));
		} else {
			$this->error(L('_CAOZUO_FAIL_'));
		}
	}
	function huifu() {
		//$goods_id=$id;//I('get.goods_id');
		$goods_id = isset($_REQUEST['goods_id']) ? I('get.goods_id') : I("id");
		if (empty($goods_id)) {
			$this->error('请先进行选择');
		}

		$uid = M('Goods')->where("goods_id in($goods_id)")->save(array('status' => 1));
		if (0 < $uid) {
			$this->success(L('_TO_HUIFU_'), U('index'));
		} else {
			$this->error(L('_CAOZUO_FAIL_'));
		}
	}
	function delall() {
		$list = M('Goods')->where('status=-1')->select();
		foreach ($list as $val) {
			M('GoodsAttribute')->where('goods_id=' . $val['goods_id'])->delete();
		}
		$result = M('Goods')->where("status=-1")->delete();
		if (result) {
			$this->success(L('_CLEAR_NULL_'), U('recycle'));
		} else {
			$this->error(L('_CAOZUO_FAIL_'));
		}
	}
}