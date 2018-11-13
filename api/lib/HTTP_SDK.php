<?php
//$test = HTTP_SDK::getInstance($username,$password);
class HTTP_SDK {
	private $_rpcClient = null;
	private $cpId = "";
	private $cpPwd = "";
	private $server = "";
	/**
	 *
	 * @var SMS_SDK
	 */
	private static $_self = null;
	public static function getInstance($cpId, $cpPwd, $server = "http://api.itrigo.net") {
		if (null == self::$_self) {
			self::$_self = new HTTP_SDK ( $cpId, $cpPwd, $server );
		}

		return self::$_self;
	}
	private function __construct($cpId, $cpPwd, $server) {
		$this->cpId = $cpId;
		$this->cpPwd = $cpPwd;
		$this->server = $server;
	}
	public function pushMt($phone,$spnumber,$content,$extend) {
		$content=iconv("utf-8","gbk",$content);//这里需要转换成gbk
		$content=urlencode($content);
		$url = $this->server ."/mt.jsp?cpName={$this->cpId}&cpPwd={$this->cpPwd}&phones={$phone}&spCode={$spnumber}&msg={$content}&extNum={$extend}";
		return $this->request($url);
	}

	private function request($url)
	{
		return file_get_contents($url);
	}

	public function pushMts($phone,$content) {
		$content=iconv("utf-8","gbk",$content);//这里需要转换成gbk
		$content=urlencode($content);
		$url = $this->server ."/mt.jsp?cpName={$this->cpId}&cpPwd={$this->cpPwd}&phones={$phone}&msg={$content}";
		return $this->request($url);
	}
}


