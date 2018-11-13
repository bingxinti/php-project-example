<?php
/**
 * IM 环信即时通讯云领导者
 * 来自 Easemob 类
 * @link(examples, https://github.com/easemob/emchat-server-examples)
 * @author [Demon] <508037051@qq.com>
 */
class W2IM
{
	/** @var [type] [description] */
	protected $client_id;
	protected $client_secret;
	protected $org_name;
	protected $app_name;
	protected $url;

	/**
	 * 初始化参数
	 *
	 * @param array $options
	 * @param $options['client_id']
	 * @param $options['client_secret']
	 * @param $options['org_name']
	 * @param $options['app_name']
	 */
	public function __construct($options) {
		$this->client_id = isset ( $options ['client_id'] ) ? $options ['client_id'] : '';
		$this->client_secret = isset ( $options ['client_secret'] ) ? $options ['client_secret'] : '';
		$this->org_name = isset ( $options ['org_name'] ) ? $options ['org_name'] : '';
		$this->app_name = isset ( $options ['app_name'] ) ? $options ['app_name'] : '';
		if (! empty ( $this->org_name ) && ! empty ( $this->app_name )) {
			$this->url = 'https://a1.easemob.com/' . $this->org_name . '/' . $this->app_name . '/';
		}
	}
	/**
	 * 开放注册模式
	 *
	 * @param $options['username'] 用户名
	 * @param $options['password'] 密码
	 */
	public function openRegister($options) {
		$url = $this->url . "users";
		$result = $this->postCurl ( $url, $options, $head = 0 );
		return $result;
	}

	/**
	 * 授权注册模式 || 批量注册
	 *
	 * @param $options['username'] 用户名
	 * @param $options['password'] 密码
	 *        	批量注册传二维数组
	 */
	public function accreditRegister($options) {

		$url = $this->url . "users";
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		// D( $url, $options, $header);
		$result = $this->postCurl ( $url, $options, $header );
		return $result;
	}

