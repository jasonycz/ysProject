<?php
set_time_limit(0);

header("Content-Type: text/html; charset=GBK");
	
/**
 * 定义程序绝对路径
 */
define('SCRIPT_ROOT',  dirname(__FILE__).'/');
require_once SCRIPT_ROOT.'include/Client.php';
class SmsSend
{
		/**
	 * 网关地址
	 */	
	public $gwUrl;


	/**
	 * 序列号,请通过亿美销售人员获取
	 */
	public $serialNumber;

	/**
	 * 密码,请通过亿美销售人员获取
	 */
	public $password;

	/**
	 * 登录后所持有的SESSION KEY，即可通过login方法时创建
	 */
	public $sessionKey;

	/**
	 * 连接超时时间，单位为秒
	 */
	public $connectTimeOut;

	/**
	 * 远程信息读取超时时间，单位为秒
	 */ 
	public $readTimeOut;

	public $proxyhost;
	public $proxyport ;
	public $proxyusername;
	public $proxypassword; 

	public $client;
	/**
	 * 发送向服务端的编码，如果本页面的编码为GBK，请使用GBK
	 */
	function  __construct($name,$age){
		$this->gwUrl = 'http://sdk4report.eucp.b2m.cn:8080/sdk/SDKService';
		$this->serialNumber = '0SDK-EMY-0130-XXXXX';
		$this->password = '123456';
		$this->sessionKey = '123456';
		$this->connectTimeOut = 2;
		$this->readTimeOut = 10;
		$this->proxyhost = false;
		$this->proxyport = false;
		$this->proxyusername = false;
		$this->proxypassword = false;
		$this->client = new Client($this->gwUrl,$this->serialNumber,$this->password,$this->sessionKey,$this->proxyhost,$this->proxyport,$this->proxyusername,$this->proxypassword,$this->connectTimeOut,$readTimeOut);
		$this->client->setOutgoingEncoding("GBK");
	}
	/**
	 * 企业注册 用例
 	*/
	function registDetailInfo()
	{
		
		$eName = "xx公司";
		$linkMan = "陈xx";
		$phoneNum = "010-1111111";
		$mobile = "159xxxxxxxx";
		$email = "xx@yy.com";
		$fax = "010-1111111";
		$address = "xx路";
		$postcode = "111111";
		
		/**
		 * 企业注册  [邮政编码]长度为6 其它参数长度为20以内
		 * 
		 * @param string $eName 	企业名称
		 * @param string $linkMan 	联系人姓名
		 * @param string $phoneNum 	联系电话
		 * @param string $mobile 	联系手机号码
		 * @param string $email 	联系电子邮件
		 * @param string $fax 		传真号码
		 * @param string $address 	联系地址
		 * @param string $postcode  邮政编码
		 * 
		 * @return int 操作结果状态码
		 * 
		 */
		$statusCode = $this->client->registDetailInfo($eName,$linkMan,$phoneNum,$mobile,$email,$fax,$address,$postcode);
		echo "处理状态码:".$statusCode;
	
	}
	/**
 * 登录 用例
 */
	function login()
	{
		
		/**
		 * 下面的操作是产生随机6位数 session key
		 * 注意: 如果要更换新的session key，则必须要求先成功执行 logout(注销操作)后才能更换
		 * 我们建议 sesson key不用常变
		 */
		//$sessionKey = $client->generateKey();
		//$statusCode = $client->login($sessionKey);
		
		$statusCode = $this->client->login();
		
		echo "处理状态码:".$statusCode."<br/>";
		if ($statusCode!=null && $statusCode=="0")
		{
			//登录成功，并且做保存 $sessionKey 的操作，用于以后相关操作的使用
			echo "登录成功, session key:".$this->client->getSessionKey()."<br/>";
		}else{
			//登录失败处理
			echo "登录失败,返回:".$statusCode;
		}
		 
	}
	/**
	 * 短信发送 用例
	 */
	function sendSMS($phone,$message)
	{
		/**
		 * 下面的代码将发送内容为 test 给 159xxxxxxxx 和 159xxxxxxxx
		 * $client->sendSMS还有更多可用参数，请参考 Client.php
		 */
		$statusCode = $this->client->sendSMS(array($phone),$message);
		echo "处理状态码:".$statusCode;
	}
}
?>