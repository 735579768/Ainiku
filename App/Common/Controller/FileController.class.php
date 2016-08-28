<?php
// | Author: 枫叶 <735579768@qq.com> <http://www.zhaokeli.com>
// +----------------------------------------------------------------------
namespace Common\Controller;
use Think\Controller;

defined("ACCESS_ROOT") || die("Invalid access");
/**
 * 文件控制器
 * 主要用于下载模型的文件上传和下载
 */
class FileController extends Controller {
	public function getFileInfo() {
		$id    = I('post.id');
		$type  = I('post.type');
		$data  = [];
		$idarr = preg_replace('/\,|\|\s/', ',', $id);
		if ($type == 'img') {
			$data = M('Picture')->where(['id' => ['in', "$idarr"]])->select();
		} else {
			$model = M('File');
			$data  = $model->where(['id' => ['in', "$idarr"]])->select();
			// echo $model->_sql();
		}
		$this->success($data);
	}
	private function checksha($filepath = '') {
		$fpath = '.' . $filepath;
		if (is_file($fpath)) {
			$sha1 = sha1_file($fpath);
			$list = M('Picture')->where("sha1='$sha1'")->find();
			if (empty($list)) {
				return array('path' => $filepath, 'sha1' => $sha1);
			} else {
				//删除当前路径文件
				unlink($fpath);
				return $list;
			}
		} else {
			return $filepath;
		}
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

		$result = del_file($id);
		if ($result) {
			$this->success('删除成功', U('attach'));
		} else {
			$this->error(L('_DELETE_FAIL_'));
		}
	}

	//删除图片
	public function delimg($id = '') {
		if (empty($id)) {
			$this->error(L('_ID_NOT_NULL_'));
		}

		//删除本地文件
		$result = del_image($id);
		if ($result !== false) {
			$this->success('删除成功');
		} else {
			$this->error(L('_DELETE_FAIL_'));
		}
	}

