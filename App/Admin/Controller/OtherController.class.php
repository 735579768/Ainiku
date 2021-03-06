<?php
namespace Admin\Controller;
defined("ACCESS_ROOT") || die("Invalid access");
class OtherController extends AdminController {
	/* 退出登录 */
	public function logout() {
		if (is_login()) {
			//session('user_auth', null);
			//session('user_auth_sign', null);
			//  session('[destroy]');
			session(null);
			cookie(null);
			$this->success('退出成功！', U('Public/login'));
		} else {
			$this->redirect(U('Public/login'));
		}
	}

	/**
	 * 清空系统缓存目录
	 * **/
	public function clearCache($type = 'img') {
		$arr = array();
		// $rutimepath = str_replace(MODULE_NAME . '/', '', RUNTIME_PATH);
		//图片目录缓存
		// 		if ($type == 'data' || $type == 'all') {
		// 			$arr[] = del_allfile(STYLE_CACHE_DIR);
		// 			$arr[] = del_allfile(IMAGE_CACHE_DIR);
		// 		}
		// //数据目录缓存
		// 		if ($type == 'run' || $type == 'all') {
		// 			$arr[] = del_allfile(RUNTIME_PATH);
		// 		}

		$arr[] = del_allfile('./Data/cache/');
		//运行时目录缓存
		if (is_array($arr)) {
			//统计缓存大小
			$siz = 0;
			foreach ($arr as $aa) {
				foreach ($aa as $aaa) {
					$siz += $aaa['size'];
				}
			}
			$this->success("缓存已经清理完成,共计 " . ($siz / 1000) . " k", '', 6);
		}
	}
}
