<?php
//数组分页类****************
//http://zhaokeli.com
//author:keli
//使用方法
//对象实例化后会自动从url中取页码，url中生成的页码值为"pg"
//***********$page=new mypage($arr,3,5); 初始化分页对象，参数为要分页的 (1)记录数组，(2)要显视的页码，(3)每个页面显视的记录数
//***********$page->cur_page_data;      初始化对象后可真接使用，这个变量就是你要显视的那一页的数据
//***********$page->getmypageinfo(); 返回分页类中的各个分页信息，为一个数组
//***********$page->showpage($shuchu)   输出导航参数$shuchu 控制导航是输出还是返回true 输出 false 返回
namespace Ainiku;
class Arrpage {
	public $total_arr; //总记录数组
	public $cur_page_data; //返回的当前页的数组
	public $page_num; //分成的总页数
	public $page_size; //每页的记录数
	public $total_size; //总记录数
	public $page; //当前的页码
	private $urlgz   = 0; //对应分类url中的规则
	private $ys_page = 2; //分类页导航显示数量
	private $urlarr  = array(
		0 => '&pg=',
		1 => './pg/',
	);
	protected $config = array(
		'total'    => '条记录',
		'prev'     => '上一页',
		'next'     => '下一页',
		'first'    => '首页',
		'last'     => '尾页',
		'prevurl'  => '',
		'nexturl'  => '',
		'firsturl' => '',
		'lasturl'  => '',
		'navstyle' => '<style>.pagenav{ text-align:center;}.pagenav a,.pagenav span{ font-size:12px; display:inline-block;text-decoration:none; color:#000; margin:0px 5px;}.pagenav a{height:20px; line-height:20px;border: 1px solid #E7ECF0; padding:0px 5px; margin:0px 5px;}.pagenav a.numnav{height:20px;width:20px; padding:0px;}.pagenav a:hover{ background:#ebebeb;}.pagenav strong{ display:inline-block;height:20px; width:20px; line-height:20px;}</style>',
	);
	// 默认分页变量名
	/**
	 *@param $total_arr 数据总数组
	 *@page  $page 要显示哪一页的数据，默认为空，从url中取数据，在后面调用$page->cur_page_data显示数据
	 *@page_size $page_size 每页的数据总数
	 *@param  $urlgz url生成的规则对应成员变量url规则数组0为 ./?_page=1   1为./_page/1
	 */
	function __construct($total_arr, $page = '', $page_size = 10, $urlgz = 0) {
		$this->urlgz      = $urlgz;
		$this->total_arr  = $total_arr;
		$this->total_size = count($total_arr);
		$this->page_size  = $page_size < 10 ? 10 : $page_size;
		$this->page_num   = $this->getPageNum();

		$url_page = I('pg'); //从url中取请求页
		$url_page = $url_page == '' ? 1 : $url_page;
		$page     = $page == '' ? $url_page : $page;

		$this->page = $this->getpage($page);
		//初始化分页导航url
		$this->config['prevurl']  = U('', array('pg' => $this->page - 1)); //$this->urlarr[$this->urlgz].($this->page-1);
		$this->config['nexturl']  = U('', array('pg' => $this->page + 1)); //$this->urlarr[$this->urlgz].($this->page+1);
		$this->config['firsturl'] = U('', array('pg' => 1)); //$this->urlarr[$this->urlgz].'1';
		$this->config['lasturl']  = U('', array('pg' => $this->page_num));
		$this->cur_pagedata();
	}
//返回分成的总页数
	private function getPageNum() {
		return ((int) ceil($this->total_size / $this->page_size));
	}
//判断传第过来的页码是否合格
	private function getpage($page) {
		if ($page < 1) {return 1;} elseif ($page > $this->page_num) {return $this->page_num;} else {return $page;}
	}
//取出指定的数据
	public function cur_pagedata() {
		$this->cur_page_data = array_slice($this->total_arr, ($this->page - 1) * $this->page_size, $this->page_size);
		return $this->cur_page_data;
	}
//返回对类初始化后的信息
	public function getmypageinfo() {
		$mypageinfo = array(
			"total_size"    => $this->total_size,
			"total_arr"     => $this->total_arr,
			"cur_page_data" => $this->cur_page_data,
			"page_num"      => $this->page_num,
			"page_size"     => $this->page_size,
			"page"          => $this->page,
		);
		return $mypageinfo;
	}
/**
 *功能：显示分页导航条
 *@param $shuchu 如果为true则输出，false则返回。默认为输出
 */
	public function showpage($shuchu = true) {
		//输出延伸导航页
		$ys_str = '';
		//输出上几页
		for ($i = 1; $i < $this->ys_page; $i++) {
			$a = ($this->page) - $i;
			if ($a < 1 or $a > $this->page_num) {
				break;
			}

			$ys_str = "<a class='numnav' href='" . U('', array('pg' => $a)) . "'>" . $a . "</a>" . $ys_str;
			if ($i + 1 == $this->ys_page and $a > 1) {
				$ys_str = "<span>...</span>" . $ys_str;
			}

		}
		$ys_str .= "<span>第" . $this->page . "页</span>";
		//输出下几页
		for ($i = 1; $i < $this->ys_page; $i++) {
			$a = ($this->page) + $i;
			if ($a < 1 or $a > $this->page_num) {
				break;
			}

			$ys_str .= "<a class='numnav' href='" . U('', array('pg' => $a)) . "'>" . $a . "</a>";
			if ($i + 1 == $this->ys_page and $a < $this->page_num) {
				$ys_str .= "<span>...</span>";
			}

		}
		$pagestring = "<div class='page'><a href='" . $this->config['firsturl'] . "'>" .
		$this->config['first'] . "</a><a href='" . $this->config['prevurl'] . "'>" .
		$this->config['prev'] . "</a>" .
		$ys_str .
		//"<span>第".$this->page."页</span>".
		"<a href='" . $this->config['nexturl'] . "'>" .
		$this->config['next'] . "</a><a href='" . $this->config['lasturl'] . "'>" .
		$this->config['last'] . "</a><span>共" .
		$this->total_size . $this->config['total'] . "</span></div>";
		if ($shuchu) {
			echo $this->config['navstyle'] . $pagestring;
		} else {
			return $this->config['navstyle'] . $pagestring;
		}
	}
}
?>