	/**
	 * 上传文件
	 * @author huajie <banhuajie@163.com>
	 */
	public function uploadfile() {
		//TODO: 用户登录检测

		/* 返回标准数据 */
		$return       = array('status' => 1, 'info' => '上传成功', 'path' => '', 'id' => '', 'url' => '', 'data' => '');
		$SITE_PATH    = SITE_PATH; //网站根目录
		$targetFolder = C('FILE_UPLOAD.rootPath'); //保存图片的根目录
		$JDtargetPath = '';
		$data         = array();
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
			$XDtargetPath = $targetFolder . '/' . date('Ymd') . '/' . $filename;
			create_folder(dirname($XDtargetPath));
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

		if ($return['status'] == 1) {
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
		$SITE_PATH    = SITE_PATH; //网站根目录
		$targetFolder = path_a(C('FILE_UPLOAD.rootPath')); //保存图片的根目录
		$JDtargetPath = '';
		$data         = array();
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
			if (!create_folder($imgpath)) {
				$return['info']   = '创建目录错误：' . $imgpath;
				$return['status'] = 0;
				$this->ajaxreturn($return);
			}
			$imgpath2 = $targetFolder . $foldertype . '/' . date('Ymd');
			if (!create_folder($imgpath)) {
				$return['info']   = '创建目录错误：' . $imgpath;
				$return['status'] = 0;
				$this->ajaxreturn($return);
			}

			$JDtargetPath = $targetFolder . '/' . date('Ymd') . '/' . $filename;

			// Validate the file type
			$fileTypes = array('jpg', 'jpeg', 'gif', 'png'); // File extensions

			if (in_array($extend[$va], $fileTypes)) {
				$bl = move_uploaded_file($tempFile, $JDtargetPath);

				$JDthumbPath = str_replace('/image/', '/image/thumb/', $JDtargetPath);
				if ($bl) {
					//判断是不是已经上传过类似图片
					$shafile      = $this->checksha(str_replace($SITE_PATH, '', $JDtargetPath));
					$JDtargetPath = $SITE_PATH . $shafile['path'];
					$data['sha1'] = $shafile['sha1'];
					//如果是图片就生成缩略图
					if (in_array($extend[$va], array('jpg', 'jpeg', 'gif', 'png', 'bmp'))) {
						//生成缩略图
						//缩略图路径
						$re = create_thumb($JDtargetPath, $JDthumbPath, C('THUMB_WIDTH'), C('THUMB_HEIGHT'));
						if ($re !== true) {
							$JDthumbPath = $JDtargetPath;
						}

					}
					$return['path'] = path_r($JDthumbPath);
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

		if ($return['status'] == 1) {
			//保存文件信息到数据库
			$cupath       = path_r($JDtargetPath);
			$data['path'] = $cupath;
			//$data['sha1']        = $shafile['sha1'];
			$data['thumbpath']   = path_r($JDthumbPath);
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
		}

		/* 返回JSON数据 */
		$this->ajaxReturn($return);
	}

	public function ueupload() {
		// die('run');
		$CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents(path_a(__STATIC__ . "/ueditor/php/config.json"))), true);
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
			$result = include path_a(__STATIC__ . "/ueditor/php/action_upload.php");
			break;

		/* 列出图片 */
		case 'listimage':
			$result = include path_a(__STATIC__ . "/ueditor/php/action_list.php");
			break;
		/* 列出文件 */
		case 'listfile':
			$result = include path_a(__STATIC__ . "/ueditor/php/action_list.php");
			break;

		/* 抓取远程文件 */
		case 'catchimage':
			$result = include path_a(__STATIC__ . "/ueditor/php/action_crawler.php");
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

			//保存到数据库
			$result = json_decode($result, true);
			//判断是不是已经上传过类似图片
			$shafile       = $this->checksha($result['url']);
			$result['url'] = $shafile['path'];
			echo json_encode($result);
			if (!empty($result['url'])) {
				if ($action == 'uploadimage') {
					$thumb   = str_replace("/Uploads/image/", "/Uploads/image/thumb/", $result['url']);
					$JDthumb = path_a($thumb);
					create_folder(dirname($JDthumb));
					//生成缩略图
					$srcpath = path_a($result['url']);
					$srcpath = str_replace('\\', '/', $srcpath);
					$re      = create_thumb($srcpath, $JDthumb, C('THUMB_WIDTH'), C('THUMB_HEIGHT'));
					$thumb   = file_exists('.' . $thumb) ? $thumb : $result['url'];
					M('Picture')->add(array(
						'sha1'        => $shafile['sha1'],
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
	//图片添加水印
	private function markpic($dst = null) {
		//取水印图片
		$src       = realpath('.' . get_picture(C('SHUIYIN_IMG')));
		$shuiyinon = C('SHUIYIN_ON');
		if ($shuiyinon == '1' && $dst !== false && $src !== false) {
			image_water($dst, $src, $dst);
		} else if ($shuiyinon == '2' && $dst !== false && $src !== false) {
			image_water($dst, '', $dst, C('SHUIYIN_TEXT'));
		}
	}
	/**
	 * 批量生成缩略图
	 * @return [type] [description]
	 */
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
					$spath   = get_picture($thumbpath[$i], 'path');
					$thupath = str_replace('image/', 'image/thumb/', $spath);

					$spath = path_a($spath);

					$dpath = str_replace('image/', 'image/thumb/', $spath);
					if (file_exists($spath)) {
						//源文件存在
						if (file_exists($dpath)) {
							unlink($dpath);
						}

						$result = create_thumb($spath, $dpath, C('THUMB_WIDTH'), C('THUMB_HEIGHT'));
						if ($result === true) {
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
				$catelist = F_get_cate_list(true, 'article');
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
					'is_show' => 3,
				),
			);
			$this->assign('fieldarr1', $field);
			$this->display();

		}
	}

}
