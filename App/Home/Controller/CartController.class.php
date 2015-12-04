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
		//$jon='__DB_PREFIX__goods as a   on  __DB_PREFIX__cart.goods_id=a.goods_id';
       // $map['uid']=UID;
		$list = $this->pages(array(
							//'join'=>$jon,
							'rows'=>10,
							'model'=>'CartView',
							'where'=> $map
							));
        $this->meta_title = '购物车';
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
		$num=intval(I('num'));
		$cart_id=intval(I('cart_id'));
		empty($cart_id)&&$this->error('no');
		empty($num)&&$this->error('no');
		//$map['uid']=UID;
		$map['cart_id']=$cart_id;
		$resu=M('Cart')->where($map)->save(array('num'=>$num,'update_time'=>NOW_TIME));
		if($resu>0){
			$this->success('更新成功');
			}else{
			$this->error('没有更改');
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
				//购物车中有这个产品
				$result=$model->save(array(
						'cart_id'=>$resu['cart_id'],
						'num'=>$resu['num']+$num
						));
				if(0<$result){
					$this->success('添加成功');
				}else{
					$this->error('添加失败');
					}
			}
		}else{
			redirect('/');
			}

		die();
		}
    function del(){
		$cart_id=intval(I('cart_id'));
		if(empty($cart_id))$this->error('请先进行选择');
		$model=M('Cart');
		$map['cart_id']=array('in',"$cart_id");
		//$map['uid']=UID;
    	$result=$model->where($map)->delete();
    	if($result>0){
    	  $this->success('已经从购物车删除');
    	}else{
    	  $this->error('操作失败');
    	}
    }
}
