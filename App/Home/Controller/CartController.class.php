<?php
namespace Home\Controller;
use Think\Controller;
if(!defined("ACCESS_ROOT"))die("Invalid access");
class CartController extends LoginController {
    /**
     * 购物车列表
     * @author 枫叶 <735579768@qq.com>
     */
    public function index(){
		$jon='__DB_PREFIX__goods as a   on  __DB_PREFIX__cart.goods_id=a.goods_id';
        $list = $this->pages(array(
							'join'=>$jon,
							'rows'=>5,
							'model'=>'Cart',
							'where'=> "uid=".UID
							));
        $this->meta_title = '购物车';
		//加载取货地址
		$adlist=M('Member')->where("member_group_id=2 and address<>''")->select();
		$this->assign('adlist',$adlist);
        $this->display();
    }
	/**
	 *取当前用户购物车中的产品数量
	 */
	function getnum(){
		$resu=M('Cart')->where("uid=".UID)->count();
		die($resu);
		}
	/**
	 *更新当前用户产品的数量
	 */
	function updatenum(){
		$resu=M('Cart')->where("uid=".UID.'  and cart_id='.I('cart_id'))
					   ->save(array(
							'num'=>I('num'),											
									));
		if($resu>0){
			$this->success('ok');
			}else{
			$this->error('no');	
				}
		}
	/**
	 *添加产品到购物车
	 **/
	function add($goods_id='',$num=0){
        if(IS_POST){
			$model = D('Cart');
			$data['uid']=UID;
			$data['create_time']=NOW_TIME;
			$data['update_time']=NOW_TIME;
			$data['goods_id']=$goods_id;
			$data['num']=$num;
			//查询下如果购物车里有的话就数量添加一个
			$resu=$model->where('uid='.UID.'  and goods_id='.$goods_id)->find();
			if(empty($resu)){
				//购物车中没有这个产品
				if($model->create($data)){
					$result=$model->add();
					if(0<$result){
						$this->success('添加产品到购物车成功');
					} else {
						$this->error('添加产品到购物车失败');
					}
				} else {
					//未知错误
					$this->error($model->getError());
				}		
			}else{
				$result=$model->save(array('cart_id'=>$resu['cart_id'],'num'=>$resu['num']+intval($id[1])));
				if(0<$result){
						//$this->success('添加成功');
					}else{
						$this->error('添加失败');
						}
				}
		$this->success('成功添加到购物车');
		}else{
			$this->error('请选择产品');
			}
		
		die();
		}
    function del(){
    	$cart_id=isset($_REQUEST['cart_id'])?I('get.cart_id'):I("id");//I('get.cart_id');
		if(empty($cart_id))$this->error('请先进行选择');
		$model=M('Cart');
    	$result=$model->where("cart_id in ($cart_id)")->delete();
    	if(result){
    	  $this->success('已经从购物车删除');
    	}else{
    	  $this->error('操作失败');
    	}
    }
	function delall(){
		$result=M('Cart')->where("status=-1")->delete();
    	if(result){
    	  $this->success('回收站已经清空',U('recycle'));
    	}else{
    	  $this->error('操作失败');
    	}		
		}
}