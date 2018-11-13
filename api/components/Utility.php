<?php



//-------------------全局方法-----------------------

/** debug 直接打印日志 */
function AX_DEBUG($p_info=null)
{
    if (defined('IS_AX_DEBUG') && !is_null($p_info))
    {
    	print("\n");
        $_dbt = debug_backtrace();
        foreach ($_dbt as $_i => $_d) {
            if(!array_key_exists('file', $_d) || $_d['file']=='' || $_d['file']==__file__)
            {
                continue;
            }
            $_fileName = pathinfo($_d['file'],PATHINFO_BASENAME);
            if ($_fileName == 'DBTool.php' || $_fileName == 'DBModel.php' || $_fileName == 'AbstractHandler.php' )
            {
            	continue;
            }
            $_dFuc = $_d['function'];
            // if (in_array($_dFuc , [ 'loadModelList' , 'loadModelListByIds' , 'loadModelListById' , 'loadModelFirstInList' , 'saveModel', 'update', 'delete' , 'count', 'countAll' ] ) )
            // {
            // 	continue;
            // }
            printf('%s [%d] %s -> %s ' , W2Time::microtimetostr(null,'Y-m-d H:i:s.u') , $_d['line'],  $_fileName, $_dFuc);
            break;
        }
        if (is_string($p_info))
        {
	        print(strlen($p_info)>100?" : \n":' : ');
	        print($p_info);
        }
        else
        {
        	var_export($p_info);
        }
        print("\n");
    }
}



/**
 * 自定义的一些方法工具
 * @package conf
 * @author axing
 * @since 1.0
 * @version 1.0
 */

class Utility
{
	/**
	 * cookie加密用字段
	 * @var string
	 */
	protected static $userCookieRandCode     = USER_COOKIE_RANDCODE;

	/**
	 * password加密用字段
	 * @var string
	 */
	protected static $passwordRandCode       = PASSWORD_RANDCODE;

	/**
	 * 静态变量，存储优化后的HEADERS信息
	 * @var array
	 */
	protected static $_HEADERS = null;

	/**
	 * 静态变量，存储当前用户ID
	 * @var array
	 */
	protected static $_CURRENTUSERID = false;
	// protected static $_CURRENTADMINID = false;

	/**
	 * 将用户和登陆时间组成加密字符
	 * @param  integer $p_userID 用户ID
	 * @param  string  $p_time   时间戳
	 * @return string            加密后字符
	 */
    public static function getCheckCode($p_userID, $p_userName, $p_time)
    {
        return md5(md5($p_userName).md5($p_time)).'///'.$p_userID.'///'.$p_userName.'///'.$p_time.'///'.md5(md5($p_userID).md5($p_time));
    }

    /** 将admin用户组成加密字符串 */
	public static function getCheckCodeOfAdmin($p_userID, $p_userName, $p_time)
	{
		return md5(md5($p_userName).USER_COOKIE_RANDCODE.md5($p_time)).'///'.$p_userID.'///'.$p_userName.'///'.$p_time.'///'.md5(md5($p_userID).USER_COOKIE_RANDCODE.md5($p_time));
	}

	/**
	 * 此函数千万别改规则
	 * 已知用到规则  用户支付密码
	 *
	 * 将密码再次加密
	 * @param  string $p_password 原始密码（一般此时已经经过初步MD5加密）
	 * @return string             加密后字符串（用于存储到数据库中）
	 */
    public static function getEncodedPwd($p_password)
    {
    	if (!is_null($p_password))
    	{
    		if (strlen($p_password)!=32)
    		{//如果没有经过md5加密，则此处需要先行md5加密一次
    			$p_password = md5($p_password);
    		}
	    	return md5(md5($p_password).static::$passwordRandCode.substr(md5($p_password),3,8));
    	}
    	return null;
    }


