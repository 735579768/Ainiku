<?php
// | Author: 枫叶 <735579768@qq.com> <http://www.zhaokeli.com>
// +----------------------------------------------------------------------
namespace Admin\Controller;
defined("ACCESS_ROOT") || die("Invalid access");
/**
 * 文件控制器
 * 主要用于下载模型的文件上传和下载
 */
class FileController extends AdminController {
	private function checksha($filepath = '') {
		if (is_file($filepath)) {
			$sha1 = sha1_file('.' . $filepath);
			$list = M('Picture')->where("sha1='$sha1'")->find();
			if (empty($list)) {
				return $filepath;
			} else {
				//删除当前路径文件
				unlink('.' . $filepath);
				return $list['path'];
			}
		} else {
			return $filepath;
		}
	}
	//重新生成文件的sha1
	function resetsha1() {
		$list = F('resetshalist');
		if (empty($list)) {
			$list = M('Picture')->field('id,path')->select();
			F('resetshalist', $list);
			F('resetshalistnum', count($list));
		}
		$total = F('resetshalistnum');
		$i     = 1;
		foreach ($list as $key => $val) {
			$i++;
			$sha1 = sha1_file('.' . $val['path']);
			M('Picture')->where("id={$val['id']}")->save(array('sha1' => $sha1));
			unset($list[$key]);
			if ($i > 1000 || empty($list)) {
				$num = count($list);
				if ($num > 0) {
					F('resetshalist', $list);
					$this->error('已经处理' . ($total - $num) . ',还有' . $num . '张');
				} else {
					F('resetshalist', null);
					F('resetshalistnum', null);
					$this->success('sha1重置成功,总共有' . $total . '张图片');
				}
				break;
			}

		}

	}
	//附件管理
	public function attach() {
		$this->pages(array(
			'model' => 'File',
			'rows'  => 24,
			'where' => $map,
			'order' => 'create_time desc,id desc',
		));
		$this->meta_title = '附件管理';
		$this->display();
	}
	public function deleditorimg($s = null, $d = null) {
		$re = '/[&lt;|<]img.*?src=[\'|\"]{1}(.*?)[\'|\"]{1}.*?[&lt;|<]/';
		preg_match_all($re, $s, $simg);
		preg_match_all($re, $d, $dimg);
		foreach ($simg[1] as $key => $val) {
			if (!in_array($val, $dimg[1])) {
				$result = M('Picture')->where("uid=" . UID . "  and (path='$val' or thumbpath='$val')  ")->delete();
				if (file_exists('.' . $val)) {
					unlink('.' . $val);
				}

				//删除缩略图
				$thumbpath = str_replace('image', 'image/thumb', '.' . $val);
				if (file_exists($thumbpath)) {
					unlink($thumbpath);
				}

			}
		}
		return null;
	}
	public function delattach($id = null) {
		if (empty($id)) {
			return false;
		}

		$result = delfile($id);
		if ($result) {
			$this->success('删除成功', U('attach'));
		} else {
			$this->error(L('_DELETE_FAIL_'));
		}
	}
	//图片管理
	public function imglist() {
		$starttime = strtotime(I('starttime'));
		$endtime   = strtotime(I('endtime'));
		$field     = array(
			'start' => array(
				'field'   => 'starttime',
				'name'    => 'starttime',
				'type'    => 'datetime',
				'title'   => '开始时间',
				'note'    => '',
				'extra'   => null,
				'is_show' => 3,
				'value'   => $starttime,
			),
			'end'   => array(
				'field'   => 'endtime',
				'name'    => 'endtime',
				'type'    => 'datetime',
				'title'   => '结束时间',
				'note'    => '',
				'extra'   => null,
				'is_show' => 3,
				'value'   => $endtime,
			),
		);
		$this->assign('fieldarr', $field);
		$this->assign('data', null);
		$map = null;
		if (!empty($starttime) && !empty($endtime)) {
			$map = 'create_time > ' . $starttime . ' and ' . 'create_time < ' . $endtime;
		} else if (!empty($starttime)) {
			$map = 'create_time > ' . $starttime;
		} else if (!empty($endtime)) {
			$map = 'create_time < ' . $endtime;
		}
		$this->pages(array(
			'model' => 'Picture',
			'rows'  => 24,
			'where' => $map,
			'order' => 'create_time desc,id desc',
		));
		$this->meta_title = '图片管理';
		$this->display();
	}
	//显示没有关联的图片
	public function nolinkimg() {
		set_time_limit(0);
		$rows = M('Picture')->select();
		//查找无效图片
		//首先查找文章或产品中的图片(去除有效的)
		foreach ($rows as $key => $val) {
			$result = M('Article')->where("pic like '%{$val['id']}%'")->select();
			if (0 < $result) {
				unset($rows[$key]);
			}

			$result = M('Goods')->where("pic like '%{$val['id']}%' or xc  like '%{$val['id']}%'")->select();
			if (0 < $result) {
				unset($rows[$key]);
			}

			$result = M('Category')->where("ico like '%{$val['id']}%'")->select();
			if (0 < $result) {
				unset($rows[$key]);
			}

			$result = M('Link')->where("pic like '%{$val['id']}%'")->select();
			if (0 < $result) {
				unset($rows[$key]);
			}

		}
		foreach ($rows as $key => $val) {
			$result = M('Article')->where("content like '%{$val['destname']}%'")->select();
			if (0 < $result) {
				unset($rows[$key]);
			}

			$result = M('Goods')->where("content like '%{$val['destname']}%'")->select();
			if (0 < $result) {
				unset($rows[$key]);
			}

		}
		$page             = new \Ainiku\Arrpage($rows, 1, 10);
		$this->_list      = $page->cur_page_data;
		$this->_page      = $page->showpage(false);
		$this->meta_title = '无效图片管理';
		$this->display('imglist');
	}
	//删除图片
	public function delimg($id = '') {
		if (empty($id)) {
			$this->error(L('_ID_NOT_NULL_'));
		}

		//删除本地文件
		$result = delimage($id);
		if ($result !== false) {
			$this->success('删除成功');
		} else {
			$this->error(L('_DELETE_FAIL_'));
		}
	}
	/**
	 *上传文件
	 */
	public function upload() {
		$return = array('status' => 1, 'info' => '上传成功', 'data' => '', 'url' => '');
		/* 调用文件上传组件上传文件 */
		$File        = D('File');
		$file_driver = C('DOWNLOAD_UPLOAD_DRIVER');
		$info        = $File->upload(
			$_FILES,
			C('DOWNLOAD_UPLOAD'),
			C('DOWNLOAD_UPLOAD_DRIVER'),
			C("UPLOAD_{$file_driver}_CONFIG")
		);

		/* 记录附件信息 */
		if ($info) {
			$return['id'] = $info['download']['id'];
			// $return['data'] = json_encode($info['download']);
			$return['info'] = $info['download']['name'];
		} else {
			$return['status'] = 0;
			$return['info']   = $File->getError();
		}

		/* 返回JSON数据 */
		$this->ajaxReturn($return);
	}
	/**
	 * 上传文件
	 * @author huajie <banhuajie@163.com>
	 */
	public function uploadfile() {
		//TODO: 用户登录检测

		/* 返回标准数据 */
		$return       = array('status' => 1, 'info' => '上传成功', 'path' => '', 'id' => '', 'url' => '', 'data' => '');
		$SITE_PATH    = __SITE_ROOT__; //网站根目录
		$targetFolder = C('FILE_UPLOAD.rootPath'); //保存图片的根目录
		if (!empty($_FILES)) {
			$tempFile = $_FILES['filelist']['tmp_name'];
			//生成的文件名字
			$extend   = explode(".", $_FILES['filelist']['name']);
			$va       = count($extend) - 1;
			$filename = time() . mt_rand(10000, 99999) . "." . $extend[$va];
			//文件类型文件夹
			$foldertype = '';
			switch ($extend[$va]) {
			case 'jpg':$targetFolder .= '/file/image';
				break;
			case 'png':$targetFolder .= '/file/image';
				break;
			case 'gif':$targetFolder .= '/file/image';
				break;
			case 'jpeg':$targetFolder .= '/file/image';
				break;
			case 'bmp':$targetFolder .= '/file/image';
				break;
			case 'mp4':$targetFolder .= '/file/video';
				break;
			default:$targetFolder .= '/file/other';
				break;
			}
			//原文件的相对路径到文件名字
			$XDtargetPath    = $targetFolder . '/' . date('Ymd') . '/' . $filename;
			$temarr          = explode('.', $XDtargetPath);
			$XDtargetPathdir = str_replace($filename, '', $XDtargetPath);
			createFolder($XDtargetPathdir);
			//原图文件绝对路径目录
			$targetPath = $SITE_PATH . $targetFolder . '/' . date('Ymd'); //保存原文件的绝对路径

			//原图和缩略图的绝对路径到文件名字
			$JDtargetPath = $targetPath . '/' . $filename;

			// Validate the file type

			$fileTypes = array("png", "jpg", "jpeg", "gif", "bmp", "flv", "swf", "mkv", "avi", "rm", "rmvb", "mpeg", "mpg", "ogg", "ogv", "mov", "wmv", "mp4", "webm", "mp3", "wav", "mid", "rar", "zip", "tar", "gz", "7z", "bz2", "cab", "iso", "doc", "docx", "xls", "xlsx", "ppt", "pptx", "pdf", "txt", "md", "xml"); // File extensions

			if (in_array($extend[$va], $fileTypes)) {
				$bl = move_uploaded_file($tempFile, $JDtargetPath);
				if ($bl) {
					$return['path'] = str_replace($SITE_PATH, '', $JDtargetPath);
				} else {
					$return['info']   = '上传错误' . $tempFile . '->' . $JDtargetPath;
					$return['status'] = 0;
					$this->ajaxReturn($return);
				}
			} else {
				$return['info']   = '不支持此文件类型';
				$return['status'] = 0;
				$this->ajaxReturn($return);
			}
		} else {
			$return['info']   = "文件不能为空";
			$return['status'] = 0;
			$this->ajaxReturn($return);
		}

		//保存文件信息到数据库
		$data['path']        = str_replace($SITE_PATH, '', $JDtargetPath);
		$data['destname']    = $filename;
		$data['srcname']     = $_FILES['filelist']['name'];
		$data['size']        = $_FILES['filelist']['size'];
		$data['create_time'] = NOW_TIME;
		$data['uid']         = UID;
		$model               = D('File');
		if ($model->create($data)) {
			$result = $model->add($data);
			if (0 < $result) {
				$return['id'] = $result;
			} else {
				$return['status'] = 0;
				$return['info']   = '添加到数据库时出错';
				$this->ajaxReturn($return);
			}
		} else {
			$return['status'] = 0;
			$return['info']   = $model->geterror();
			$this->ajaxReturn($return);
		}

		/* 返回JSON数据 */
		$return['info'] = $data['srcname'];
		$this->ajaxReturn($return);
	}
	/**
	 * 上传图片
	 * @author huajie <banhuajie@163.com>
	 */
	public function uploadpic() {
		//TODO: 用户登录检测

		/* 返回标准数据 */
		$return       = array('status' => 1, 'info' => '上传成功', 'path' => '', 'id' => '', 'url' => '', 'data' => '');
		$SITE_PATH    = __SITE_ROOT__; //网站根目录
		$targetFolder = pathA(C('FILE_UPLOAD.rootPath')); //保存图片的根目录
		if (!empty($_FILES)) {
			$tempFile = $_FILES['filelist']['tmp_name'];
			//生成的文件名字
			$extend   = explode(".", strtolower($_FILES['filelist']['name']));
			$va       = count($extend) - 1;
			$filename = time() . mt_rand(10000, 99999) . "." . $extend[$va];
			//文件类型文件夹
			$foldertype = '';
			switch ($extend[$va]) {
			case 'jpg':$targetFolder .= '/image';
				$foldertype = '/thumb';
				break;
			case 'png':$targetFolder .= '/image';
				$foldertype = '/thumb';
				break;
			case 'gif':$targetFolder .= '/image';
				$foldertype = '/thumb';
				break;
			case 'jpeg':$targetFolder .= '/image';
				$foldertype = '/thumb';
				break;
			case 'bmp':$targetFolder .= '/image';
				$foldertype = '/thumb';
				break;
			case 'mp4':$targetFolder .= '/file/video';
				break;
			default:$targetFolder .= '/file/other';
				break;
			}
			$imgpath = $targetFolder . '/' . date('Ymd');
			if (!createFolder($imgpath)) {
				$return['info']   = '创建目录错误：' . $imgpath;
				$return['status'] = 0;
				$this->ajaxreturn($return);
			}
			$imgpath2 = $targetFolder . $foldertype . '/' . date('Ymd');
			if (!createFolder($imgpath)) {
				$return['info']   = '创建目录错误：' . $imgpath;
				$return['status'] = 0;
				$this->ajaxreturn($return);
			}

//			$thumbPath=$targetFolder.$foldertype;
			//
			//
			//
			//			//原图和缩略图的相对路径到文件名字
			//			//原图和缩略图的相对路径到文件名字
			//			$XDtargetPath=$targetFolder.'/'.date('Ymd');
			//			$XDthumbPath=$thumbPath.'/'.date('Ymd');
			//			if(!createFolder($XDtargetPath)){
			//					$return['info']='创建目录错误：'.$XDtargetPath;
			//					 $return['status']=0;
			//					 $this->ajaxreturn($return);
			//				}
			//			if(!createFolder($XDthumbPath)){
			//					$return['info']='创建目录错误：'.$XDthumbPath;
			//					 $return['status']=0;
			//					 $this->ajaxreturn($return);
			//				}
			$JDtargetPath = $targetFolder . '/' . date('Ymd') . '/' . $filename;
//			$JDthumbPath=$targetFolder.$foldertype.'/'.date('Ymd').'/'.$filename;
			//			//原图和缩略图的绝对路径目录
			//			$targetPath = $SITE_PATH .$targetFolder.'/'.date('Ymd');//保存原文件的绝对路径
			//			$thumbPath = $SITE_PATH .$thumbPath.'/'.date('Ymd');//保存缩略图的绝对路径

//			//原图和缩略图的绝对路径到文件名字
			//			$JDtargetPath=$targetPath.'/'. $filename;
			//			$JDthumbPath =$thumbPath.'/'. $filename;

			// Validate the file type
			$fileTypes = array('jpg', 'jpeg', 'gif', 'png', 'rar', 'mp4'); // File extensions

			if (in_array($extend[$va], $fileTypes)) {
				$bl = move_uploaded_file($tempFile, $JDtargetPath);
				//判断是不是已经上传过类似图片
				$JDtargetPath = $SITE_PATH . $this->checksha(str_replace($SITE_PATH, '', $JDtargetPath));
				$JDthumbPath  = str_replace('/image/', '/image/thumb/', $JDtargetPath);
				if ($bl) {
					//如果是图片就生成缩略图
					if (in_array($extend[$va], array('jpg', 'jpeg', 'gif', 'png', 'bmp'))) {
						//生成缩略图
						//缩略图路径
						$re = img2thumb($JDtargetPath, $JDthumbPath, C('THUMB_WIDTH'), C('THUMB_HEIGHT'));
						if ($re === false) {
							$JDthumbPath = $JDtargetPath;
						}

					}
					$return['path'] = pathR($JDthumbPath);
				} else {
					$return['info']   = '上传错误' . $tempFile . '->' . $JDtargetPath;
					$return['status'] = 0;
				}
			} else {
				$return['info']   = '不支持此文件类型';
				$return['status'] = 0;
			}
		} else {
			$return['info']   = "文件不能为空";
			$return['status'] = 0;
		}

		//保存文件信息到数据库
		$cupath              = pathR($JDtargetPath);
		$data['path']        = $cupath;
		$data['sha1']        = sha1_file('.' . $cupath);
		$data['thumbpath']   = pathR($JDthumbPath);
		$data['destname']    = $filename;
		$data['srcname']     = $_FILES['filelist']['name'];
		$data['create_time'] = time();
		$data['uid']         = UID;
		$model               = M('picture');
		if ($model->create($data)) {
			$result = $model->add($data);
			if ($result) {
				//添加水印
				$this->markpic($JDthumbPath);
				$this->markpic(realpath('.' . $data['path']));
				$return['id'] = $result;
			}
		}

		/* 返回JSON数据 */
		$this->ajaxReturn($return);
	}

