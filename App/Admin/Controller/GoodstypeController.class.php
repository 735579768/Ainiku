<?php
// | Author: 枫叶 <735579768@qq.com> <http://www.zhaokeli.com>
// +----------------------------------------------------------------------
namespace Admin\Controller;
use \Admin\Controller\AdminController;
if(!defined("ACCESS_ROOT"))die("Invalid access");
class GoodstypeController extends AdminController {
 	public function index(){
		$this->pages(array(
			'model'=>'GoodsType'
		));
		$this->meta_title='类型列表';
		$this->display();
		}
    /* 编辑分类 */
    public function edit($goods_type_id = null, $pid = 0){
        $GoodsType = D('GoodsType');

        if(IS_POST){ //提交表单
			F('sys_GoodsType_tree',null);
            if(false !== $GoodsType->create()){
				$GoodsType->save();
                $this->success('更新成功！');
            } else {
                $error = $GoodsType->getError();
                $this->error(empty($error) ? '未知错误！' : $error);
            }
			
        } else {
            /* 获取分类信息 */
            $data = $goods_type_id ? $GoodsType->info($goods_type_id) : '';
			//$field=Api('Model/GoodsTypeModel');
			$field=getmodel('goodstype');
			$this->assign('fieldarr',$field);
			$this->assign('data',$data);
            $this->meta_title = '类型>'.getField('goodsType',$goods_type_id,'title').'>编辑类型';
            $this->display();
        }
    }

    /* 新增分类 */
    public function add($pid = 0){
        $GoodsType = D('GoodsType');

        if(IS_POST){ //提交表单
			F('sys_GoodsType_tree',null);
            if(false !== $GoodsType->create()){
				$GoodsType->add();
                $this->success('新增成功！', U('index'));
            } else {
                $error = $GoodsType->getError();
                $this->error(empty($error) ? '未知错误！' : $error);
            }
			
        } else {
			//$field=Api('Model/GoodsTypeModel');
			$field=getmodel('goodstype');
			$this->assign('fieldarr',$field);
			$data=array('pid'=>$pid);
			$this->assign('data',$data);
            $this->meta_title = '新增产品类型';
            $this->display('edit');
        }
    }
	public function del($goods_type_id=''){
		if(empty($goods_type_id))$this->error('id不能为空');
		if($goods_type_id==='4')$this->error('系统默认类型不能删除');
		//查找有没有分类使用这个类型
		$rows=M('GoodsCat')->where("goods_type_id=$goods_type_id")->select();
		if(!empty($rows))$this->error('有商品分类使用此类型不能删除');
		//查找类型下面有没有属性
		$rows=M('GoodsTypeAttr')->where("goods_type_id=$goods_type_id")->select();
		if(!empty($rows))$this->error('请删除此类型下的属性再操作');
		$result=M('GoodsType')->delete($goods_type_id);
		if($result){
			$this->success('删除成功');
			}else{
			$this->error('删除失败');	
			}
		}
 
}