	/**
	 * 获取指定用户详情
	 *
	 * @param $username 用户名
	 */
	public function userDetails($username) {
		$url = $this->url . "users/" . $username;
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = 'GET' );
		return $result;
	}

	/**
	 * 重置用户密码
	 *
	 * @param $options['username'] 用户名
	 * @param $options['password'] 密码
	 * @param $options['newpassword'] 新密码
	 */
	public function editPassword($options) {
		$url = $this->url . "users/" . $options ['username'] . "/password";
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, $options, $header, $type = 'PUT');
		return $result;
	}
	/**
	 * 删除用户
	 *
	 * @param $username 用户名
	 */
	public function deleteUser($username) {
		$url = $this->url . "users/" . $username;
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = 'DELETE' );
	}

	/**
	 * 批量删除用户
	 * 描述：删除某个app下指定数量的环信账号。上述url可一次删除300个用户,数值可以修改 建议这个数值在100-500之间，不要过大
	 *
	 * @param $limit="300" 默认为300条
	 * @param $ql 删除条件
	 *        	如ql=order+by+created+desc 按照创建时间来排序(降序)
	 */
	public function batchDeleteUser($limit = "300", $ql = '') {
		$url = $this->url . "users?limit=" . $limit;
		if (! empty ( $ql )) {
			$url = $this->url . "users?ql=" . $ql . "&limit=" . $limit;
		}
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = 'DELETE' );
	}

	/**
	 * 给一个用户添加一个好友
	 *
	 * @param
	 *        	$owner_username
	 * @param
	 *        	$friend_username
	 */
	public function addFriend($owner_username, $friend_username) {
		$url = $this->url . "users/" . $owner_username . "/contacts/users/" . $friend_username;
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, '', $header );
	}
	/**
	 * 删除好友
	 *
	 * @param
	 *        	$owner_username
	 * @param
	 *        	$friend_username
	 */
	public function deleteFriend($owner_username, $friend_username) {
		$url = $this->url . "users/" . $owner_username . "/contacts/users/" . $friend_username;
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = "DELETE" );
	}
	/**
	 * 查看用户的好友
	 *
	 * @param
	 *        	$owner_username
	 */
	public function showFriend($owner_username) {
		$url = $this->url . "users/" . $owner_username . "/contacts/users/";
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = "GET" );
	}
	// +----------------------------------------------------------------------
	// | 聊天相关的方法
	// +----------------------------------------------------------------------
	/**
	 * 查看用户是否在线
	 *
	 * @param
	 *        	$username
	 */
	public function isOnline($username) {
		$url = $this->url . "users/" . $username . "/status";
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = "GET" );
		return $result;
	}
	/**
	 * 发送消息
	 *
	 * @param string $from_user
	 *        	发送方用户名
	 * @param array $username
	 *        	array('1','2')
	 * @param string $target_type
	 *        	默认为：users 描述：给一个或者多个用户(users)或者群组发送消息(chatgroups)
	 * @param string $content
	 * @param array $ext
	 *        	自定义参数
	 */
	public function yy_hxSend($from_user = "admin", $username, $content, $target_type = "users", $ext = array()) {
		// $_REQUEST[__FUNCTION__] =  func_get_args() ;
		$_REQUEST['信鸽透传的参数 ' . __FUNCTION__][] =  func_get_args() ;
		$option ['target_type'] = $target_type;
		$option ['target'] = $username;
		$params ['type'] = "txt";
		$params ['msg'] = $content;
		$option ['msg'] = $params;
		$option ['from'] = $from_user;

		// $ext= ['test' => '123'];
		if(!empty($ext))
		{
			$option ['ext'] = $ext;
		}
		$url = $this->url . "messages";
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		// D($option);
		$result = $this->postCurl ( $url, $option, $header );
		return $result;
	}
	/**
	 * 获取app中所有的群组
	 */
	public function chatGroups() {
		$url = $this->url . "chatgroups";
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = "GET" );
		return $result;
	}
	/**
	 * 创建群组
	 *
	 * @param $option['groupname'] //群组名称,
	 *        	此属性为必须的
	 * @param $option['desc'] //群组描述,
	 *        	此属性为必须的
	 * @param $option['public'] //是否是公开群,
	 *        	此属性为必须的 true or false
	 * @param $option['approval'] //加入公开群是否需要批准,
	 *        	没有这个属性的话默认是true, 此属性为可选的
	 * @param $option['owner'] //群组的管理员,
	 *        	此属性为必须的
	 * @param $option['members'] //群组成员,此属性为可选的
	 */
	public function createGroups($option) {
		$url = $this->url . "chatgroups";
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, $option, $header );
		return $result;
	}
	/**
	 * 获取群组详情
	 *
	 * @param
	 *        	$group_id
	 */
	public function chatGroupsDetails($group_id) {
		$url = $this->url . "chatgroups/" . $group_id;
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = "GET" );
		return $result;
	}
	/**
	 * 删除群组
	 *
	 * @param
	 *        	$group_id
	 */
	public function deleteGroups($group_id) {
		$url = $this->url . "chatgroups/" . $group_id;
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = "DELETE" );
		return $result;
	}
	/**
	 * 获取群组成员
	 *
	 * @param
	 *        	$group_id
	 */
	public function groupsUser($group_id) {
		$url = $this->url . "chatgroups/" . $group_id . "/users";
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = "GET" );
		return $result;
	}
	/**
	 * 群组添加成员
	 *
	 * @param
	 *        	$group_id
	 * @param
	 *        	$username
	 */
	public function addGroupsUser($group_id, $username) {
		$url = $this->url . "chatgroups/" . $group_id . "/users/" . $username;
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = "POST" );
		return $result;
	}
	/**
	 * 群组删除成员
	 *
	 * @param
	 *        	$group_id
	 * @param
	 *        	$username
	 */
	public function delGroupsUser($group_id, $username) {
		$url = $this->url . "chatgroups/" . $group_id . "/users/" . $username;
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = "DELETE" );
		return $result;
	}
	/**
	 * 聊天消息记录
	 *
	 * @param $ql 查询条件如：$ql
	 *        	= "select+*+where+from='" . $uid . "'+or+to='". $uid ."'+order+by+timestamp+desc&limit=" . $limit . $cursor;
	 *        	默认为order by timestamp desc
	 * @param $cursor 分页参数
	 *        	默认为空
	 * @param $limit 条数
	 *        	默认20
	 */
	public function chatRecord($ql = '', $cursor = '', $limit = 20) {
		$ql = ! empty ( $ql ) ? "ql=" . $ql : "order+by+timestamp+desc";
		$cursor = ! empty ( $cursor ) ? "&cursor=" . $cursor : '';
		$url = $this->url . "chatmessages?" . $ql . "&limit=" . $limit . $cursor;
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = "GET " );
		return $result;
	}

	// 导出聊天记录-------不分页
	// public function getChatRecord($ql){
	// 	$GLOBALS['base_url'] = $this->url;
	// 	if(!empty($ql)){
	// 		$url=$GLOBALS['base_url'].'chatmessages?ql='.$ql;
	// 	}else{
	// 		$url=$GLOBALS['base_url'].'chatmessages';
	// 	}
	// 	$header=array($this->getToken());
	// 	$result=$this->postCurl($url,'',$header,"GET");
	// 	return $result;
	// }
	// /*
	// 	导出聊天记录---分页
	// */
	// public function getChatRecordForPage($ql,$limit=0,$cursor){
	// 	$GLOBALS['base_url'] = $this->url;
	// 	if(!empty($ql)){
	// 		$url=$GLOBALS['base_url'].'chatmessages?ql='.$ql.'&limit='.$limit.'&cursor='.$cursor;
	// 	}
	// 	$header=array($this->getToken());
	// 	$result=$this->postCurl($url,'',$header,"GET");
	// 	$cursor=$result["cursor"];
	// 	// writeCursor("chatfile.txt",$cursor);
	// 	//var_dump($GLOBALS['cursor'].'00000000000000');
	// 	return $result;
	// }


	/**
	 * 获取Token
	 */
	public function getToken() {
		$option ['grant_type'] = "client_credentials";
		$option ['client_id'] = $this->client_id;
		$option ['client_secret'] = $this->client_secret;
		$url = $this->url . "token";
		$fp = @fopen ( "easemob.txt", 'r' );
		if ($fp) {
			$arr = unserialize ( fgets ( $fp ) );
			if ($arr ['expires_in'] < time ()) {
				$result = $this->postCurl ( $url, $option, $head = 0 );
				$result ['expires_in'] = $result ['expires_in'] + time ();
				@fwrite ( $fp, serialize ( $result ) );
				return $result ['access_token'];
				fclose ( $fp );
				exit ();
			}
			return $arr ['access_token'];
			fclose ( $fp );
			exit ();
		}
		$result = $this->postCurl ( $url, $option, $head = 0 );
		// exit;
		// $result = json_decode($result,true);
		$result ['expires_in'] = $result ['expires_in'] + time ();
		$fp = @fopen ( "easemob.txt", 'w' );
		@fwrite ( $fp, serialize ( $result ) );
		return $result ['access_token'];
		fclose ( $fp );
	}

	/**
	 * CURL Post
	 */
	protected function postCurl($url, $option, $header = 0, $type = 'POST') {
		$curl = curl_init (); // 启动一个CURL会话
		curl_setopt ( $curl, CURLOPT_URL, $url ); // 要访问的地址
		curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, FALSE ); // 对认证证书来源的检查
		curl_setopt ( $curl, CURLOPT_SSL_VERIFYHOST, FALSE ); // 从证书中检查SSL加密算法是否存在
		curl_setopt ( $curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0)' ); // 模拟用户使用的浏览器
		if (! empty ( $option )) {
			$options = json_encode ( $option );
			curl_setopt ( $curl, CURLOPT_POSTFIELDS, $options ); // Post提交的数据包
		}
		curl_setopt ( $curl, CURLOPT_TIMEOUT, 30 ); // 设置超时限制防止死循环

		if (!empty($header))
		curl_setopt ( $curl, CURLOPT_HTTPHEADER, $header ); // 设置HTTP头
		curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 ); // 获取的信息以文件流的形式返回
		curl_setopt ( $curl, CURLOPT_CUSTOMREQUEST, $type );
		$result = curl_exec ( $curl ); // 执行操作
		$res = json_decode ( $result, true ) ;
		// $res = object_array ( json_decode ( $result, trur ) );
		// $res ['status'] = curl_getinfo ( $curl, CURLINFO_HTTP_CODE );
		// pre ( $res );
		// D($res);
		curl_close ( $curl ); // 关闭CURL会话
		return $res;
	}
}