	public function ueupload() {
		$CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents(pathA(__STATIC__ . "/ueditor/php/config.json"))), true);
//"imagePathFormat": "/Uploads/image/{yyyy}{mm}{dd}/{time}{rand:6}",
		$CONFIG['imagePathFormat']      = __ROOT__ . "/Uploads/image/{yyyy}{mm}{dd}/{time}{rand:6}";
		$CONFIG['scrawlPathFormat']     = __ROOT__ . "/Uploads/image/{yyyy}{mm}{dd}/{time}{rand:6}";
		$CONFIG['snapscreenPathFormat'] = __ROOT__ . "/Uploads/image/{yyyy}{mm}{dd}/{time}{rand:6}";
		$CONFIG['catcherPathFormat']    = __ROOT__ . "/Uploads/image/{yyyy}{mm}{dd}/{time}{rand:6}";
		$CONFIG['videoPathFormat']      = __ROOT__ . "/Uploads/file/video/{yyyy}{mm}{dd}/{time}{rand:6}";
		$CONFIG['filePathFormat']       = __ROOT__ . "/Uploads/file/other/{yyyy}{mm}{dd}/{time}{rand:6}";
		$CONFIG['fileManagerListPath']  = __ROOT__ . "/Uploads/file/"; /* 指定要列出文件的目录 */
		$CONFIG['imageManagerListPath'] = __ROOT__ . "/Uploads/image/"; /* 指定要列出图片的目录 */
		$action                         = $_GET['action'];

		switch ($action) {
		case 'config':
			$result = json_encode($CONFIG);
			break;

		/* 上传图片 */
		case 'uploadimage':
		/* 上传涂鸦 */
		case 'uploadscrawl':
		/* 上传视频 */
		case 'uploadvideo':
		/* 上传文件 */
		case 'uploadfile':
			$result = include pathA(__STATIC__ . "/ueditor/php/action_upload.php");
			break;

		/* 列出图片 */
		case 'listimage':
			$result = include pathA(__STATIC__ . "/ueditor/php/action_list.php");
			break;
		/* 列出文件 */
		case 'listfile':
			$result = include pathA(__STATIC__ . "/ueditor/php/action_list.php");
			break;

		/* 抓取远程文件 */
		case 'catchimage':
			$result = include pathA(__STATIC__ . "/ueditor/php/action_crawler.php");
			break;

		default:
			$result = json_encode(array(
				'state' => '请求地址出错',
			));
			break;
		}

		/* 输出结果 */
		if (isset($_GET["callback"])) {
			if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
				echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
			} else {
				echo json_encode(array(
					'state' => 'callback参数不合法',
				));
			}
		} else {

			echo $result;
			//保存到数据库
			$result = json_decode($result, true);
			//判断是不是已经上传过类似图片
			$result['url'] = $this->checksha($result['url']);
			if (!empty($result['url'])) {
				if ($action == 'uploadimage') {
					$thumb      = str_replace("/Uploads/image/", "/Uploads/image/thumb/", $result['url']);
					$JDthumb    = pathA($thumb);
					$temarr     = explode('.', $JDthumb);
					$JDthumbdir = str_replace($temarr[count($temarr) - 1], '', $JDthumb);
					createFolder($JDthumbdir);
					//生成缩略图
					$srcpath = pathA($result['url']);
					$srcpath = str_replace('\\', '/', $srcpath);
					$re      = img2thumb($srcpath, $JDthumb, C('THUMB_WIDTH'), C('THUMB_HEIGHT'));
					$thumb   = file_exists('.' . $thumb) ? $thumb : $result['url'];
					M('Picture')->add(array(
						'sha1'        => sha1_file('.' . $result['url']),
						'srcname'     => $result['original'],
						'destname'    => $result['title'],
						'path'        => $result['url'],
						'thumbpath'   => $thumb,
						'create_time' => time(),
						'from'        => 'ueditor',
						'uid'         => UID,
					));
					//添加水印
					$this->markpic($JDthumb);
					$this->markpic(realpath('.' . $result['url']));
				} else {
					//把附件信息保存到数据库
					$data['path']        = $result['url'];
					$data['destname']    = $result['title'];
					$data['srcname']     = $result['original'];
					$data['size']        = $result['size'];
					$data['create_time'] = NOW_TIME;
					$data['uid']         = UID;
					$model               = D('File');
					if ($model->create($data)) {
						$result = $model->add($data);
					} else {
						\Think\Log::record('ue编辑器把附件信息写到数据库时出错' . $model->geterror());

					}
				}
			}
		}
		exit();
	}
