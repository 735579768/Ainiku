<?php
// | Author: 枫叶 <735579768@qq.com> <http://www.zhaokeli.com>
// +----------------------------------------------------------------------
namespace Admin\Controller;
use \Admin\Controller\AdminController;
defined("ACCESS_ROOT") || die("Invalid access");
class GoodstypeattributeController extends AdminController {

 	public function index($goods_type_id=''){
		$map=array();
		if(!empty($goods_type_id))$map['goods_type_id']=$goods_type_id;
		$this->pages(array(
			'where'=>$map,
			'model'=>'GoodsTypeAttribute'
		));
		$typename=getField('goodsType',I('goods_type_id'),'title');
		$this->assign('typename',$typename);
		$this->meta_title='类型>'.$typename.'>属性列表';
		$this->display();
		}
	/**生成属性表单**/
	function formlist($goods_type_id=null,$mainid=null){
		if($goods_type_id==0)return '';
		$list=getGoodsTypeModel($goods_type_id);
		//查询属性数据
		$map['goods_id']=$mainid;
		//$map['goods_type_id']=$goods_type_id;
		$attlist=M('GoodsAttribute')->where($map)->select();
		$data=array();
		foreach($attlist as $key=>$val){
			$data[$val['name'].'____'.$val['goods_type_attribute_id']]=$val['value'];
			}
		$this->assign('data',$data);
		$this->assign('fieldarr',$list);
		$this->display();
		}
    /* 编辑分类 */
    public function edit($goods_type_attribute_id = null, $pid = 0){
        $GoodsTypeAttribute = D('GoodsTypeAttribute');

        if(IS_POST){ //提交表单
			F('sys_GoodsTypeAttribute_tree',null);
            if(false !== $GoodsTypeAttribute->create()){
				$GoodsTypeAttribute->save();
                $this->success(L('_UPDATE_SUCCESS_'),__FORWARD__);
            } else {
                $error = $GoodsTypeAttribute->getError();
                $this->error(empty($error) ? L('_UNKNOWN_ERROR_') : $error);
            }
			
        } else {
            /* 获取分类信息 */
            $data = $goods_type_attribute_id ? $GoodsTypeAttribute->info($goods_type_attribute_id) : '';
			//$field=Api('Model/goodstypeattrModel');
			$field=getModelAttr('goodstypeattr');
			$this->assign('fieldarr',$field);
			$this->assign('data',$data);
            $this->meta_title = '类型>'.getField('goodsType',$data['goods_type_id'],'title').'>编辑属性>'.getField('goodsTypeAttribute',$goods_type_attribute_id,'title');
            $this->display();
        }
    }

    /* 新增分类 */
    public function add($goods_type_id = 0){
        $GoodsTypeAttribute = D('GoodsTypeAttribute');

        if(IS_POST){ //提交表单
			F('sys_GoodsTypeAttribute_tree',null);
            if(false !== $GoodsTypeAttribute->create()){
				$GoodsTypeAttribute->add();
                $this->success(L('_ADD_SUCCESS_'), U('index'));
            } else {
                $error = $GoodsTypeAttribute->getError();
                $this->error(empty($error) ? L('_UNKNOWN_ERROR_') : $error);
            }
			
        } else {
			//$field=Api('Model/goodstypeattrModel');
			$field=getModelAttr('goodstypeattr');
			$this->assign('fieldarr',$field);
			$data=array('goods_type_id'=>$goods_type_id);
			$this->assign('data',$data);
            $this->meta_title = '类型>'.getField('goodsType',$data['goods_type_id'],'title').'>添加属性';
            $this->display('edit');
        }
    }
	public function del($goods_type_attribute_id=null){
		if(empty($goods_type_attribute_id))return false;
		//查找类型下面有没有属性
		$rows=M('GoodsAttribute')->where("goods_type_attribute_id=$goods_type_attribute_id")->select();
		if(!empty($rows))$this->error('有商品使用此属性不能删除');
		$goods_type_id=getField('GoodsTypeAttribute',$goods_type_attribute_id,'goods_type_id');
		$result=M('GoodsTypeAttribute')->delete($goods_type_attribute_id);
		if($result){
			$this->success('删除成功',U('index',array('goods_type_id'=>$goods_type_id)));
			}else{
			$this->error(L('_DELETE_FAIL_'));	
			}
		$this->display();
		}
}