/**
 * IM 环信即时通讯云领导者
 * @author [Demon] <508037051@qq.com>
 */
class W2HuanXin extends W2IM
{
	/** [sendSucceed 校验是是否发送成功] */
	public static function sendSucceed( $targeID =  0, $sendInfo = [] )
	{
		return !empty($sendInfo['data'][$targeID])  && $sendInfo['data'][$targeID] === 'success';
	}

	const ADMIN = 'admin';
	const USERS = 'users';

	/*IM发送消息类型：sendType  string
	1 系统消息
	11 申请好友消息
	21 朋友圈评论或回复消息
	31 朋友圈又最新动态 （客户端红点显示）
	51 好友之间发送 项目列表信息 消息*/

	// const SEND_TYPE_SYSTEM = '1';
	const SEND_TYPE_APPLY = '11';
	// const SEND_TYPE_CONTENT = '21';
	const SEND_TYPE_USER_MESSAGE = '31';

	// 发送好友消息类型 项目
	const SEND_TYPE_PROJECT = '51';

	private static $getTokenValue = null;

	private static  $instance;

	/**
	 *
	 * [objMOdel 获取对象]
	 * @return [type] [description]
	 */
	public static function instance()
	{
		if(!isset(self::$instance))
		{
			self::$instance = new static();
		}

		return self::$instance;
	}

