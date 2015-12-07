<?php
// | Author: 枫叶 <735579768@qq.com> <http://www.zhaokeli.com>
// +----------------------------------------------------------------------

namespace Admin\Model;
use Think\Model;
defined("ACCESS_ROOT") || die("Invalid access");
/**
 * 分类模型
 * @author 枫叶 <735579768@qq.com>
 */
class CategoryModel extends BaseModel{

    protected $_validate = array(
	
	    array('title', 'require', '名称不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
		array('title', '', '分类名称已经存在', self::VALUE_VALIDATE, 'unique', self::MODEL_BOTH),
        
		array('category_type', 'require', '分类类型标识不能为空', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
      
        array('name', '', '标识已经存在', self::VALUE_VALIDATE, 'unique', self::MODEL_BOTH),
		

		
    	array('meta_title', '1,50', '网页标题不能超过50个字符', self::VALUE_VALIDATE , 'length', self::MODEL_BOTH),
    	array('keywords', '1,255', '网页关键字不能超过255个字符', self::VALUE_VALIDATE , 'length', self::MODEL_BOTH),
    	array('meta_descr', '1,255', '网页描述不能超过255个字符', self::VALUE_VALIDATE , 'length', self::MODEL_BOTH),
    );

    protected $_auto = array(
      //  array('model', 'arr2str', self::MODEL_BOTH, 'function'),
	    array('name', 'getname',  self::MODEL_BOTH, 'callback', self::MODEL_BOTH),
		 array('reg_ip', 'get_client_ip', self::MODEL_INSERT, 'function', 1),
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_BOTH),
        array('status', '1', self::MODEL_INSERT),
    );
	function getname($name=null){
		if(empty($name)){
			$py=Pinyin(I('title'));
			if(strlen($py)>8)$py=substr($py,0,8);
			return $py;
			}else{
			return $name;	
				}
		}
    /**
     * 获取分类详细信息
     * @param  milit   $id 分类ID或标识
     * @param  boolean $field 查询字段
     * @return array     分类信息
     * @author 枫叶 <735579768@qq.com>
     */
    public function info($category_id, $field = true){
        /* 获取分类信息 */
        $map = array();
        if(is_numeric($category_id)){ //通过ID查询
            $map['category_id'] = $category_id;
        } else { //通过标识查询
            $map['name'] = $category_id;
        }
        return $this->field($field)->where($map)->find();
    }

    /**
     * 获取分类树，指定分类则返回指定分类极其子分类，不指定则返回所有分类树
     * @param  integer $id    分类ID
     * @param  boolean $field 查询字段
     * @return array          分类树
     * @author 枫叶 <735579768@qq.com>
     */
    public function getTree($id = 0, $field = true){
        /* 获取当前分类信息 */
        if($id){
            $info = $this->info($id);
            $id   = $info['id'];
        }

        /* 获取所有分类 */
        $map  = array('status' => array('gt', -1));
		$category_type=I('category_type');
		if(!empty($category_type))$map['category_type']=$category_type;
        $list = $this->field($field)->where($map)->order('sort asc,category_id asc')->select();
		$list = list_to_tree($list, $pk = 'category_id', $pid = 'pid', $child = '_', $root = $id);
        /* 获取返回数据 */
        if(isset($info)){ //指定分类则返回当前分类极其子分类
            $info['_'] = $list;
        } else { //否则返回所有分类
            $info = $list;
        }

        return $info;
    }

//    /**
//     * 获取指定分类的同级分类
//     * @param  integer $id    分类ID
//     * @param  boolean $field 查询字段
//     * @return array
//     * @author 枫叶 <735579768@qq.com>
//     */
//    public function getSameLevel($id, $field = true){
//        $info = $this->info($id, 'pid');
//        $map = array('pid' => $info['pid'], 'status' => 1);
//        return $this->field($field)->where($map)->order('sort')->select();
//    }

    /**
     * 更新分类信息
     * @return boolean 更新状态
     * @author 枫叶 <735579768@qq.com>
     */
    public function update(){
        $data = $this->create();
        if(!$data){ //数据对象创建错误
            return false;
        }
        /* 添加或更新数据 */
        if(empty($data['category_id'])){
            $res = $this->add();
        }else{
            $res = $this->save();
//			//把分类添加到导航
//			$navshow=$data['nav_show'];
//			if($navshow==='1'){
//				//查找导航中有没有
//				$a=M('channel')->where("url like '%cat/{$data['name']}%'")->find();
//				if(empty($a)){
//					M('channel')->add(array(
//									'title'=>$data['title'],
//									'url'=>'cat/'.$data['name'],
//									'status'=>1,
//									'create_time'=>time(),
//									'sort'=>10,
//									'type'=>'cate',
//									'target'=>0
//									));
//					}	
//			}else{
//				M('channel')->where("url like '%cat/{$data['name']}%'")->delete();
//					}
       }

        //更新分类缓存
        S('sys_category_list', null);

        return $res;
    }

}
