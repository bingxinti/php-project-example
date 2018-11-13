<?php
/**
 * 短信处理函数库文件
 * @package W2
 * @author axing
 * @since 1.0
 * @version 1.0
 */
class W2SMS {

	 /**
     * [sendNote 发送短信]
     * @param  string $strTelephone [手机号]
     * @param  string $strSendMsg   [短信内容]
     * @return [type]               [description]
     */
   //  public static function sendNote66($strTelephone = '', $strSendMsg = '')
   //  {
   //  	// return 1;
   //  	// $strTelephone = '15121032751';
   //      $sendState   = 0 ;
   //      $strSendMsg .= '';
   //      if (strlen($strTelephone) == 11 && !empty($strSendMsg) )
   //      {
			// $smsName    = CommonFun::loadCommonConfig('SMS_NAME');
			// $strSendMsg = "{$smsName} {$strSendMsg} 退订回N";
   //      	// http://111.1.1.146:8820/
			// $smsUser        = CommonFun::loadCommonConfig('SMS_USER_NAME');
			// $smsPassword    = CommonFun::loadCommonConfig('SMS_USER_PWD');
			// D($smsUser, $smsPassword, $smsName, $strSendMsg);
			// $sendState      = 1;
			// $strCurrentTime = date('mdHis',time());
			// $strPasswd      = md5("{$smsUser}{$smsPassword}{$strCurrentTime}");
			// $strSendMsg     = urlencode($strSendMsg);
			// $strUrl         = "http://111.1.1.146:8821/sendsms.aspx?user={$smsUser}&passwd={$strPasswd}&mobs={$strTelephone}&msg={$strSendMsg}&extno=99&ts={$strCurrentTime}";
			// D($strUrl);
			// exit;
			// $sendState      =  file_get_contents($strUrl);
			// $_REQUEST['sendState'] = $sendState;
   //          return $sendState;
   //      }
   //      return $sendState;
   //  }


    public static function sendNote($strTelephone = '', $strSendMsg = '')
    {
    	// return 1;
    	// $strTelephone = '15121032751';
        $sendState   = 0 ;
        $strSendMsg .= '';
        if (strlen($strTelephone) == 11 && !empty($strSendMsg) )
        {
			// $smsName    = CommonFun::loadCommonConfig('SMS_NAME');
			$strSendMsg = "{$strSendMsg}";
        	// http://www.shizhicom.cn/kh/main.aspx
			$smsUser        = CommonFun::loadCommonConfig('SMS_USER_NAME');
			$smsPassword    = CommonFun::loadCommonConfig('SMS_USER_PWD');
			// D($smsUser, $smsPassword, $strSendMsg);
			$sendState      = 1;
			$strCurrentTime = date('mdHis',time());
			$strPasswd      = strtoupper(md5("{$smsUser}{$smsPassword}{$strCurrentTime}"));
			$strPasswd      = strtoupper(md5("{$smsUser}{$smsPassword}"));
			$strSendMsg     = urlencode($strSendMsg);
			$strUrl         = "http://www.shizhicom.cn:8080/SendMessage/SendSms_API?user={$smsUser}&passwd={$strPasswd}&mobs={$strTelephone}&msg={$strSendMsg}&extno=99&ts={$strCurrentTime}";
			$sendState      =  file_get_contents($strUrl);
			$_REQUEST['sendState'] = $sendState;
            return $sendState;
        }
        return $sendState;
    }

	public static $SMS_USER;
	public static $SMS_PASSWD;

	public static $UCPASS_ACCOUNTSID;
	public static $UCPASS_TOKEN;
	public static $UCPASS_APPID;
	public static $UCPASS_TEMPLATEID;

