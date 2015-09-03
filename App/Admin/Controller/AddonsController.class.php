<?php
namespace Admin\Controller;
class AddonsController extends AdminController {
    public function index(){
	  $this->meta_title='插件管理';
      $dirlist=getDirList(__SITE_ROOT__.'/Plugins/');
      $dirlist=str_replace('Plugin', '', $dirlist);
      $addoninfo=array();
      foreach ($dirlist as $a){
          $temarr=runPluginMethod($a, 'getConfig');
          $mark=$a;//strtolower($temarr['mark']);
       	  $model=M('Addons')->where("mark='$mark'")->find();
        if(!empty($model)){
          //数据库中有插件的信息说明已经安装过啦
          //查询插件是不是有设置页面
            $setmenu=M('Menu')->where("`type`='$mark'")->find();
            //trace($setmenu);
        	$addoninfo[]=array(
        	    'id'=>$model['id'],
        		'name'=>$model['name'],
        	    'mark'=>$model['mark'],
        	    'author'=>$model['author'],
        	    'descr'=>$model['descr'],
        	    'install'=>$model['install'],
        	    'status'=>$model['status'],
              'type'=>$model['type'],
        	    'setmenu'=>$setmenu
        	);
        }else{
          //没有信息说明还没有安装
          	$temarr['mark']=$a;//strtolower($temarr['mark']);
          	$temarr['install']=0;
          	$temarr['status']=1;
            $temarr['type']='other';
          	$addoninfo[]=$temarr;
        }
      }
      //trace($addoninfo);
      $list0=array();
    //  $list1=array();
      //排序
      foreach ($addoninfo as $key=>$a){
        if($a['install']=='0'){
      	$list0[]=$addoninfo[$key];
      	unset($addoninfo[$key]);
   //     }else{
      //  $list1[]=$addoninfo[$key];	
        }
	}
       foreach ($addoninfo as $key=>$a){
        if($a['status']=='0'){
      	$list0[]=$addoninfo[$key];
      	unset($addoninfo[$key]);
        }
	}
         foreach ($addoninfo as $key=>$a){
        if($a['status']=='1'){
      	$list0[]=$addoninfo[$key];
      	unset($addoninfo[$key]);
        }      
      }
      //$addoninfo=array_merge($list0,$list1);
	$page=new \Ainiku\Arrpage($list0,I('pg'),10);
	$this->_list=$page->cur_page_data;
	$this->_page=$page->showpage(false);
	
      //$this->assign('_list',$list0);
	  $this->display();
    }
    /*
     * 禁用插件
     * */
    public function jinyong($id=null){
    	if(D('Addons')->jinyong($id)){
    		$this->success('禁用成功',U('index'));
    	}else{
    	  $this->error('操作失败');
    	}
    }
    /*
     * 启用插件
    * */
    public function qiyong($id=null){
        if(D('Addons')->qiyong($id)){
    	  $this->success('启用成功',U('index'));
    	}else{
    	  $this->error('操作失败');
    	}
    }
    /*
     * 安装插件
    * */
    public function install($mark=null){
		if(empty($mark)){
				$this->error('非法安装');
			}else{
				$result=runPluginMethod($mark, 'install');
				if(!$result)$this->error('插件安装失败,请联系做作者');
				$data= runPluginMethod($mark, 'getConfig');
				$data['mark']=$mark;//strtolower($data['mark']);
				$data['status']=0;
				$model=D('Addons');
				if($model->create($data)){
					$model->add();
					$this->success('安装成功',U('index'));
				}else{
					$this->error($model->getError());
				}
			}
//       }
    }
    /*
     * 卸载插件
    * */
    public function uninstall($id=null){
      $rows=M('Addons')->where("id=$id")->find();
      $mark=$rows['mark'];
	  if(empty($mark)){
		  $this->error('卸载失败');
		  }else{
		  $result=runPluginMethod($mark, 'uninstall');
		  if(!$result)$this->error('插件卸载失败,请联系做作者');
		  $result=M('Addons')->where("id=$id")->delete();
		  if($result){
			$this->success('卸载成功',U('index'));
		  }else{
			$this->error('卸载失败');
		  }			  
			  }

    }

    /*
     * 运行插件方法
     * */
    public function plugin($pn=null,$pm=null){
    	$str=runPluginMethod($pn,$pm);
    	$this->assign('plugincontent',$str);
    	$this->display();
    }
}
