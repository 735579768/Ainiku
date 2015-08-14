<?php
// | Author: 枫叶 <735579768@qq.com> <http://www.zhaokeli.com>
// +----------------------------------------------------------------------

namespace Admin\Model;
use Think\Model;
if(!defined("ACCESS_ROOT"))die("Invalid access");
/**
 * 导航模型
 * @author 枫叶 <735579768@qq.com>
 */
class NavModel extends BaseModel{

    protected $_validate = array(
        array('title', 'require', '名称不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
    	array('meta_title', '1,50', '网页标题不能超过50个字符', self::VALUE_VALIDATE , 'length', self::MODEL_BOTH),
    	array('keywords', '1,255', '网页关键字不能超过255个字符', self::VALUE_VALIDATE , 'length', self::MODEL_BOTH),
    	array('meta_descr', '1,255', '网页描述不能超过255个字符', self::VALUE_VALIDATE , 'length', self::MODEL_BOTH),
    );

    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_BOTH),
        array('status', '1', self::MODEL_INSERT)
    );
    /**
     * 获取导航详细信息
     * @param  milit   $id 导航ID或标识
     * @param  boolean $field 查询字段
     * @return array     导航信息
     * @author 枫叶 <735579768@qq.com>
     */
    public function info($id=null){
        /* 获取导航信息 */
         $map['nav_id'] = $id;
        return $this->where($map)->find();
    }

    /**
     * 获取导航树，指定导航则返回指定导航极其子导航，不指定则返回所有导航树
     * @param  integer $id    导航ID
     * @param  boolean $field 查询字段
     * @return array          导航树
     * @author 枫叶 <735579768@qq.com>
     */
    public function getTree($id = 0, $field = true){
        /* 获取当前导航信息 */
        if($id){
            $info = $this->info($id);
            $id   = $info['id'];
        }

        /* 获取所有导航 */
        $map  = array('status' => array('gt', -1));
        $list = $this->field($field)->where($map)->order('sort asc')->select();
		$list = list_to_tree($list, $pk = 'nav_id', $pid = 'pid', $child = '_', $root = $id);
        /* 获取返回数据 */
        if(isset($info)){ //指定导航则返回当前导航极其子导航
            $info['_'] = $list;
        } else { //否则返回所有导航
            $info = $list;
        }

        return $info;
    }

    /**
     * 获取指定导航的同级导航
     * @param  integer $id    导航ID
     * @param  boolean $field 查询字段
     * @return array
     * @author 枫叶 <735579768@qq.com>
     */
    public function getSameLevel($id, $field = true){
        $info = $this->info($id, 'pid');
        $map = array('pid' => $info['pid'], 'status' => 1);
        return $this->field($field)->where($map)->order('sort')->select();
    }

    /**
     * 更新导航信息
     * @return boolean 更新状态
     * @author 枫叶 <735579768@qq.com>
     */
    public function update(){
        $data = $this->create();
        if(!$data){ //数据对象创建错误
            return false;
        }
        /* 添加或更新数据 */
        if(empty($data['nav_id'])){
            $res = $this->add();
        }else{
            $res = $this->save();
			//把导航添加到导航
        }
        //更新导航缓存
        S(md5('sys_nav_tree'), null);
        return $res;
    }

}
