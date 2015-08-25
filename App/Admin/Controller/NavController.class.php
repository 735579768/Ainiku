<?php
// | Author: 枫叶 <735579768@qq.com> <http://www.zhaokeli.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;
if(!defined("ACCESS_ROOT"))die("Invalid access");
/**
 * 后台导航管理控制器
 * @author 枫叶 <735579768@qq.com>
 */
class NavController extends AdminController {
 public function __construct(){
		 parent::__construct();
		 $this->model_name='Nav';
		 //$this->primarykey='id';
		 } 
    /**
     * 导航管理列表
     * @author 枫叶 <735579768@qq.com>
     */
    public function index(){
		$tree = D('Nav')->getTree(0,true);
        $this->assign('_TREE_', $tree);
        $this->meta_title = '导航管理';
        $this->display();
    }

    /**
     * 显示导航树，仅支持内部调
     * @param  array $tree 导航树
     * @author 枫叶 <735579768@qq.com>
     */
    public function tree($tree = null){
        $this->assign('_TREE_', $tree);
        $this->display('tree');
    }

    /* 编辑导航 */
    public function edit($nav_id = null, $pid = 0){
        $Nav = D('Nav');

        if(IS_POST){ //提交表单
			F('sys_nav_tree',null);
			F('sys_navhome_list',null);
            if(false !== $Nav->update()){
                $this->success('更新成功！',U('index'));
            } else {
                $error = $Nav->getError();
                $this->error(empty($error) ? '未知错误！' : $error);
            }
			
        } else {
            /* 获取导航信息 */
            $data = $nav_id ? $Nav->info($nav_id) : '';
			//$field=Api('Model/NavModel');
			$field=getModel('nav');
			$this->assign('fieldarr',$field);
			$this->assign('data',$data);
            $this->meta_title = '编辑导航';
            $this->display();
        }
    }

    /* 新增导航 */
    public function add($pid = 0){
        $Nav = D('Nav');

        if(IS_POST){ //提交表单
			F('sys_nav_tree',null);
			F('sys_navhome_list',null);
            if(false !== $Nav->update()){
                $this->success('新增成功！', U('index'));
            } else {
                $error = $Nav->getError();
                $this->error(empty($error) ? '未知错误！' : $error);
            }
			
        } else {
			//$field=Api('Model/NavModel');
			$field=getModel('nav');
			$this->assign('fieldarr',$field);
			$data=array('pid'=>$pid);
			$this->assign('data',$data);
            $this->meta_title = '新增导航';
            $this->display('edit');
        }
    }

    /**
     * 删除一个导航
     * @author huajie <banhuajie@163.com>
     */
    public function del($nav_id=''){
        $nav_id = I('get.nav_id');
        if(empty($nav_id)){
            $this->error('参数错误!');
        }

        //判断该导航下有没有子导航，有则不允许删除
        $child = M('Nav')->where(array('pid'=>$nav_id))->field('nav_id')->select();
        if(!empty($child)){
            $this->error('请先删除该导航下的子导航');
        }

        //删除该导航信息
        $res = M('Nav')->delete($nav_id);
        if($res !== false){
			 F('sys_nav_tree',null);
            $this->success('删除导航成功！',U('index'));
        }else{
            $this->error('删除导航失败！');
        }
    }
}