	public function __construct()
	{
		$options =
		[
			'client_id'     => CommonFun::loadConfig('IM.CLIENT_ID'),
			'client_secret' => CommonFun::loadConfig('IM.CLIENT_SECRET'),
			'org_name'      => CommonFun::loadConfig('IM.ORG_NAME'),
			'app_name'      => CommonFun::loadConfig('IM.APP_NAME'),
		];
		parent::__construct($options);
	}

	/** [hashIm 登录密码加密规则] */
	public static final function hashIm( $password =  '' )
    {
        if($password == '') return '';
        // $password = '999'; // 测试值 999
        $password = md5(trim($password)); // 对字符串进行去除空格，然后MD5运算
        // 运算后:b706835de79a2b4e80506f582af3676a

        $md5Password = md5($password . '@_2017_daTing' . substr($password, 2, 5));
        //  运算后: md5(b706835de79a2b4e80506f582af3676a@_2017_daTing06835)
        //  最终结果: e7e3e2261ea80164045dec75fc33a703
        // 密码连接上@_2017_daTing连接上密码从第2个字符截取到第5个字符
        return $md5Password;
    }

	/**
	 * 获取Token
	 */
	public function getToken()
	{
		if(is_null(self::$getTokenValue))
		{
			$option ['grant_type'] = "client_credentials";
			$option ['client_id'] = $this->client_id;
			$option ['client_secret'] = $this->client_secret;
			$url = $this->url . "token";
			$result = $this->postCurl ( $url, $option, $head = 0 );
			// D($option);
			// DD($result);
			// exit;
			// $result = ZTools::httpCurl ( $url, ZTools::toJson($option), 'POST' );
			self::$getTokenValue = $result['access_token'];
		}
		return self::$getTokenValue;
	}

	public function sendMessages($userID = 0, $sendUser = null,$message = '', $type = 'users' ,$ext = [])
	{
		if(!is_array($sendUser))
		{
			$sendUser = [$sendUser];
		}

		if(empty($userID))
        {
            $userID = 'admin';
        }
        $sendData = W2HuanXin::instance()->yy_hxSend($userID, $sendUser,$message,'users',$ext);
        return $sendData;
	}


	/** [accreditRegisterID 注册环信帐号体系] */
	public function accreditRegisterID( $userID =  0 ,$nickname = '')
	{
		if ($userID)
		{
			$options             =  [];
			$options['username'] = $userID;
			$options['nickname'] = $nickname;
			$options['password'] = static::hashIm($userID);
			$result              = $this->accreditRegister($options);
			// D($result);
			return $result;
		}
	}

	// public function accreditRegister($options) {
	// 	$url = $this->url . "users";
	// 	$access_token = $this->getToken ();
	// 	$header [] = 'Authorization: Bearer ' . $access_token;
	// 	$result = $this->postCurl ( $url, $options, $header );
	// 	return $result;
	// }

}