	/**
	 * 发送短信，使用的是悦可短信平台
	 * @param  string $p_telephone 手机号码
	 * @param  string $p_msg       短信内容
	 * @return string              结果
	 */
    public static function sendMessage($p_telephone, $p_msg)
    {
    	die('暂停使用');
    	$_r = null ;
    	if (W2String::isTelephone($p_telephone))
    	{
			// $strSendMsg = $p_msg;
			// $strSendMsg = iconv('UTF-8', 'GBK', $strSendMsg);
			// $strSendMsg = urlencode($strSendMsg);
			// $_r = '测试期，暂不发送实际短信';
			// $strUrl ="http://125.208.9.42:8080/WS/Send.aspx?CorpID=btapp&Pwd=123456&Mobile={$strTelephone}&Content={$strSendMsg}";
			// $_r = W2Web::loadStringByUrl($strUrl);
			$data = array();
			$data['user'] = static::$SMS_USER;
			$data['passwd'] = static::$SMS_PASSWD;
			$data['msg'] = $p_msg;//短消息内容，UTF-8编码
			$data['mobs'] = $p_telephone;//手机号码，逗号分隔，个数最多100
			$data['ts'] = date('YmdHi',time());//计划发送时间,格式“yyyyMMddHHmm”，默认当前
			$data['dtype'] = 0;//响应数据格式;0,普通字串，1.XML格式，默认0

			$data['passwd'] = md5($data['user'].$data['passwd']);//MD532位加密用户名和API密码
			$_r = W2Web::loadStringByUrl('http://api5.nashikuai.cn/SendSms.aspx','post',$data);
			// DD($_r);
    	}
		return $_r;
    }
	/**
	* 获取短信余额
	* @return int 数量
	*/
	public static function GetBalance()
	{
		$user = static::$SMS_USER;
		$passwd = static::$SMS_PASSWD;
		$strPasswd = md5($user.$passwd);
		$strUrl = 'http://api5.nashikuai.cn/GetBalance.aspx?user='.$user.'&passwd='.$strPasswd.'&dtype=1';
		$results = file_get_contents($strUrl);
		$results = simplexml_load_string($results);
		if ($results->overqty)
		{
			return $results->overqty;
		}
	return false;
	}

	/**
	 * 发送短信，使用的是云之讯ucpaas.com
	 * @param  string $p_telephone 		  手机号码
	 * @param  string $p_verifyCode       验证码
	 * @return array              		  结果
	 */
    public static function sendVerifyCodeWithUcpaas($p_telephone, $p_verifyCode)
    {
    	$BaseUrl          = 'https://api.ucpaas.com/';
    	$SoftVersion      = '2014-06-30';
    	$accountSid       = static::$UCPASS_ACCOUNTSID;
    	$token            = static::$UCPASS_TOKEN;
    	$appId            = static::$UCPASS_APPID;
    	$templateId       = static::$UCPASS_TEMPLATEID;
    	$timestamp = date("YmdHis") + 7200;
    	$authorization = trim(base64_encode($accountSid . ":" . $timestamp));
    	$_r = null ;
    	if (W2String::isTelephone($p_telephone))
    	{
    		$sigParameter = strtoupper(md5($accountSid . $token . $timestamp));
	        $url = $BaseUrl . $SoftVersion . '/Accounts/' . $accountSid . '/Messages/templateSMS?sig=' . $sigParameter;
            $body_json = array('templateSMS'=>array(
                'appId'=>$appId,
                'templateId'=>$templateId,
                'to'=>$p_telephone,
                'param'=>$p_verifyCode
            ));
            $body = json_encode($body_json);

            $header = array(
                'Accept:' . 'application/json',
                'Content-Type:' . 'application/json' . ';charset=utf-8',
                'Authorization:' . $authorization,
            );

	        $_r = W2Web::loadStringByUrl($url,'post',$body,$header);
    	}
		return $_r;
    }
}


//静态类的静态变量的初始化不能使用宏，只能用这样的笨办法了。
if (W2SMS::$SMS_USER == null && defined('W2SMS_USER'))
{
	W2SMS::$SMS_USER      = W2SMS_USER;
	W2SMS::$SMS_PASSWD    = W2SMS_PASSWD;
}
if (W2SMS::$UCPASS_ACCOUNTSID == null && defined('W2SMS_UCPASS_ACCOUNTSID'))
{
	W2SMS::$UCPASS_ACCOUNTSID      = W2SMS_UCPASS_ACCOUNTSID;
	W2SMS::$UCPASS_TOKEN           = W2SMS_UCPASS_TOKEN;
	W2SMS::$UCPASS_APPID           = W2SMS_UCPASS_APPID;
	W2SMS::$UCPASS_TEMPLATEID      = W2SMS_UCPASS_TEMPLATEID;
}

