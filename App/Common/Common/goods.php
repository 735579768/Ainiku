<?PHP
defined("ACCESS_ROOT") or die("Invalid access");
/**
 *系统产品函数库
 */
/*
*取产品分类类型
*/
function F_getGoodsType(){
	$rearr=F('sys_goodstype_tree');
	if(empty($rearr) || APP_DEBUG){
		$rows=M('GoodsType')->where('status>0')->order('sort asc')->select();
		$rearr=array();
		foreach($rows as $val){
			$rearr[$val['goods_type_id']]=$val['title'];
			}
		F('sys_goodstype_tree',$rearr);
	}
	return $rearr;
	}
	
//function getTypeModel($goods_type_id){
//	$rows= M('GoodsTypeAttr')->where("goods_type_id=$goods_type_id")->select();
//	foreach($rows as $key=>$val){
//			$rows[$key]['name']=$val['name'].'__'.$val['id'];
//		if(!empty($val['extra'])){
//			$rows[$key]['extra']=A_extratoarray($val['extra']);
//			}
//		}
//	return $rows;
//	}
/*
*取属性所属的类型
*/
function getAtrrType($id='',$field=''){
	$rows=M('GoodsType')->find($id);
	return empty($field)?$rows['title']:$rows[$field];
	}
/**
 *取一个分类的扩展属性(在前台输出搜索筛选时有用)
 */
function getGoodsCatAttr($cat_id=null){
	if(empty($cat_id))return false;
	$rearr=array();
	$row=M('GoodsCat')->field('goods_type_id')->find($cat_id);
	if(!empty($row)){
	$tema=M('GoodsTypeAttr')->where("goods_type_id={$row['goods_type_id']}")->order('sort asc ,id desc')->select();
	 foreach($tema as $val){
		 $extra=$val['extra'];
		 if(!empty($extra))$extra=A_extratoarray($extra);
		 $rearr[]=array(
		 	'name'=>$val['name'],
			'title'=>$val['title'],
			'extra'=>$extra
		 );
		 }
	return $rearr;
	}
	}
/**
 *取当个产品的属性详细信息
 */
 function getGoodsInfo($goodsid=null){
	 if(empty($goodsid))return false;
	 $rows=M('Goods')
	      ->join("kl_goods_cat as b on kl_goods.cat_id=b.id")
		  ->where("kl_goods.id=$goodsid")
		  ->field("*,kl_goods.id as id,kl_goods.title as title,kl_goods.content as content,kl_goods.pic as pic,b.title as cattitle")
		  ->find();
	 if(empty($rows))return false;
	 //查找扩展信息
	 $goods_type_id=$rows['goods_type_id'];
	 $tema=M('GoodsType')->where("goods_type_id=$goods_type_id")->select();
	 foreach($tema as $key=>$val){
		 $map['goodsid']=$goodsid;
		 $map['attrid']=$tema['id'];
		 $temb=M('GoodsAttrvalue')->where($map)->find();
		 $rows[$temb['name']]=$temb['value'];
		 }
	return $rows;
	 }
	//保存产品post过来的附加信息
function updateGoodsinfo($Goodsid=null){
			$msg=array(
				'info'=>'操作成功',
				'status'=>1
			);
			
			//附加用户信息转为数组
					//取post值
		$data=I('post.');
		$fields=array();
		//取带_的键值说明是附加属性
		foreach ($data as $key => $value) {
			$pos=strpos($key,'__');
			if($pos!==false){
			$fields[$key]=$value;
			//$tem=explode('__', $key);
			//$data2[$tem[0]]=$value;
			}
		}
			//$model=M('GoodsAttrvalue');
			//更新字段状态
			$fieldbool=true;
			foreach($fields as $key=>$val){
				//查找有没有这个表单值
				$tem=explode('__', $key);
				$data2[]=$value;
				
				$map['goodsid']=$Goodsid;
				$map['name']=$tem[0];
				$map['goods_type_id']=$tem[1];
				$temrow=M('GoodsAttrvalue')->where($map)->find();
				
				if(empty($temrow)){
					//添加
					$map['value']=$val;

					$fieldbool=M('GoodsAttrvalue')->add($map);
					
					if($fieldbool===false){
						$msg['info']='添加字段时出错';
						$msg['status']=0;
						}
					}else{
					//更新
					$mapp['Goods_id']=$Goods_id;
					$mapp['goods_type_id']=$key;
					$fieldbool=M('GoodsAttrvalue')->where($mapp)->save(array('value'=>$val));	
					if($fieldbool===false){
						$msg['info']='更新字段时出错';
						$msg['status']=0;	
						}
						}
				
				}
		return $msg;		
		}
?>