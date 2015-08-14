<?php
// | Author: 枫叶 <735579768@qq.com> <http://www.zhaokeli.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;
/**
 * 后台用户控制器
 * @author 枫叶 <735579768@qq.com>
 */
class CommentsController extends AdminController {
 public function index(){
	 	$name=I('name');
		$map['name']=array('like','%'.$name.'%');
		//$map['status']=array('egt',0);
    	$this->pages(array(
					'model'=>'Comments',
					'where'=>$map,
					'order'=>'status asc,comments_id desc'
					));
	 $this->meta_title="留言列表";
	 $this->display();
	 }
public function check($comments_id=''){
	$this->data=M('comments')->find($comments_id);
	//M('comments')->where("Comments_id=$Comments_id")->setInc('is_view',1);
	$this->display();
	}	 
    function del(){
    	//$comments_id=I("id");//I('get.article_id');
		$comments_id=isset($_REQUEST['comments_id'])?I('get.comments_id'):I("id");
		if(empty($comments_id))$this->error('请先进行选择');
		$model=M('Comments');
    	$result=$model->where("comments_id in ($comments_id)")->delete();
    	if(result){
    	  $this->success('已经彻底删除',U('index'));
    	}else{
    	  $this->error('操作失败');
    	}
    }
	function delall(){
		$result=M('Comments')->where("1=1")->delete();
    	if(result){
    	  $this->success('已经清空',U('index'));
    	}else{
    	  $this->error('操作失败');
    	}		
		}
}
