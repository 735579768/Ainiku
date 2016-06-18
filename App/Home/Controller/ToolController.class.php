<?php
namespace Home\Controller;
use Think\Controller;

defined("ACCESS_ROOT") || die("Invalid access");
class ToolController extends HomeController {
	/**
	 * [createjs description]
	 * 提取文章中的第一个图片
	 * @return [type] [description]
	 */
	function setFirstPicture() {
		$list = M('Article')->field('article_id,content,pic')->where('pic=0')->select();
		foreach ($list as $key => $value) {
			preg_match('/<img.*?src\=[\'|\"](.*?)[\'|\"].*?>/', $value['content'], $match);
			if ($match) {
				$info = M('Picture')->field('id')->where("path='{$match[1]}'")->find();
				if(!empty($info)){
					M('Article')->where('article='.$value['article_id'])->setField('pic',$info['id']);
				}
		}

	}
	function createjs() {
		$sheng = '';
		$shi   = '';
		$qu    = '';
		$list  = M('Area')->field('area_id,parent_id,area_name')->where('parent_id=0')->order('area_id asc')->select();
		$i     = true;
		$j     = true;
		$k     = true;
		foreach ($list as $key => $value) {
			$sheng .= $i ? "{$value['area_id']}:'{$value['area_name']}'" : ",{$value['area_id']}:'{$value['area_name']}'";
			//查询市
			$list1 = M('Area')->field('area_id,parent_id,area_name')->order('area_id asc')->where('parent_id=' . $value['area_id'])->select();

			$shi .= $j ? "{$value['area_id']}:{" : ",{$value['area_id']}:{";
			$j = false;
			$m = true;
			foreach ($list1 as $ke => $va) {
				$shi .= $m ? "{$va['area_id']}:'{$va['area_name']}'" : ",{$va['area_id']}:'{$va['area_name']}'";
				$m = false;
				//查询区域
				$list2 = M('Area')->field('area_id,parent_id,area_name')->order('area_id asc')->where('parent_id=' . $va['area_id'])->select();
				$qu .= $k ? "{$va['area_id']}:{" : ",{$va['area_id']}:{";
				$k = false;
				$n = true;
				foreach ($list2 as $ka => $v) {
					$qu .= $n ? "{$v['area_id']}:'{$v['area_name']}'" : ",{$v['area_id']}:'{$v['area_name']}'";
					$n = false;
				}
				$qu .= "}";
			}
			$shi .= "}";
			$i = false;
		}
		echo "var selectdata={'sheng':{{$sheng}},'shi':{{$shi}},'qu':{{$qu}}};";
/*		echo "var sheng={{$sheng}};";
echo "var shi={{$shi}};";
echo "var qu={{$qu}};";*/
		die();
	}
	//查询数据库中是不是有外链接
	function picurl() {
		$map['path'] = array('like', '%http%');
		$list        = M('Picture')->where($map)->select();
		dump($list);
	}
	function delurl() {
		set_time_limit(0);
		$list = M('Article')->field('article_id,content')->select(); //([a-zA-z]+\.)+[a-zA-z]{2,3}
		//$pattern='/([a-zA-z]+\.)+[a-zA-z]{2,3}/i';
		$pattern[] = '/\&(\w+?)\;/';
		//$pattern[]='/<.*?div>/';
		//$pattern='/更多相关\&gt\;\&gt\;\&gt\;.+/is';
		$i = 0;
		foreach ($list as $val) {
			$data['content'] = preg_replace($pattern, '', $val['content']);
			$data['content'] = preg_replace('/\<.*?div.*?>/', '<br><br>', $data['content']);
			M('Article')->where("article_id={$val['article_id']}")->save($data);
			//dump($out[0].$val['article_id']);
			//			if(preg_match($pattern,$val['content'],$out)){
			//				dump($out[0].$val['article_id']);
			//				$i++;
			//				}
		}
		echo $i;
	}
	function shuiyin() {



	}
	function newarea() {
		$cod = I('code');
		$nam = I('area_name');
		$cx  = I('chengxiang');
//		//添加省
		//		$code=array(11,12,13,14,15,21,22,23,31,32,33,34,35,36,37,41,42,43,44,45,46,50,51,52,53,54,61,62,63,64,65);
		//		$areaname=array('北京市','天津市','河北省','山西省','内蒙古自治区','辽宁省','吉林省','黑龙江省','上海市','江苏省','浙江省','安徽省','福建省','江西省','山东省','河南省','湖北省','湖南省','广东省','广西壮族自治区','海南省','重庆市','四川省','贵州省','云南省','西藏自治区','陕西省','甘肃省','青海省','宁夏回族自治区','新疆维吾尔自治区');
		//		$i=0;
		//		for($i=0;$i<count($code);$i++){
		//			M('newarea')->add(array('area_name'=>$areaname[$i],'code'=>$code[$i],'parent_id'=>0));
		//			}

		//添加市
		//		$code=explode(',',substr($cod,1));
		//		$code=array_unique($code);
		//		$code2=array();
		//		foreach($code as $val){
		//			$code2[]=$val;
		//			}
		//		$code=$code2;
		//		$areaname=explode(',',substr($nam,1));
		//
		//		$i=0;
		//		for($i=0;$i<count($code);$i++){
		//			$temarr=explode('/',$code[$i]);
		//			$data=M('newarea')->where("code='{$temarr[0]}'")->find();
		//			$result=M('newarea')->add(array('area_name'=>$areaname[$i],'code'=>$temarr[1],'parent_id'=>$data['area_id']));
		//			if(0<$result){
		//
		//				}else{
		//					di('失败');
		//					}
		//			}

//		//添加区
		//		$code=explode(',',substr($cod,1));
		//		$areaname=explode(',',substr($nam,1));
		//
		//		$i=0;
		//		for($i=0;$i<count($code);$i++){
		//			$temarr[0]=substr($code[$i],0,4);
		//			$temarr[1]=substr($code[$i],0,6);
		//			$data=M('newarea')->where("code='{$temarr[0]}'")->find();
		//			if(!empty($areaname[$i]) && !empty($temarr[1]) && !empty($data['area_id'])){
		//			$result=M('newarea')->add(array('area_name'=>$areaname[$i],'code'=>$temarr[1],'parent_id'=>$data['area_id']));
		//			if(0<$result){
		//
		//				}else{
		//					di('失败');
		//					}
		//			}
		//			}

		//添加街道
		$code     = explode(',', substr($cod, 1));
		$areaname = explode(',', substr($nam, 1));

		$i = 0;
		for ($i = 0; $i < count($code); $i++) {
			$temarr[0] = substr($code[$i], 0, 6);
			$temarr[1] = substr($code[$i], 0, 9);
			$data      = M('newarea')->where("code='{$temarr[0]}'")->find();
			if (!empty($areaname[$i]) && !empty($temarr[1]) && !empty($data['area_id'])) {
				$result = M('newarea')->add(array('area_name' => $areaname[$i], 'code' => $temarr[1], 'parent_id' => $data['area_id']));
				if (0 < $result) {

				} else {
					die('失败');
				}
			}
		}

//		//添加社区居委会
		//		$code=explode(',',substr($cod,1));
		//		$areaname=explode(',',substr($nam,1));
		//		$chengxiang=explode(',',substr($cx,1));
		//
		//		$i=0;
		//		for($i=0;$i<count($code);$i++){
		//			$temarr[0]=substr($code[$i],0,9);
		//			$temarr[1]=substr($code[$i],0,12);
		//			$data=M('newarea')->where("code='{$temarr[0]}'")->find();
		//			if(!empty($areaname[$i]) && !empty($temarr[1]) && !empty($data['area_id'])){
		//			$result=M('newarea')->add(array('chengxiang'=>$chengxiang[$i],'area_name'=>$areaname[$i],'code'=>$temarr[1],'parent_id'=>$data['area_id']));
		//			if(0<$result){
		//
		//				}else{
		//					die('失败');
		//					}
		//			}else{
		//				die('失败');
		//				}
		//			}

		echo $i . '成功';
		die();
	}
//处理文档少的分类
	function delshaocate() {
		$str  = '';
		$i    = 8;
		$list = M('Category')->where("category_type='article' and pid<>0")->select();
		foreach ($list as $val) {
			$count = M('Article')->where("category_id={$val['category_id']}")->count();
			if ($count < 100) {
				$list2 = M('Article')->where("category_id={$val['category_id']}")->select();
				foreach ($list2 as $k => $v) {
					M('Article')->where("article_id={$v['article_id']}")->save(array(
						'category_id' => 506,
						'position'    => $v['positioin'] . ',' . $i,
					));
				}
//				M('Article')->where("category_id={$val['category_id']}")->save(array(
				//													'category_id'=>84,
				//													'position'=>$i
				//													));
				M('Category')->where("category_id={$val['category_id']}")->delete();
				$str .= ',' . $i++ . ':' . preg_replace(array('/的说说/', '/说说/'), '', $val['title']);
			}
		}
		echo $str;
	}
	//缩略图测试
	function slt() {
		create_thumb('./test.jpg', './test_thumb.jpg', $width = 210, $height = 200);
	}
	function delcate() {
		$clist  = M('Category')->where("category_type='article'")->select();
		$temarr = array();
		foreach ($clist as $key => $val) {
			$temarr[$val['title']][] = $val['category_id'];
			foreach ($clist as $k => $v) {
				if (trim($val['title']) == trim($v['title']) && $val['category_id'] != $v['category_id']) {
					$temarr[$v['title']][] = $v['category_id'];
				}
			}
			$temarr[$val['title']] = array_unique($temarr[$val['title']]);

		}
		foreach ($temarr as $key => $val) {
			if (count($val) > 1) {
				$firstid = array_shift($val);
				$idstr   = implode(',', $val);
				M('Article')->where('category_id in(' . $idstr . ')')->save(array('category_id' => $firstid));
				M('Category')->where('category_id in(' . $idstr . ')')->delete();
			}
		}
	}
	function index() {
//1:首页
		//2:热点
		//3:图文
		//4:搞笑
		//5:伤感
		//6:个性
		//7:爱情
		//取出所有分类并自动归类
		$clist           = M('Category')->where("category_type='article'")->select();
		$data            = array();
		$data['title']   = toUtf8(I('title'));
		$data['content'] = toUtf8($_POST['content']);
		$patt            = array(
			'/([a-zA-z]+\.)+[a-zA-z]{2,3}/i', //去掉域名
		);
		$data['content'] = preg_replace($patt, '', $data['content']);
		//替换掉域名
		$data['position'] = '0';
		//保存编辑器中的图片
		preg_match_all('/<img.*?src=[\'|\"]{1}(.*?)[\'|\"]{1}.*?>/', $data['content'], $out, PREG_PATTERN_ORDER);
		$pic = 0;
		foreach ($out[1] as $val) {
			if (!empty($val)) {
				$thumbpath = str_replace('/image', '/image/thumb', $val);
				create_thumb('.' . $val, '.' . $thumbpath, C('THUMB_WIDTH'), C('THUMB_HEIGHT'));
				$this->markpic('.' . $val);
				$result = M('Picture')->add(array(
					'uid'         => 1,
					'path'        => $val,
					'thumbpath'   => $thumbpath,
					'create_time' => NOW_TIME,
					'status'      => 1,
				));
				if ($pic < 1 && 0 < $result) {
					$data['pic'] = $result;
					$pic++;
				}
			}
		}

		if ($pic > 0) {
			$data['position'] .= ',3';
		}

		//保存图片
		//		$data['pic']=I('pic');
		//		if(!empty($data['pic'])){
		//			$result=M('Picture')->add(array(
		//					'uid'=>1,
		//					'path'=>$data['pic'],
		//					'create_time'=>NOW_TIME,
		//					'status'=>1
		//			));
		//			if(0<$result)$data['pic']=$result;
		//}

		//查询分类
		$catetitle = toUtf8(I('fenlei'));
		//$catetitle=explode('>',$catetitle);
		//$catetitle=empty($catetitle[count($catetitle)-1])?$catetitle[count($catetitle)-2]:$catetitle[count($catetitle)-1];
		$catetitle = trim($catetitle);
		$sresult   = false;
		foreach ($clist as $val) {
			if (strpos(trim($catetitle), $val['title']) === false) {
				$sresult = false;
			} else {
				$sresult             = true;
				$data['category_id'] = $val['category_id'];
				break;
			}
		}
		if (!$sresult) {
			//die($data['category_id'].strlen($catetitle));
			//查找是不是四个字
			$rresu = M('Category')->add(array(
				'pid'           => 80,
				'title'         => $catetitle,
				'name'          => Pinyin($catetitle),
				'category_type' => 'article',
				'status'        => 1,
			));
			if (0 < $rresu) {
				$data['category_id'] = $rresu;
			} else {
				$data['category_id'] = 67;
			}
		}
		$data['create_time'] = NOW_TIME;
		$data['update_time'] = NOW_TIME;
		$data['status']      = 1;

		if (empty($data['title']) || empty($data['content'])) {
			die('发布失败');
		}

		//取文章的标记自动添加标记
		//$mark=get_model_attr('Article','extra');
		$data1   = M('ModelAttr')->field('extra')->find(11);
		$posiarr = $this->extra_to_array($data1['extra']);
		foreach ($posiarr as $key => $val) {
			//查询标题中如果跟标记文字有类似的就添加上标记
			if (strpos($data['title'], $val) === false) {

			} else {
				$data['position'] .= ',' . $key;
			}
		}
		$res = M('Article')->add($data);
		if (0 < $res) {
			echo '发布成功';
		} else {
			echo '发布失败';
		}

		die();
	}
	function extra_to_array($extra) {
		$extra = str_replace(' ', '', $extra);
		$jg    = "\n";
		if (strpos($extra, ',') !== false) {
			$jg = ',';
		}

		$dest = array();
		$tema = explode($jg, $extra);
		foreach ($tema as $val) {
			if (strpos($extra, ':') !== false) {
				$temb = explode(':', $val);
				if (count($temb) === 2) {
					$dest[$temb[0]] = $temb[1];
				}

			} else {
				$dest[$val] = $val;
			}
		}
		return $dest;
	}
	function clcon() {
		$list = M('Article')->select();
/*		$pattern[]='/([,|.|。|\?|!|？|！]{1})[\s|\r|\n|\t]*(\d+[．|、])/i';//匹配没有换行的情况
$pattern[]='/([,|.|。|\?|!|？|！]{1})[\s|\r|\n|\t]*<br\s*\/?>[\s|\r|\n|\t]*(\d+[．|、])/i';//匹配只有一个换行的情况
$pattern[]='/<div>[\s|\r|\n|\t]*(\d+[．|、][\s\S]*?)<\/div>/is';//换掉div外框*/
		//$pattern[]='/<\/?div.*\?\>/i';
		$pattern = array(); //去掉连续的换行
		// $pattern[]='/[\r|\n]+/i';
		foreach ($list as $val) {
			//$con=preg_replace($pattern,'$1<br><br>$2',$val['content']);
			$con = preg_replace('/(\/?<br.*?>\s*)+/i', '<br><br>', $val['content']);
			if ($val['article_id'] == '5576') {
				echo $con;
			}
			M('Article')->save(array(
				'article_id' => $val['article_id'],
				'content'    => $con,
			));

		}
		echo 'ok';
//		$info=M('Article')->find(5571);
		//		$info=preg_replace('/([\r|\n\s]+?)(\d+?、)/is','$1<br>$2',$info['content']);
		//		echo  $info;
	}
//图片添加水印
	private function markpic($dst='') {
		//取水印图片
		$src       = realpath('.' . get_picture(C('SHUIYIN_IMG')));
		$shuiyinon = C('SHUIYIN_ON');
		if ($shuiyinon == '1' && $dst !== false && $src !== false) {
			image_water($dst,$src,$dst);
		} else if ($shuiyinon == '2' && $dst !== false && $src !== false) {
			image_water($dst,'',$dst,C('SHUIYIN_TEXT'));
		}
	}
}