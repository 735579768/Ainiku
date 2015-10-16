<?php
namespace Admin\Controller;
use Think\Controller;
if(!defined("ACCESS_ROOT"))die("Invalid access");
class ArchiveController extends AdminController {
	private $m_name='';
	private $m_info=null;
	public function __construct(){
			$model_id=I('model_id');
			$this->m_name=$model_id;
			if(is_numeric($model_id)){
					$this->m_info=getModel($model_id);
				}else{
					$this->m_info=M('Model')->where("`table`='{$model_id}'")->find();
					}
			
			parent::__construct();	
		}
	 protected function _initialize(){
		 parent:: _initialize();
		 if($this->m_name=='')$this->error('文档模型ID不存在');
		 if(empty($this->m_info))$this->error('文档不存在');
		 $this->assign('model',$this->m_info);
		 $this->assign('preid',getTable($this->m_info['table'],false).'_id');
	 }
	/**
	 *存档列表
	 */
	public  function index(){

		//解析列表规则
        $fields = array();
        $grids  = preg_split('/[;\r\n]+/s', $this->m_info['list_format']);
        foreach ($grids as &$value) {
            // 字段:标题:链接
            $val      = explode(':', $value);
            // 支持多个字段显示
            $field   = explode(',', $val[0]);
            $value    = array('field' => $field, 'title' => $val[1]);
            if(isset($val[2])){
                // 链接信息
                $value['href']  =   $val[2];
                // 搜索链接信息中的字段信息
                preg_replace_callback('/\[([a-z_]+)\]/', function($match) use(&$fields){$fields[]=$match[1];}, $value['href']);
            }
            if(strpos($val[1],'|')){
                // 显示格式定义
                list($value['title'],$value['format'])    =   explode('|',$val[1]);
            }
            foreach($field as $val){
                $array  =   explode('|',$val);
                $fields[] = $array[0];
            }
        }
		$map=array();
		 //解析搜索字段
		 if(!empty($this->m_info['search_format'])){
		 $search_format=explode(':',$this->m_info['search_format']);
		 $this->assign('search_format',array('title'=>$search_format[1],'field'=>$search_format[0]));
		 $map[$search_format[0]]=array('like','%'.I($search_format[0]).'%');
		 }
		 
        // 过滤重复字段信息 TODO: 传入到查询方法
        $fields = array_unique($fields);
		$this->assign('field',$fields);
		$this->assign('list_grids',$grids);
		$this->assign('model_list',$this->m_info);
		$map['status']=1;
		$this->pages(array(
						'where'=>$map,
						'model'=>$this->m_info['table'],
						'order'=>getTable($this->m_info['table'],false).'_id  desc'
		 ));
		 $this->meta_title =$this->m_info['title'].'列表';

		 $this->display('index');
		}
	/**
	 *回收站
	 **/
	 function recycle(){
			//解析列表规则
			$fields = array();
			$grids  = preg_split('/[;\r\n]+/s', $this->m_info['recycle_format']);
			foreach ($grids as &$value) {
				// 字段:标题:链接
				$val      = explode(':', $value);
				// 支持多个字段显示
				$field   = explode(',', $val[0]);
				$value    = array('field' => $field, 'title' => $val[1]);
				if(isset($val[2])){
					// 链接信息
					$value['href']  =   $val[2];
					// 搜索链接信息中的字段信息
					preg_replace_callback('/\[([a-z_]+)\]/', function($match) use(&$fields){$fields[]=$match[1];}, $value['href']);
				}
				if(strpos($val[1],'|')){
					// 显示格式定义
					list($value['title'],$value['format'])    =   explode('|',$val[1]);
				}
				foreach($field as $val){
					$array  =   explode('|',$val);
					$fields[] = $array[0];
				}
			}
			// 过滤重复字段信息 TODO: 传入到查询方法
			$fields = array_unique($fields);
			$this->assign('field',$fields);
			$this->assign('list_grids',$grids);
			$this->assign('model_list',$this->m_info);
		$map['status']=-1;
		$this->pages(array(
					'where'=>$map,
					'model'=>$this->m_info['table'],
					'order'=>getTable($this->m_info['table'],false).'_id  desc'
		 ));
			 $this->meta_title =$this->m_info['title'].'回收站';
			 $this->display('index');		 
		 }
	/**
	 *添加文档
	 */
	public function add(){
		$Document = D($this->m_info['table']);
		if(IS_POST){
				if($Document->create()){
					$Document->create_time=NOW_TIME;
					$Document->update_time=NOW_TIME;
					$result=$Document->add();
					$result>0?$this->success(L('_ADD_SUCCESS_'),__FORWARD__):$this->error(L('_ADD_FAIL_'));
					}else{
						$this->error($Document->geterror());
						}			
			}else{
					$this->assign('model_id',$this->m_info['model_id']);
					$field=getModelAttr($this->m_info['model_id']);
					$this->assign('fieldarr',$field);
					$this->assign('data',$data);
					$this->meta_title = '添加'.$this->m_info['title'];
					$this->display('edit');					
				}
	
		}
	/**
	 *编辑文档
	 */
	public function edit(){
			$Document = D($this->m_info['table']);
		if(IS_POST){
				if($Document->create()){
					$Document->create_time=NOW_TIME;
					$result=$Document->save();
					$result>0?$this->success(L('_UPDATE_SUCCESS_'),__FORWARD__):$this->error(L('_UPDATE_FAIL_'));
					}else{
						$this->error($Document->geterror());
						}
			}else{
			   $id     =   I('get.id','');
				if(empty($id)){
					$this->error('参数不能为空！');
				}
		
				/*获取一条记录的详细数据*/
				
				$data = $Document->find($id);
				if(!$data){
					$this->error($Document->getError());
				}
				$this->assign('model_id',$this->m_info['model_id']);
				$field=getModelAttr($this->m_info['model_id']);
				$this->assign('fieldarr',$field);
				$this->assign('data',$data);
				$this->meta_title   =   '编辑'.$this->m_info['title'];
				$this->display();						
				}

		}
	/**
	 *移动文档到回收站
	 */
	public function del(){
				$id=I("id");
				if(empty($id))$this->error('请先进行选择');
				$preid=getTable($this->m_info['table'],false).'_id';
				$result=M($this->m_info['table'])->where("{$preid} in($id)")->save(array('status'=>-1));  
			    $result>0?$this->success(L('_TO_RECYCLE_'),U('recycle',array('model_id'=>I('model_id')))):$this->error(L('_CAOZUO_FAIL_'));	
		}
	/**
	 *从回收站删除文档
	 */
    function dele(){
    	$id=I("id");//I('get.id');
		if(empty($id))$this->error('请先进行选择');
		$preid=getTable($this->m_info['table'],false).'_id';
    	$result=M($this->m_info['table'])->where("{$preid} in ($id)")->delete();
		$result>0?$this->success(L('_CHEDI_DELETE_'),U('recycle',array('model_id'=>I('model_id')))):$this->error(L('_CAOZUO_FAIL_'));
    }
	function huifu(){
		  $id=I("id");
		  if(empty($id))$this->error('请先进行选择');
		  $preid=getTable($this->m_info['table'],false).'_id';
		  $uid=M($this->m_info['table'])->where("{$preid} in($id)")->save(array('status'=>1));
		  if(0<$uid){
			$this->success(L('_TO_HUIFU_'),U('index',array('model_id'=>I('model_id'))));
		  }else{
			$this->error(L('_CAOZUO_FAIL_'));
		  }
    }
	function delall(){
			$result=M($this->m_info['table'])->where("status=-1")->delete();
			if(result){
			  $this->success(L('_CLEAR_NULL_'),U('recycle',array('model_id'=>I('model_id'))));
			}else{
			  $this->error(L('_CAOZUO_FAIL_'));
			}		
		}
}