    /**
     * 提取请求中的headers信息，
     * 并复制一份首字母大写其他字母小写的key值，
     * 最后存储到$_HEADERS变量中供使用
     * @return array 优化后的headers信息
     */
	public static function getallheadersUcfirst()
	{
		if (static::$_HEADERS === null)
		{
			static::$_HEADERS = getallheaders();
			foreach (static::$_HEADERS as $key => $value) {
				static::$_HEADERS[ucfirst(strtolower($key))] = $value;
			}
		}
		return static::$_HEADERS;
	}
	public static function getDeviceTypeLabel($deviceType = 0)
	{
		switch ($deviceType)
		{
			case DEVICE_TYPE::BROWSER: $deviceTypeLabel = '浏览器'; break;
			case DEVICE_TYPE::ADMIN: $deviceTypeLabel = '后台'; break;
			case DEVICE_TYPE::ANDROID: $deviceTypeLabel = '安卓'; break;
			case DEVICE_TYPE::IOS: $deviceTypeLabel = '苹果'; break;
			default: $deviceTypeLabel = ''; break;
		}
		return $deviceTypeLabel;
	}

	public static function getHeaderValue($p_key)
	{
		return static::getHeadersValue($p_key);
		/*$_headers = Utility::getallheadersUcfirst();
		$p_key = ucfirst(strtolower($p_key));
		if (array_key_exists($p_key,$_headers))
		{
			if($p_key == 'Checkcode')
			{
				$_headers[$p_key] = urldecode($_headers[$p_key]);
				return $_headers[$p_key];
			}
		}
		return null;*/
	}


	/** [headers 获取key的值] */
    public static function getHeadersValue( $key  = ''  )
    {
		$key     = strtolower($key);
		$headers = array_change_key_case(getallheaders());
        if (!empty($key)) return isset($headers[$key]) ? urldecode($headers[$key]) : null ;
        return $headers;
    }