////umeditor编辑器上传图片
	//public function umeupload(){
	//    $result=include __SITE_ROOT__.__STATIC__."/umeditor/php/imageUp.php";
	//	//保存到数据库
	//	//$result=json_decode($result,true);
	//	//var_dump($result);
	//		//判断是不是已经上传过类似图片
	//	$result['url']=$this->checksha($result['url']);
	//	if(!empty($result['url'])){
	//	$thumb=str_replace("/Uploads/image/","/Uploads/image/thumb/",$result['url']);
	//
	//	$JDthumb=__SITE_ROOT__.$thumb;
	//	if(!createFolder(dirname($JDthumb))){
	//		$return['info']='创建目录错误：'.$JDthumb;
	//		 $return['status']=0;
	//		 $this->ajaxreturn($return);
	//	}
	//	//生成缩略图
	//	$srcpath=__SITE_ROOT__.$result['url'];
	//	$srcpath=str_replace('\\','/',$srcpath);
	//	$re=img2thumb($srcpath,$JDthumb,C('THUMB_WIDTH'),C('THUMB_HEIGHT'));
	//
	//	//if($picture->create())
	//	//查看缩略图生成成功没有
	//	$thumb=file_exists('.'.$thumb)?$thumb:$result['url'];
	//	M('Picture')->add(array(
	//			'sha1'=>sha1_file('.'.$result['url']),
	//			'srcname'=>$result['originalName'],
	//			'destname'=>$result['name'],
	//			'path'=>$result['url'],
	//			'thumbpath'=>$thumb,
	//			'create_time'=>time(),
	//			'from'=>'umeditor',
	//			'uid'=>UID
	//	));
	//	//添加水印
	//	$this->markpic($JDthumb);
	//	$this->markpic(realpath('.'.$result['url']));
	//	}
	//	exit();
	//	}
	//图片添加水印
	private function markpic($dst = null) {
		//取水印图片
		$src       = realpath('.' . getPicture(C('SHUIYIN_IMG')));
		$shuiyinon = C('SHUIYIN_ON');
		if ($shuiyinon == '1' && $dst !== false && $src !== false) {
			markimg(array(
				'dst' => $dst, //原始图像
				'src' => $src, //水印图像
				'pos' => C('SHUIYIN_POS'), //水印位置('left,right,center')
			));
		} else if ($shuiyinon == '2' && $dst !== false && $src !== false) {
			markimg(array(
				'dst' => $dst, //原始图像
				'str' => C('SHUIYIN_TEXT'),
				'pos' => C('SHUIYIN_POS'), //水印位置('left,right,center')
			));
		}
	}
