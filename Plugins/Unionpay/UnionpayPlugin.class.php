<?php
namespace  Plugins\Unionpay;
define('UNIONPAY_PATH',str_replace('\\','/',dirname(__FILE__)));
require_once pathA('/Plugins/Plugin.class.php');
class UnionpayPlugin extends \Plugins\Plugin{
	protected   $config=array(
            		'version'=>'1.0',
            	    'author'=>'qiaokeli',
            	    'name'=>'中国银联',
            	    'descr'=>'银联支付'
            	 );
	//钩子默认的调用方法
	public function run($a,$b){
	  $this->assign('a',$a);
	  $this->assign('b',$b);
	   $this->display('content');	
	}
	private function daoru(){

	}
	public function dopay($money=null,$order=null,$ordername=null){
	//取插件配置参数
		$conf=F('pluginunionpay');
		if(empty($conf)||APP_DEBUG){
	  		$data=M('Addons')->field('param')->where("mark='Unionpay'")->find();
			 $conf=json_decode($data['param'],true);
			F('pluginunionpay',$conf);
		}
		define('UNIONPAY_MEMBER_ID',$conf['MEMBER_ID']);
		include_once(UNIONPAY_PATH. '/lib/utf8/func/SDKConfig.php');
		include_once(UNIONPAY_PATH. '/lib/utf8/func/common.php');
		include_once(UNIONPAY_PATH. '/lib/utf8/func/PinBlock.php');
		include_once(UNIONPAY_PATH. '/lib/utf8/func/PublicEncrypte.php');
		include_once(UNIONPAY_PATH. '/lib/utf8/func/secureUtil.php');
		include_once(UNIONPAY_PATH. '/lib/utf8/func/httpClient.php');
		
		/**
		 *	以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己需要，按照技术文档编写。该代码仅供参考
		 */
		// 初始化日志
		//$log = new PhpLog ( SDK_LOG_FILE_PATH, "PRC", SDK_LOG_LEVEL );
		//$log->LogInfo ( "============处理前台请求开始===============" );
		// 初始化日志
		$params = array(
				'version' => '5.0.0',				//版本号
				'encoding' => 'utf-8',				//编码方式
				'certId' => getSignCertId (),			//证书ID
				'txnType' => '01',				//交易类型	
				'txnSubType' => '01',				//交易子类 01：预授权、03：担保消费
				'bizType' => '000201',				//业务类型
				'frontUrl' =>  SDK_FRONT_NOTIFY_URL,  		//前台通知地址
				'backUrl' => SDK_BACK_NOTIFY_URL,		//后台通知地址	
				'signMethod' => '01',		//签名方法
				'channelType' => '08',		//渠道类型，07-PC，08-手机
				'accessType' => '0',		//接入类型
				'merId' =>MEMBER_ID,	//商户代码，请改自己的测试商户号
				'orderId' => date('YmdHis'),	//商户订单号，8-40位数字字母
				'txnTime' => date('YmdHis'),	//订单发送时间
				'txnAmt' => '100',		//交易金额，单位分
				'currencyCode' => '156',	//交易币种
				'orderDesc' => '订单描述',  //订单描述，可不上送，上送时控件中会显示该信息
				'reqReserved' =>' 透传信息', //请求方保留域，透传字段，查询、通知、对账文件中均会原样出现
				);

		// 签名
		sign ( $params );
		// 前台请求地址
		$front_uri = SDK_FRONT_TRANS_URL;
		$html_form = create_html ( $params, $front_uri );
		echo $html_form;
		die();
	}
	private function yanzheng(){
		include_once(UNIONPAY_PATH. '/lib/utf8/func/SDKConfig.php');
		include_once(UNIONPAY_PATH. '/lib/utf8/func/common.php');
		include_once(UNIONPAY_PATH. '/lib/utf8/func/PinBlock.php');
		include_once(UNIONPAY_PATH. '/lib/utf8/func/PublicEncrypte.php');
		include_once(UNIONPAY_PATH. '/lib/utf8/func/secureUtil.php');
		include_once(UNIONPAY_PATH. '/lib/utf8/func/httpClient.php');
		if (isset ( $_POST ['signature'] )) {
			return verify ( $_POST );
		}else{
			return false;
		}
		
	}
	public  function return_url(){
		if($this->yanzheng()){
			
			$orderId = $_POST ['orderId']; 
			$data=array(
				'status'=>1,
				'str'=>'验签成功',
				'mark'=>'中国银联',
				'order_sn'=>$orderId
				);
			return $data;
		}else{
			return array(
					'status'=>0,
					'mark'=>'中国银联',
					'str'=>'验签失败'
					);
		}
		//银联通知
		$str='';
/*		foreach ( $_POST as $key => $val ) {
			$str.= isset($mpi_arr[$key]) ?$mpi_arr[$key] : $key .'<br>';
			$str.= $val;
		}*/
		return $str;
	}
	public function notify_url(){
	if($this->yanzheng()){
		$orderId = $_POST ['orderId']; 
		$info=M('Order')->where("order_sn=$orderId")->setField('order_status',1);
	}
	}
	public function getConfig(){
		return $this->config;
	}
    public  function install(){
    	//向后台添加菜单，如果不添加的话直接返回真
      $data=array(
      	 'title'=>'中国银联',//插件后台菜单名字
         'pid'=>ADDONS_MENU,//不用改变
         'url'=>'Addons/plugin?pn=Unionpay&pm=set',//填写后台菜单url名称和方法
         'group'=>'已装插件',//不用改变
         'type'=>'Unionpay'    //填写自己的插件名字
      );
      //添加到数据库
       if(M('Menu')->add($data)){
       	return true;
       }else{
       	return false;
       }
	}
	public function uninstall(){
	//删除后台添加的菜单，如果没有直接返回真
	$map['type']='Unionpay'; 
	  if(M('Menu')->where($map)->delete()){
	  	return true;
	  }else{
	  	return false;
	  }
	}
	public function tijiao(){
	$this->success('提交成功');
	}
	public function set(){
		$this->meta_title='支付宝设置';
		//插件工菜单后台设置,没有的话直接返回真
	    if(IS_POST){
	      $data=array(
	      	'MEMBER_ID'=>I('post.MEMBER_ID')	//商户id
	      );
 	     $model=M('Addons');
        $result= $model->where("mark='Unionpay'")->save(array('param'=>json_encode($data)));	  	
			if(0<$result){
				$this->success('保存成功');	
			}else{
				$this->error('保存失败');			
			}	    	
	    	}else{
	    	    $data=M('Addons')->field('param')->where("mark='Unionpay'")->find();
	  		   $this->assign('info',json_decode($data['param'],true));
	  			  $str=$this->fetch('config');
     		   return $str;
	    }
	}
}

	class PhpLog
	{
		const DEBUG = 1;// Most Verbose
		const INFO = 2;// ...
		const WARN = 3;// ...
		const ERROR = 4;// ...
		const FATAL = 5;// Least Verbose
		const OFF = 6;// Nothing at all.
		 
		const LOG_OPEN = 1;
		const OPEN_FAILED = 2;
		const LOG_CLOSED = 3;
		 
		/* Public members: Not so much of an example of encapsulation, but that's okay. */
		public $Log_Status = PhpLog::LOG_CLOSED;
		public $DateFormat= "Y-m-d G:i:s";
		public $MessageQueue;
		 
		private $filename;
		private $log_file;
		private $priority = PhpLog::INFO;
		 
		private $file_handle;
		
		/**
		 * AUTHOR:	gu_yongkang
		 * DATA:	20110322
		 * Enter description here ...
		 * @param $filepath
		 * 文件存储的路径
		 * @param $timezone
		 * 时间格式，此处设置为"PRC"（中国）
		 * @param $priority
		 * 设置运行级别
		 */
		 
		public function __construct( $filepath, $timezone, $priority )
		{
			if ( $priority == PhpLog::OFF ) return;
			 
			$this->filename = date('Y-m-d', time()) . '.log';	//默认为以时间＋.log的文件文件
			$this->log_file = $this->createPath($filepath, $this->filename);
			$this->MessageQueue = array();
			$this->priority = $priority;
			date_default_timezone_set($timezone);
			 
			if ( !file_exists($filepath) )	//判断文件路径是否存在
			{
				if(!empty($filepath))	//判断路径是否为空
				{
					if(!($this->_createDir($filepath)))
					{
						die("创建目录失败!");
					}
					if ( !is_writable($this->log_file) )
					{
					$this->Log_Status = PhpLog::OPEN_FAILED;
					$this->MessageQueue[] = "The file exists, but could not be opened for writing. Check that appropriate permissions have been set.";
					return;
					}
				}
			}
		 
			if ( $this->file_handle = fopen( $this->log_file , "a+" ) )
			{
				$this->Log_Status = PhpLog::LOG_OPEN;
				$this->MessageQueue[] = "The log file was opened successfully.";
			}
			else
			{
				$this->Log_Status = PhpLog::OPEN_FAILED;
				$this->MessageQueue[] = "The file could not be opened. Check permissions.";
			}
			return;
		}
		 
		public function __destruct()
		{
			if ( $this->file_handle )
			fclose( $this->file_handle );
		}
		
		/**
	     *作用:创建目录
	     *输入:要创建的目录
	     *输出:true | false
	     */
		private  function _createDir($dir)
		{
			return is_dir($dir) or (self::_createDir(dirname($dir)) and mkdir($dir, 0777));
		}
		
		/**
	     *作用:构建路径
	     *输入:文件的路径,要写入的文件名
	     *输出:构建好的路径字串
	     */
		private function createPath($dir, $filename)
		{
			if (empty($dir)) 
			{
				return $filename;
			} 
			else 
			{
				return $dir . "/" . $filename;
			}
		}
		 
		public function LogInfo($line)
		{
			/**
			 * AUTHOR : gu_yongkang
			 * 增加打印函数和文件名的功能
			 */
			$sAarray = array();
			$sAarray = debug_backtrace();
			$sGetFilePath = $sAarray[0]["file"];
			$sGetFileLine = $sAarray[0]["line"];
			$this->Log( $line, PhpLog::INFO, $sGetFilePath, $sGetFileLine);
			unset($sAarray);
			unset($sGetFilePath);
			unset($sGetFileLine);
		}
		 
		public function LogDebug($line)
		{
			/**
			 * AUTHOR : gu_yongkang
			 * 增加打印函数和文件名的功能
			 */
			$sAarray = array();
			$sAarray = debug_backtrace();
			$sGetFilePath = $sAarray[0]["file"];
			$sGetFileLine = $sAarray[0]["line"];
			$this->Log( $line, PhpLog::DEBUG, $sGetFilePath, $sGetFileLine);
			unset($sAarray);
			unset($sGetFilePath);
			unset($sGetFileLine);
		}
		 
		public function LogWarn($line)
		{
			/**
			 * AUTHOR : gu_yongkang
			 * 增加打印函数和文件名的功能
			 */
			$sAarray = array();
			$sAarray = debug_backtrace();
			$sGetFilePath = $sAarray[0]["file"];
			$sGetFileLine = $sAarray[0]["line"];
			$this->Log( $line, PhpLog::WARN, $sGetFilePath, $sGetFileLine);
			unset($sAarray);
			unset($sGetFilePath);
			unset($sGetFileLine);
		}
		 
		public function LogError($line)
		{
			/**
			 * AUTHOR : gu_yongkang
			 * 增加打印函数和文件名的功能
			 */
			$sAarray = array();
			$sAarray = debug_backtrace();
			$sGetFilePath = $sAarray[0]["file"];
			$sGetFileLine = $sAarray[0]["line"];
			$this->Log( $line, PhpLog::ERROR, $sGetFilePath, $sGetFileLine);
			unset($sAarray);
			unset($sGetFilePath);
			unset($sGetFileLine);
		}
		 
		public function LogFatal($line)
		{
			/**
			 * AUTHOR : gu_yongkang
			 * 增加打印函数和文件名的功能
			 */
			$sAarray = array();
			$sAarray = debug_backtrace();
			$sGetFilePath = $sAarray[0]["file"];
			$sGetFileLine = $sAarray[0]["line"];
			$this->Log( $line, PhpLog::FATAL, $sGetFilePath, $sGetFileLine);
			unset($sAarray);
			unset($sGetFilePath);
			unset($sGetFileLine);
		}

		/**
		 * Author ： gu_yongkang
		 * Enter description here ...
		 * @param unknown_type $line
		 * content 内容
		 * @param unknown_type $priority
		 * 打印级别
		 * @param unknown_type $sFile
		 * 调用打印日志的文件名
		 * @param unknown_type $iLine
		 * 打印文件的位置（行数）
		 */
		public function Log($line, $priority, $sFile, $iLine)
		{
			if ($iLine > 0)
			{
				//$line = iconv('GBK', 'UTF-8', $line);
				if ( $this->priority <= $priority )
				{
					$status = $this->getTimeLine( $priority, $sFile, $iLine);
					$this->WriteFreeFormLine ( "$status $line \n" );
				}
			}
			else 
			{
				/**
				 * AUTHOR : gu_yongkang
				 * 增加打印函数和文件名的功能
				 */
				$sAarray = array();
				$sAarray = debug_backtrace();
				$sGetFilePath = $sAarray[0]["file"];
				$sGetFileLine = $sAarray[0]["line"];
				if ( $this->priority <= $priority )
				{
					$status = $this->getTimeLine( $priority, $sGetFilePath, $sGetFileLine);
					unset($sAarray);
					unset($sGetFilePath);
					unset($sGetFileLine);
					$this->WriteFreeFormLine ( "$status $line \n" );
				}
			}
		}
		 // 支持输入多个参数
		public function WriteFreeFormLine( $line )
		{
			if ( $this->Log_Status == PhpLog::LOG_OPEN && $this->priority != PhpLog::OFF )
			{
				if (fwrite( $this->file_handle , $line ) === false) 
				{
					$this->MessageQueue[] = "The file could not be written to. Check that appropriate permissions have been set.";
				}
			}
		}
		private function getRemoteIP()
		{
			foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key)
			{
				if (array_key_exists($key, $_SERVER) === true)
				{
					foreach (explode(',', $_SERVER[$key]) as $ip)
					{
						$ip = trim($ip);
						if (!empty($ip))
						{
							return $ip;
						}
					}
				}
			}
			return "_NO_IP";
		}
		 
		private function getTimeLine( $level, $FilePath, $FileLine)
		{			
			$time = date( $this->DateFormat );
			$ip = $this->getRemoteIP();
			switch( $level )
			{
				case PhpLog::INFO:
				return "$time, " . "INFO, " . "$ip, " . "File[ $FilePath ], " . "Line[$FileLine]" . "------";
				case PhpLog::WARN:
				return "$time, " . "WARN, " . "$ip, " . "File[ $FilePath ], " . "Line[$FileLine]" . "------";
				case PhpLog::DEBUG:
				return "$time, " . "DEBUG, " . "$ip, " . "File[ $FilePath ], " . "Line[$FileLine]" . "------";
				case PhpLog::ERROR:
				return "$time, " . "ERROR, " . "$ip, " . "File[ $FilePath ], " . "Line[$FileLine]" . "------";
				case PhpLog::FATAL:
				return "$time, " . "FATAL, " . "$ip, " . "File[ $FilePath ], " . "Line[$FileLine]" . "------";
				default:
				return "$time, " . "LOG, " . "$ip, " . "File[ $FilePath ], " . "Line[$FileLine]" . "------";
			}
		}
	}