	public static function getCurrentIP()
	{
		$onlineip = null;
	    if(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown'))
	    {
	    	$onlineip = $_SERVER['REMOTE_ADDR'];
	    }
		if ( Utility::getHeaderValue('Devicetype') == DEVICE_TYPE::ADMIN )
		{
		    if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown'))
		    {
		    	$onlineip = getenv('HTTP_CLIENT_IP');
		    }
		    elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown'))
			{
				$onlineip = getenv('HTTP_X_FORWARDED_FOR');
			}
			elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown'))
		    {
		    	$onlineip = getenv('REMOTE_ADDR');
		    }
		}
		return $onlineip;
	}

	public static function setCurrentUserID($p_userID=null)
	{
		static::$_CURRENTUSERID = $p_userID;
	}


	public static function getIntCurrentUserID()
	{
		$userId = static::getCurrentUserID();
		$userId = intval($userId);
		return $userId;
	}

	public static function getCurrentUserID()
    {
        if (static::$_CURRENTUSERID === false )
        {
            $p_userID = null;
            $infoHeader = getallheaders();
            $userinfo = Utility::getHeaderValue('Checkcode');
            $userinfo = explode('///',$userinfo);
            if (count($userinfo)==5)
            {
                $check1 = $userinfo[0];
                $id = $userinfo[1];//用户id
                $user_name = $userinfo[2];//用户名
                $login_time = $userinfo[3];//登录时间
                $check2 = $userinfo[4];

                if(md5(md5($user_name).md5($login_time))==$userinfo[0] && md5(md5($id).md5($login_time))==$check2){
                    $p_userID = $id;
                }
            }

            static::setCurrentUserID($p_userID);
        }
        return static::$_CURRENTUSERID ;
    }


	public static function getHeaderAuthInfoForUserID($p_userID,$p_userName)
    {
        $p_time = time();
        return array(
                'Userid'=>$p_userID
                ,'Logintime'=>$p_time
                ,'Checkcode'=>Utility::getCheckCode($p_userID,$p_userName,$p_time)
            );
    }

	public static function getUserByID($p_userID)
	{
		if ($p_userID==0)
		{
			return null;
		}
		$_clsHandler = USERHANDLER_NAME;
		return $_clsHandler::loadModelById($p_userID);
	}

	public static function getLngbaidu()
	{
		return W2HttpRequest::getRequestFloat('lngbaidu');
	}

	public static function getLatbaidu()
	{
		return W2HttpRequest::getRequestFloat('latbaidu');
	}

	/**
	 * [getCurrentUserModel description]
	 * @return UserModel   用户
	 */
	public static function getCurrentUserModel()
	{
		$_clsHandler = USERHANDLER_NAME;
		$tmpModel    =  $_clsHandler::loadModelById(Utility::getIntCurrentUserID());
		return $tmpModel;
	}

	/**
	 * 获得组装后的结果数组
	 * @param  integer $errorCode 错误码，0为正常
	 * @param  string  $errorStr  错误描述
	 * @param  array   $result    返回数据
	 * @param  array   $extraInfo 返回额外数据
	 * @return array             结果数组
	 */
    public static function getArrayForResults($errorCode=0,$errorStr='',$result = array(),$extraInfo=array())
    {
    	return HaoResult::init(is_array($errorCode)?$errorCode:array($errorCode,$errorStr,$errorStr),$result,$extraInfo);
    }

    /**
     * 判断结果数组是否正确获得结果
     * @param  array  $tmpResult 结果数组
     * @return boolean            是否正确获得
     */
    public static function isResults($tmpResult=null)
    {
    	return  (is_object($tmpResult) && get_class($tmpResult)=='HaoResult') || (is_array($tmpResult) && array_key_exists('errorCode',$tmpResult) ) ;
    }

    /**
     * 判断结果数组是否正确获得结果
     * @param  array  $tmpResult 结果数组
     * @return boolean            是否正确获得
     */
    public static function isResultsOK($tmpResult=null)
    {
    	return (Utility::isResults($tmpResult) && ((is_object($tmpResult) && $tmpResult->isResultsOK()) || (is_array($tmpResult) && $tmpResult['errorCode']==RUNTIME_CODE_OK)));
    }

    /**
     * 判断结果数组是否正确获得结果，并取出其中的结果
     * @param  array  $tmpResult 结果数组
     * @return boolean            是否正确获得
     */
    public static function getResults($tmpResult=null)
    {
    	if (Utility::isResultsOK($tmpResult))
    	{
    		return is_object($tmpResult)?$tmpResult->getResults():$tmpResult['results'];
    	}
    	return null;
    }

    /**
     * 将数组（或字典）的key和value组成%s=%s字符串
     * 如果是字典则组成 people[height]=180;（注意：没有引号）
     * 如果是数组则组成 people[] = 180;
     * html前端注意，不要混用people[height]和people[]，会导致后者被转成字典哦。
     * 尽量字典和数组使用不同变量。
     * @param  array $array array
     * @param  string $key   前缀
     * @return array        [height=180,people[sex]=1]
     */
    protected static function getTmpArr($array,$key='')
    {
    	$tmpArr = array();
    	if (is_array($array))
    	{
            $isList = W2Array::isList($array);
    		foreach ($array as $_key => $_value) {
    			$_tmp =  static::getTmpArr(
			    						$_value
			    						,$key!=''
					    					?$key
					    						.( ( $isList || is_array($_value) )
					    						 	?'['.$_key.']'
					    						 	:'[]'
					    						 )
					    					:$_key
			    					);
    			$tmpArr = array_merge($tmpArr,$_tmp);
    		}
    	}
    	else
    	{
    		$tmpArr[] = sprintf('%s=%s', $key, $array);
    	}
    	return $tmpArr;
    }

    /**
     * 对请求进行校验
     * @return HaoResult
     */
    public static function getAuthForApiRequest()
    {
    	$isAuthed = false;

		$_HEADERS = Utility::getallheadersUcfirst();

		if (array_key_exists('Signature', $_HEADERS))
		{
			//定义一个空的数组
			$tmpArr = array();

			//将所有头信息和数据组合成字符串格式：%s=%s，存入上面的数组
			foreach (array('Clientversion','Devicetype','Devicetoken','Requesttime','Userid','Logintime','Checkcode') as $_key) {
				if (array_key_exists($_key,$_HEADERS))
				{
					array_push($tmpArr, sprintf('%s=%s', $_key, $_HEADERS[$_key]));
				}
				else
				{
					array_push($tmpArr, sprintf('%s=%s', $_key, ''));
					// 考虑到客户端不同浏览器，对于请求头信息的传输方式不一样
					// 如 火狐浏览器 没有参数值， 就不传这个值和这个kye
					// return HaoResult::init(ERROR_CODE::$PARAM_ERROR,array('errorContent'=>'缺少头信息：'.$_key));
				}
			}

			if (abs($_HEADERS['Requesttime'] - time()) > 5*60 )//300
			{
				return HaoResult::init(ERROR_CODE::$REQUEST_TIME_OUT);
			}

			//加密版本2.0，支持应用识别码和debug模式
			if (!isset($_REQUEST['r']))
			{
				foreach (array('Clientinfo','Isdebug') as $_key) {
					if (array_key_exists($_key,$_HEADERS))
					{
						array_push($tmpArr, sprintf('%s=%s', $_key, $_HEADERS[$_key]));
					}
					else
					{
						return HaoResult::init(ERROR_CODE::$PARAM_ERROR,array('errorContent'=>'缺少头信息：'.$_key));
					}
				}

				array_push($tmpArr, sprintf('%s=%s%s', 'link', $_SERVER['HTTP_HOST'],preg_replace ("/(\/*[\?#].*$|[\?#].*$|\/*$)/", '', $_SERVER['REQUEST_URI'])));
			}
		    //是否开启debug
		    if (isset($_HEADERS['Isdebug']) && $_HEADERS['Isdebug']=='1')
		    {
		        define('IS_SQL_PRINT',True);
		        define('IS_AX_DEBUG',True);
		    }

			//同样的，将所有表单数据也组成字符串后，放入数组。（注：file类型不包含）
			$tmpArr = array_merge($tmpArr , static::getTmpArr($_REQUEST) );

			//最后，将一串约定好的密钥字符串也放入数组。（不同的项目甚至不同的版本中，可以使用不同的密钥）
			switch ($_HEADERS['Devicetype']) {

				case DEVICE_TYPE::BROWSER://浏览器设备
					array_push($tmpArr, SECRET_HAX_BROWSER);
					break;
				case DEVICE_TYPE::ADMIN://ADMIN设备，服务器
					array_push($tmpArr, SECRET_HAX_ADMIN);
					break;
				case DEVICE_TYPE::ANDROID://安卓
					array_push($tmpArr, SECRET_HAX_ANDROID);
					break;
				case DEVICE_TYPE::IOS://iOS
					array_push($tmpArr, SECRET_HAX_IOS);
					break;
				case DEVICE_TYPE::WXAPP://iOS
					array_push($tmpArr, SECRET_HAX_WXAPP);
					break;
				case DEVICE_TYPE::NY_API://NY_API
					array_push($tmpArr, SECRET_HAX_NY_API);
					break;
			}

			//对数组进行自然排序
			sort($tmpArr, SORT_STRING);

			//将排序后的数组组合成字符串
			$tmpStr = implode( $tmpArr );

			//对这个字符串进行MD5加密，即可获得Signature
			$tmpStr = md5( $tmpStr );

			$isAuthed = true;//默认验证通过

			//如果不通过，则返回调试信息。
			if( $tmpStr != $_HEADERS['Signature'] ){
				$isAuthed = array(
					'status'=>false,
					'tmpArr'=>$tmpArr,
					'tmpArrString'=>implode( $tmpArr ),
					'tmpArrMd5'=>$tmpStr,
					'getallheaders()'=>getallheaders(),
					'_GET'=>$_GET,
					'_POST'=>$_POST,
					'_FILES'=>$_FILES,
					'_SERVER'=>$_SERVER,
					);
			}

		}
		else if (false)
		{
			$isAuthed = true;
		}
		else
		{
			return HaoResult::init(ERROR_CODE::$PARAM_ERROR,array('errorContent'=>'缺少头信息：'.'signature'));
		}
		if ($isAuthed === true)
		{
			return HaoResult::init(ERROR_CODE::$OK,$isAuthed);
		}
		else
		{
			return HaoResult::init(ERROR_CODE::$SIGNATURE_WRONG,(AXAPI_DEPLOY_STATUS==1?$isAuthed:''));
		}

    }


    /** 字符串转换。驼峰式字符串（首字母小写） */
    public static function camelCase($str)
    {
        //使用空格隔开后，每个单词首字母大写
        $str = ucwords(str_replace('_', ' ', $str));
        //小写字符串的首字母，然后删除空格
        $str = str_replace(' ','',lcfirst($str));
        $str = str_replace('Id','ID',$str);
        return $str;
    }

    /** 字符串转换。驼峰转换成下划线的形式 */
    public static function under_score($str) {
        $str = str_replace('ID','Id',$str);
        return strtolower(ltrim(preg_replace_callback('/[A-Z]/', function ($mathes) { return '_' . strtolower($mathes[0]); }, $str), '_'));
    }

    /**
     * [jsonDecode description]
     * @param  string  $json    [description]
     * @param  boolean $isArray [description]
     * @return [type]           [description]
     */
    public static function jsonDecode($json = '', $isArray = true)
    {
    	$array = json_decode($json,$isArray);
    	return $array;
	}
	/**
	 * [jsonEncode description]
	 * @param  string $json [description]
	 * @return [type]       [description]
	 */
	public static function jsonEncode($json = '')
	{
		$json = static::json_encode_unicode($json);
		return $json;
	}

    /** PHP5.4以上使用JSON_UNESCAPED_UNICODE编码json字符，否则只能自己实现了。 */
    public static function json_encode_unicode($data) {
	    if (defined('JSON_UNESCAPED_UNICODE')) {
	        return json_encode($data, JSON_UNESCAPED_UNICODE);
	    }
	    return preg_replace_callback('/(?<!\\\\)\\\\u([0-9a-f]{4})/i',
		    function($m) {
		        $d = pack("H*", $m[1]);
		        $r = mb_convert_encoding($d, "UTF8", "UTF-16BE");
		        return $r !== "?" && $r !== "" ? $r: $m[0];
		    },
		    json_encode($data)
	    );
	}

	/**
	 * [getDomainURL description]
	 * @param  [type] $imgurl [description]
	 * @param  [type] $size   [description]
	 * @return [type]         [description]
	 */
	public static function getDomainURL($imgurl,$size)
	{
		$imgurl=str_replace('./', '', $imgurl);
		$imgurlinfo=explode('.', $imgurl);
		if($size == ''){
			return DOMAIN_ADDRESS.$imgurl;
		}else{
			return DOMAIN_ADDRESS.$imgurlinfo[0].$size.$imgurlinfo[1];
		}

	}

	/**
	 * [getImgPreview 拼装图片展示URL 如果图片里面带http直接返回，一般第三方登录会是 绝对URL路径]
	 * @param  string $imgurl     [description]
	 * @param  string $imgDefalut [description]
	 * @return [type]             [description]
	 */
	public static function getImgPreview($imgurl = '', $imgDefalut = '')
	{
		$imgPreview = '';
		if(!empty($imgurl))
		{
			// 不存在 http
			if(strpos($imgurl, 'http') === false)
			{
				$imgPreview = UPLOAD_IMG_URL . $imgurl;
			}
			else
			{
				$imgPreview = $imgurl;
			}
		}
		else
		{
			$imgPreview = '';
		}
		return $imgPreview;
	}

	public static function getImgPreviewList($imgurl = '', $imgDefalut = '')
	{
		$arrImgList = explode(',', $imgurl);
		$imgList = [];
		foreach ($arrImgList as $img)
		{
			$imgList[] = static::getImgPreview($img,$imgDefalut);
		}
		return $imgList;
	}



	public static function getDescriptionsInModel($modelName)
	{
	    $keyList = array();
        $p_className = $modelName.'Model';
         $classNameV3 = str_replace('Model', '', $p_className).'/'.$p_className;
         $_dir = AXAPI_ROOT_PATH.'/mhc/models';
        if (isset($classNameV3))
        {
            $classNameV3 = preg_replace_callback('/([A-Za-z])/us', function($matches){
                                                    return '['.strtolower($matches[1]).strtoupper($matches[1]).']';
                                                }, $classNameV3);
            foreach (glob(AXAPI_ROOT_PATH.'/mhc/'.$classNameV3.'.php') as $_file) {
                $_modelFilePath = $_file;
                break;
            }
        }
        if (!isset($_modelFilePath))
        {
            $p_className = strtolower($p_className).'.php';
            foreach (glob($_dir.'/*.php') as $_file) {
                if (strtolower(basename($_file)) == $p_className)
                {
                    $_modelFilePath =  $_file;
                    break;
                }
            }
        }

		if (isset($_modelFilePath) && file_exists($_modelFilePath))
		{
		    $content = file_get_contents($_modelFilePath);
		    preg_match_all('/(\/\*[^\/]*?\*\/|\/\/.*|)\s+public function get(.*?)\(/',$content,$matches,PREG_SET_ORDER);
		    foreach ($matches as $match) {
		        $description = $match[1];
		        $keyStr = lcfirst($match[2]);
		        $keyList[$keyStr] = $description;
		    }


		    preg_match_all('/(\/\*[^\/]*?\*\/|\/\/.*|)\s+public \$(.*);/',$content,$matches,PREG_SET_ORDER);
		    foreach ($matches as $match) {
		        $description = $match[1];
		        $keyStr = lcfirst(W2String::camelCase($match[2]));
		        if ($description!='' && array_key_exists($keyStr,$keyList) && $keyList[$keyStr]==null)
		        {
		            $keyList[$keyStr] = $description;
		        }
		    }
		}
		return $keyList;
	}

	/** [funcNextval 获取数据库主键自增ID值] */
    public static function funcNextval($seq)
    {
    	$sql = 'select func_nextval(\'seq_'.$seq.'\')';
        return DBTool::queryValue($sql);
    }


	/** [isDeployStatus 是否是测试环境] */
	public static function isDeployStatus()
	{
		return defined('AXAPI_DEPLOY_STATUS') && AXAPI_DEPLOY_STATUS === 1;
	}


	public static function isClient()
	{
		return !static::isAdmin();
	}
	public static function isAndroid()
	{
		return Utility::getHeaderValue('devicetype') ==  DEVICE_TYPE::ANDROID;
	}
	public static function isNYApi()
	{
		return Utility::getHeaderValue('devicetype') ==  DEVICE_TYPE::NY_API;
	}
	public static function isAdmin()
	{
		return Utility::getHeaderValue('devicetype') ==  DEVICE_TYPE::ADMIN;
	}

    /**
     * [getArrPageUrl 获取URL参数]
     * @param  string $pageUrl [description]
     * @return [type]          [description]
     */
    public static function getArrPageUrl($pageUrl = '')
    {
    	$arrQuery = [];
    	$arrPageUrl = parse_url($pageUrl);
    	if($arrPageUrl['query'])
    	{
    		parse_str($arrPageUrl['query'],$arrQuery);
    	}
    	return $arrQuery;
    }

    // 获取生日对应的年龄
    public static function birthday($birthday = 0)
    {
		$age = strtotime($birthday);
		if($age === false)
		{
			return 0;
		}
		list($y1,$m1,$d1) = explode("-",date("Y-m-d",$age));
		$now = strtotime("now");
		list($y2,$m2,$d2) = explode("-",date("Y-m-d",$now));
		$age = $y2 - $y1;
		if((int)($m2.$d2) < (int)($m1.$d1))
		$age -= 1;
		return $age;
	}

	public static function getTimeLabel($time = 0, $format = 'Y-m-d H:i')
	{
		$label  = '';
		if($time > 0)
		{
			$time = strtotime($time);
			$label = date($format,$time);
		}
		return $label;
	}
	public function getTimeNow()
    {
        return date('Y-m-d H:i:s');
    }


    /**
	 * 官方解释 https://developer.mozilla.org/en-US/docs/Web/HTTP/Access_control_CORS
	 * 前台跨域post请求，由于CORS（cross origin resource share）规范的存在，浏览器会首先发送一次options嗅探，同时header带上origin，判断是否有跨域请求权限，服务器响应access control allow origin的值，供浏览器与origin匹配，如果匹配则正式发送post请求。
	 * [[ajaxCrossDomain description]
	 * @return [type] [description]
	 */
	public static function ajaxCrossDomain()
	{
		$origin = isset($_SERVER['HTTP_ORIGIN'])? $_SERVER['HTTP_ORIGIN'] : '';
	    if(static::isAjaxCrossDomain($origin))
	    {
	        // 允许请求的heders
	        $headers =
	        [
	        	'Clientinfo',
				'Clientversion',
				'Devicetype',
				'Devicetoken',
				'Requesttime',
				'Userid',
				'Logintime',
				'Checkcode',
				'Signature',
				'Isdebug',
				'cookie',
				'Cookie',
				'openid',
	        ];
	        $headers = implode(',', $headers);
	        header('Access-Control-Allow-Credentials: true');
	        header('Access-Control-Allow-Headers:' . $headers);
	        header('Access-Control-Allow-Origin:' . $origin);
	        header('Access-Control-Allow-Methods:POST,GET,OPTIONS,DELETE'); //支持的http动作
	        // header("Access-Control-Allow-Origin:*");
	    }

	    // ajax首次请求获取是否授权不处理逻辑 直接结束
	    if($_SERVER['REQUEST_METHOD']=='OPTIONS')
        {
            header('HTTP/1.1 202 Accepted');
            exit;
        }

	}

	/**
	 * [isAjaxCrossDomain 是否允许跨域请求域名]
	 * @return boolean [description]
	 */
	public static function isAjaxCrossDomain($origin = '')
	{
		$isAjaxCrossDomain = false;
	    global $allowOrigin;
	    if(static::isDeployStatus() || !empty($allowOrigin) && in_array($origin, $allowOrigin))
	    // if(!empty($allowOrigin) && in_array($origin, $allowOrigin))
	    {
	    	$isAjaxCrossDomain = true;
	    }
	    return $isAjaxCrossDomain;
	}




     /**
     * [getBarcode description]
     * @param  string $url [description]
     * @return [type]      [description]
     */
    public static function getBarcode($url = '', $logo = '', $width = 150, $height = 150)
    {
        // http://www.liantu.com/pingtai/
        $barcodeApi = 'http://qr.liantu.com/api.php';
        // $url = str_replace('http://','',$url);
        $url = strtr($url,['http://' => 'http://', '&' => '%26']);
        // $params['text'] = urlencode($url);
        $params['text'] = $url;
        empty($logo) OR $params['logo'] = $logo;
        $params['w'] = $width;
        $params['h'] = $height;
        $params['m'] = 8;
        $barcodeApi .= '?' . http_build_query($params);
        // D($barcodeApi);
        return $barcodeApi;
    }



}