//批量生成缩略图
	function createthumb() {
		if (IS_POST) {
			$info = array(
				'status' => 2, //2代表没有完全生成
				'info'   => '',
				'url'    => '',
			);
			$arccatid = I('post.article_catid');
			$catid    = I('post.category_d');
			//查询是否有没有处理完的图片
			$thumbpath = F('thumbpath');
			if (empty($thumbpath)) {
				//开始查询要处理的图片数据
				$temarr     = array();
				$map['pic'] = array('neq', 0);
				if ($arccatid != 0) {
					$map['category_id'] = $arccatid;
				}

				$list = M('Article')->where($map)->field('pic')->select();

				foreach ($list as $val) {
					$temarr[] = $val['pic'];
				}

				if ($catid != 0) {
					$map['category_id'] = $catid;
				}

				$list = M('goods')->where($map)->field('pic,xc')->select();
				foreach ($list as $val) {
					$temarr[] = $val['pic'];
					//拆分相册
					$xcarr = explode('|', $val['xc']);
					foreach ($xcarr as $val) {
						$temarr[] = $val;
					}

				}
				//把查到的数量保存到缓存中
				F('thumbpath', $temarr);
				$thumbpath = $temarr;
			}
			//开始生成图片
			$jishu     = 0; //处理的总数量
			$sucstr    = ''; //成功字符串
			$failjishu = 0; //失败的数量
			$failstr   = ''; //错误字符串

			$num = count($thumbpath);
			$num = $num > 50 ? 50 : $num; //一次只处理50个数据

			$i = 0;
			for ($i; $i < $num; $i++) {
				if (count($thumbpath) > 0) {
					$spath   = getPicture($thumbpath[$i], 'path');
					$thupath = str_replace('image/', 'image/thumb/', $spath);

					$spath = pathA($spath);

					$dpath = str_replace('image/', 'image/thumb/', $spath);
					if (file_exists($spath)) {
						//源文件存在
						if (file_exists($dpath)) {
							unlink($dpath);
						}

						$result = img2thumb($spath, $dpath, C('THUMB_WIDTH'), C('THUMB_HEIGHT'));
						if ($result) {
							M('Picture')->where("id={$thumbpath[$i]}")->save(array('thumbpath' => $thupath));

							$jishu++;
							$sucstr .= $spath . '->' . $dpath . '<br>';
						} else {
							$failjishu++;
							$failstr .= $spath . '->' . $dpath . '<br>';
						}
					} else {
						$failjishu++;
						$failstr .= '<span style="color:red;">' . $spath . "此路径文件丢失数据库id为{$thumbpath[$i]}请联系管理员自行处理</span><br>";
					}
					unset($thumbpath[$i]);
				}
			}
			$tishistr = '全部完成';
			if (count($thumbpath) <= 0) {
				$info['status'] = 1;
			} else {
				$tishistr = '还剩' . count($thumbpath) . '个。继续生成中......';
			}
			F('thumbpath', array_values($thumbpath));
			$info['info'] = "成功生成{$jishu}个缩略图,失败{$failjishu}个,{$failstr},$tishistr";
			$this->ajaxreturn($info);
			exit();

		} else {
			//文章分类树
			$catelist = F('sys_category_tree');
			if (empty($catelist)) {
				$catelist = F_getCatelist(true, 'article');
				F('sys_category_tree', $catelist);
			}
			$catelist[0] = '全部分类';
			$field       = array(
				array(
					'field'   => 'arccat_catid',
					'name'    => 'arccat_catid',
					'type'    => 'select',
					'title'   => '文章分类',
					'note'    => '',
					'extra'   => $catelist,
					'is_show' => 1,
				),
			);
			$this->assign('fieldarr1', $field);

//				//产品分类树
			//			$catelist=F('sys_goodscat_tree');
			//			if(empty($catelist)){
			//				$catelist=F_getCatelist(true,'goods');
			//				F('sys_goodscat_tree',$catelist);
			//				}
			//			$catelist[0]='全部分类';
			//			$field=array(
			//					array(
			//						'field'=>'category_id',
			//						'name'=>'category_id',
			//						'type'=>'select',
			//						'title'=>'产品分类',
			//						'note'=>'',
			//						'extra'=>$catelist,
			//						'is_show'=>1
			//					)
			//			);
			//		$this->assign('fieldarr2',$field);
			$this->display();

		}
	}

}
