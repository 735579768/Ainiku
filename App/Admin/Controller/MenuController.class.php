<?php
namespace Admin\Controller;
if(!defined("ACCESS_ROOT"))die("Invalid access");
class MenuController extends AdminController {
 public function __construct(){
		 parent::__construct();
		 $this->model_name='Menu';
		 //$this->primarykey='id';
		 } 
    /**
     * 配置管理
     * @author 枫叶 <735579768@qq.com>
     */
    public function index(){
		//$list=M('menu')->where("hide=0 and pid=0")->select();
		$this->assign('meta_title','菜单列表');
        $tree = D('Menu')->getTree(0,'id,title,url,hide,sort,pid,group,is_dev,status');
		$this->assign('_TREE_',$tree);
		//trace(APP_DEBUG);
		$this->display();
    }
    /**
     * 显示分类树，仅支持内部调
     * @param  array $tree 分类树
     * @author 枫叶 <735579768@qq.com>
     */
    public function tree($tree = null){
        $this->assign('_TREE_', $tree);
        $this->display('tree');
    }
	function add($pid=''){
        if(IS_POST){
			F('sys_menu_tree',null);
            $model = D('Menu');
            $data = $model->create();
            if($data){
                if($model->add()){
                    $this->success('新增成功', U('index'));
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($model->getError());
            }
        } else {
			//$field=Api('Model/menuModel');
			$field=getModel('menu');
			$this->assign('fieldarr',$field);
			$data=array();
			if(!empty($pid))$data['pid']=$pid;
			$this->assign('data',$data);
			$this->assign('meta_title','添加菜单');
			$this->display('edit');
        }
		F("sys_menu_tree",null);
		}
    /**
     * 编辑配置
     * @author yangweijie <yangweijiester@gmail.com>
     */
    public function edit($id = 0){
        if(IS_POST){
			F('sys_menu_tree',null);
            $Menu = D('Menu');
            $data = $Menu->create();
            if($data){
                if($Menu->save()!== false){
                    $this->success('更新成功');
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($Menu->getError());
            }
        } else {
            $info = array();
            /* 获取数据 */
            $data = M('Menu')->find($id);
			$field=getModel('menu');
			$this->assign('fieldarr',$field);
			$this->assign('data',$data);
            $this->meta_title = '编辑菜单';
            $this->display();
        }
    }
     /**
     * 删除后台菜单
     * @author yangweijie <yangweijiester@gmail.com>
     */
    public function del($id=null){
		F('sys_menu_tree',null);
        $tem=M('menu')->where('pid='.$id)->find();
        if(!empty($tem)){
            $this->error('请删除子菜单后再操作');
        }else{
           $result=M('Menu')->where("id=$id")->delete();
        	if($result){
				$this->success('删除成功',U('Menu/index'));
				}else{
				$this->error('删除失败');	
					}
		}
    }
}