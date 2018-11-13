<?php
/**
 * Axapi相关接口（用来看些日志信息之类的）
 * @package Controller
 * @author zb
 * @since 1.0
 * @version 1.0
 */
class AxapiController extends BaseController{

    public static function actionSayHello()
    {

        $xml = '<xml>
  <wxappid><![CDATA[wx1b7559b818e3c123]]></wxappid>
  <mch_id>1235571234</mch_id>
  <mch_billno>1235571234201605241726128109</mch_billno>
  <client_ip><![CDATA[127.0.0.1]]></client_ip>
  <re_openid><![CDATA[oiPuduGV7gJ_MOSfAWpVmhhgXh-U]]></re_openid>
  <total_amount>101</total_amount>
  <min_value>101</min_value>
  <max_value>101</max_value>
  <total_num>1</total_num>
  <nick_name><![CDATA[方倍工作室]]></nick_name>
  <send_name><![CDATA[方倍工作室]]></send_name>
  <wishing><![CDATA[恭喜发财]]></wishing>
  <act_name><![CDATA[方倍工作室送红包]]></act_name>
  <remark><![CDATA[关注公众账号]]></remark>
</xml>';
        $result = W2PayWx::xmlToArray($xml);
        D($result);
        exit;
        return HaoResult::init(ERROR_CODE::$OK,array('$_GET'=>$_GET,'$_POST'=>$_POST,'$_FILES'=>$_FILES,'getallheaders()'=>getallheaders(),'$_SERVER'=>$_SERVER));
    }

    public static function actionLoadLogList()
    {
        switch ($auth = static::getAuthIfUserCanDoIt(Utility::getCurrentUserID(),'axapi',null))
        {
            case 'admin'   : //有管理权限
                break;
            case 'self'    : //作者
            case 'normal'  : //正常用户
            case 'draft'   : //未激活
            case 'pending' : //待审禁言
            case 'disabled': //封号
            case 'visitor' : //游客
            default :
                // return HaoResult::init(ERROR_CODE::$NO_AUTH);
                break;
        }

        $p_type = W2HttpRequest::getRequestString('type');
        $p_datetime = W2HttpRequest::getRequestDateTime('datetime');
        $p_pageIndex = W2HttpRequest::getRequestInt('page',null,false,true,1);
        $p_pageSize = W2HttpRequest::getRequestInt('size',null,false,true,100);
        // $p_countThis = W2HttpRequest::getRequestBool('iscountall')?1:-1;

        switch ($p_type) {
            case 'access':
            case 'error':
                break;

            default:
                return HaoResult::init(ERROR_CODE::$LOGLIST_TYPE_WRONG);
                break;
        }


        $logFilePath = sprintf('%s/%s-%s.log'
                            ,AXAPI_ROOT_PATH.'/logs/'
                            ,$p_type
                            ,W2Time::timetostr($p_datetime,'Ymd'));

        if (defined('IS_AX_DEBUG')){print("\n");print(W2Time::microtimetostr());print("\n");var_export($logFilePath);print("\n");}
        if (!file_exists($logFilePath))
        {
            return HaoResult::init(ERROR_CODE::$LOGLIST_NO_LOG_FOUND);
        }

        $lineList = file($logFilePath);//把整个文件读入一个数组中。


        $p_pageIndex = 0 - $p_pageIndex;

        if ($p_pageIndex < 0 && $p_pageSize>0)
        {
            $pageIndexMax = (intval((count($lineList)-1)/$p_pageSize)+1);
            $p_pageIndex += $pageIndexMax+1; //分页从1开始，第一页就是1.
        }

        $logFileSeek = ($p_pageIndex-1)*$p_pageSize;

        if($p_type == 'access')
        {
            $p_pageIndex = W2HttpRequest::getRequestInt('page',null,false,true,1);
            $p_pageSize = W2HttpRequest::getRequestInt('size',null,false,true,100);
            $p_pageIndex = max($p_pageIndex-1,0)*$p_pageSize;

            rsort($lineList);
            $listData = [];
            $dbIndex = 0;
            foreach ($lineList as $key => $s_line)
            {
                // preg_match('/\{.*\}/i', $s_line, $match);
                $_arrList = explode(' ',$s_line);
                if(count($_arrList) == 12)
                {
                    // && $dbIndex <=  ($p_pageIndex+$p_pageSize)
                    // D($p_pageIndex,$dbIndex, $p_pageIndex >= $dbIndex );
                    $s_line = strtr($s_line,['[' => '',']' => '']);
                    $arrList = explode(' ',$s_line);
                    $apiName = $arrList[9];
                    if($apiName != 'axapi/LoadLogList:')
                    {
                        if($dbIndex  >= $p_pageIndex && $dbIndex < ($p_pageIndex+$p_pageSize)  )
                        {
                            $info = [];
                            $info['date'] = "{$arrList[0]} {$arrList[1]}";
                            $info['timezone'] = $arrList[2];
                            $info['time'] = $arrList[3];
                            $info['ip'] = $arrList[4];
                            $info['Devicetype'] = $arrList[5];
                            $info['Clientversion'] = $arrList[6];
                            $info['userID'] = $arrList[7];
                            $info['method'] = $arrList[8];
                            $info['apiName'] = $apiName;
                            $info['params'] = json_decode($_arrList[10],1);
                            $info['results'] = $arrList[11];
                            $listData[] = $info;
                        }
                        $dbIndex++;
                    }
                }
            }
            return HaoResult::init(ERROR_CODE::$OK,$listData);
            D($arrList);
            D($lineList);
            D($listData);
            exit;
        }

        $result = array();

        for ($i=$logFileSeek; $i < count($lineList) && $i < $logFileSeek+$p_pageSize ; $i++) {
            $s_line = $lineList[$i];
            try {
                preg_match('/^(.*?\]): (.*+)$/',$s_line,$a_match);
                if (!is_array($a_match) || count($a_match)==0)
                {
                    $result[] = array('info'=>$s_line,'more'=>null);
                }
                else
                {
                    $result[] = array('info'=>$a_match[1],'more'=>json_decode($a_match[2]));
                }
            } catch (Exception $e) {
                $result[] = array('info'=>$s_line,'more'=>null);
            }

        }

        rsort($result);

        return HaoResult::init(ERROR_CODE::$OK,$result);
    }

    public static function actionCreateMhcWithTableName()
    {
        if (static::getAuthIfUserCanDoIt(Utility::getCurrentUserID(),'axapi',null) != 'admin')
        {
           return HaoResult::init(ERROR_CODE::$NO_AUTH);
        }

        require_once(AXAPI_ROOT_PATH.'/mhc/create_mhc_with_table_name.php');

        exit;
    }

    public static function actionUpdateCodesOfHaoConnect()
    {
        if (static::getAuthIfUserCanDoIt(Utility::getCurrentUserID(),'axapi',null) != 'admin')
        {
            return HaoResult::init(ERROR_CODE::$NO_AUTH);
        }

        require_once(AXAPI_ROOT_PATH.'/mhc/create_haoconnect_codes.php');

        exit;
    }

    public static function actionGetDescriptionsInModel()
    {
        $modelName = W2HttpRequest::getRequestString('model_name',false);

        return Utility::getDescriptionsInModel($modelName);
    }


    public static function actionGetHomeTableForTest()
    {
        $result = array();
        for ($i=0; $i < 10; $i++) {
            switch (rand(0,5)) {
                case 1:
                    // $result[] = UserHandler::loadModelFirstInList(array(),'rand()');
                    break;
                case 2:
                    $result[] = SmsVerifyHandler::loadModelFirstInList(array(),'rand()');
                    break;
                case 3:
                    // $result[] = UnionLoginHandler::loadModelFirstInList(array(),'rand()');
                    break;
                case 4:
                    $result[] = array('suibian'=>'随便','looklook'=>'seesee');
                    break;
                case 5:
                    $result[] = array('one'=>array('two'=>array('three'=>'four')));
                    break;
            }

        }

        $sleep = W2HttpRequest::getRequestInt('sleep');
        if ( $sleep>0)
        {
            sleep($sleep);
        }
        // echo('x');exit;
        // sleep(rand(2,4));
        return HaoResult::init(ERROR_CODE::$OK,$result);
    }

    /**
     * 根据验证码生成密钥，（或判断密钥是否正确）
     * @param  string $captchaCode 验证码
     * @param  string $checkKey    密钥（待验证）
     * @return [type]              [description]
     */
    public static function getCaptchaKeyOfCode($captchaCode,$checkKey=null)
    {
        if (!isset($captchaCode))
        {
            return false;
        }
        if (!is_null($checkKey))
        {
            $captchaTime = substr($checkKey,33);
            if (W2Time::getTimeBetweenDateTime(null,$captchaTime)>60)
            {//每个验证码生成后只有60秒可用。
                return false;
            }
            if (W2Cache::incr($checkKey)>3)
            {//当缓存接口可用时，会进行次数验证，每个验证码有三次机会。
                return false;
            }
        }
        else
        {
            $captchaTime = time();
        }

        $captchaCode= strtolower($captchaCode);
        $captchaKey  = md5(md5($captchaCode).md5($captchaTime).md5($captchaCode.$captchaTime.CAPTCHA_RANDCODE)).'_'.$captchaTime;
        if (!is_null($checkKey))
        {
            $isRight = ($checkKey===$captchaKey);
            if ($isRight)
            {
                W2Cache::incr($checkKey,3);//如果验证正确，验证次数+3
            }
            return $isRight;
        }
        return $captchaKey;
    }

    /** 获取一个验证码图像 */
    public static function actionGetCaptcha()
    {
        $captchaCode  = W2String::buildRandCharacters(4);
        $image        = W2Image::captchaImage($captchaCode,200,80);
        $content      = W2Image::toString($image);
        $result       = array();
        $result['url'] = 'data:image/jpeg;base64,'.base64_encode($content);
        $result['captchaKey'] = static::getCaptchaKeyOfCode($captchaCode);
        return HaoResult::init(ERROR_CODE::$OK,$result);
    }

    /** 获取一个验证码图像 */
    public static function actionCheckCaptcha()
    {
        $captchaCode  = W2HttpRequest::getRequestString('captcha_code',false,'',1);
        $captchaKey   = W2HttpRequest::getRequestString('captcha_key',false,'',1);
        $isRight       = static::getCaptchaKeyOfCode($captchaCode,$captchaKey);
        if ($isRight)
        {
            return HaoResult::init(ERROR_CODE::$OK,true);
        }
        else
        {
            return HaoResult::init(ERROR_CODE::$CAPTCHA_CODE_WRONG);
        }
    }

    public static function actionautoCreate()
    {
        $params = W2Params::instancePost();
        if(!$params->isEmpty('tableName'))
        {
            D(W2Template::putTemplate($params->getParams()));
            exit;
        }
        return HaoResult::initError('请指定表');
    }


    /**
     * [actionUpPicture 公共接口:ajax上传图片]
     * @return [type] [description]
     */
    public static function actionUpFile()
    {
        // ini_set('display_errors', 'Off');
        // ini_set('memory_limit', -1); //-1 / 10240M
        // ini_set("max_execution_time", 0);
        // //ini_set('magic_quotes_gpc', 'On');
        // //
        // ini_set('post_max_size','50M');
        // ini_set('upload_max_filesize','30M');

        $fileHaoResult = HaoResult::initError('上传出错了');
        $params = W2Params::instancePost();

        // 上传图片之后，更换图片地址
        if(isset($_FILES['file']['error']))
        {
            $error = $_FILES['file']['error'];
            if( $error  === 0)
            {
                if(!empty($_REQUEST['file_name']))
                {
                    $_FILES['file']['source_name'] = $_REQUEST['file_name'];
                }
                $fileHaoResult = AxapiController::actionUpImgFile($_FILES['file']);
            }
            else
            {
                $fileHaoResult->setErrorStr('请检查资源');
            }
        }
        return $fileHaoResult;
    }


    /**
    * [getUpfilePath 根据分类名称数据库中得到上传的路径paht]
    * @param  string $category [description]
    * @param  string $pathType [description]
    * @return [type]           [description]
    */
    public static function getUpfilePath($category = '', $pathType ='')
    {
        $valuePath = '';
        $where         = [];
        $where['name'] =  $pathType;
        // $category = lcfirst($category);
        $category = ucfirst($category);
        $handlerName = "SysConfig{$category}Handler";
        // D($handlerName, $where, $category);
        if(method_exists($handlerName, 'loadModelFirstInList'))
        {
            $model = $handlerName::loadModelFirstInList($where);
            if(is_object($model))
            {
                $valuePath = $model->getValue();
                $valuePath = trim($valuePath,'/');
                $valuePath = "/{$valuePath}/";
            }
        }
        return $valuePath;
    }

    /**
     * [actionUpFile 按照分类读取数据库上传图片]
     * @return [type] [description]
     */
    public static function actionUploadFile()
    {
        $params = W2Params::instancePost();
        // 不能为空参数
        $required =
        [
            'category'  => '模块名称 cw ec job witkey',
            'path_type' => '分类名称',
            // 'id'        => '数据ID',
        ];
        $haoResult = $params->checkRequired($required);
        if(!$haoResult->isResultsOK()) return  $haoResult;

        $haoResult = HaoResult::initError('上传图片出错了');

        $pathType = $params->getString('pathType');
        $category = $params->getString('category');
        $id       = $params->getString('id');
        $fileName = $params->getString('fileName');

        $filePath = static::getUpfilePath($category, $pathType);
        if(empty($filePath))
        {
            $haoResult = HaoResult::initError("{$category} 和 {$pathType} 不存在配置");
            return $haoResult;
        }
        $filePath = "{$filePath}{$id}";
        $upPath = '';
        // 上传图片之后，更换图片地址
        if(isset($_FILES['file']['error']))
        {
            $error = $_FILES['file']['error'];
            if( $error  === 0)
            {
                if(!empty($fileName))
                {
                    $_FILES['file']['source_name'] = $fileName;
                }

                $file      = $_FILES['file'];
                $fileInfo  = W2File::getFileInfo($file);
                $params    = W2Params::instance($fileInfo);

                if(empty($_REQUEST['file_alias']))
                {
                    $filePath  .= "/{$fileInfo['file_md5']}_{$fileInfo['file_size']}.{$fileInfo['file_type']}";
                }
                else
                {
                    if(stripos($_REQUEST['file_alias'],'.')===FALSE)
                    {
                        $filePath  .= "/{$_REQUEST['file_alias']}.{$fileInfo['file_type']}";
                    }
                    else
                    {
                        $filePath  .= "/{$_REQUEST['file_alias']}";
                    }
                }

                $haoResult = static::actionGetImgInfo($params,1,$filePath);
                if($haoResult->isResultsOK())
                {
                    $results = $haoResult->getResults();
                    $upPath = empty($results['upPath']) ? '': $results['upPath'];
                    if(!empty($results['filePath']))
                    {
                        $haoResult = static::actionupImg(null, $results,$file);
                    }
                }
            }
            else
            {
                $haoResult->setErrorStr('请检查图片资源');
                return $haoResult;
            }
        }

        // 上传图片字符串流
        $base64 = $params->getString('base64');
        if(!empty($base64))
        {
            // $fileInfo          = W2File::getStrImgInfo($base64,$fileName);
            $fileInfo             = W2File::getBase64ImgInfo($base64,$fileName);
            $fileInfo['pathType'] = $pathType;
            $params               = W2Params::instance($fileInfo);
            // $filePath          .= date('Ymd')."/{$fileInfo['file_md5']}_{$fileInfo['file_size']}.{$fileInfo['file_type']}";
            $filePath             .= "/{$fileInfo['file_md5']}_{$fileInfo['file_size']}.{$fileInfo['file_type']}";
            $haoResult            = static::actionGetImgInfo($params,1,$filePath);
            if($haoResult->isResultsOK())
            {
                $results = $haoResult->getResults();
                $upPath = empty($results['upPath']) ? '': $results['upPath'];
                if(!empty($upPath))
                {
                    W2File::mkDirs($upPath);
                    file_put_contents($upPath, $fileInfo['file_data']);
                    $haoResult = static::actionGetImgInfo($params,1,$filePath);
                }
            }
        }

        if($haoResult->isResultsOK())
        {
            $results     = $haoResult->getResults();
            $cropImgList = static::handlerCropSizeList($upPath);
            if(!empty($cropImgList))
            {
                $results['cropImgList'] = $cropImgList;
            }
            $haoResult->setResults($results);
        }

        return $haoResult;
    }


    /**
     * [actionUpImg 上传图片]
     * @param  [type] $params   [description]
     * @param  array  $fileInfo [description]
     * @param  array  $file     [description]
     * @return [type]           [description]
     */
    public static function actionUpImg($params = null, $fileInfo = [], $file = [])
    {
        $error  = '上传图片出错了';
        $params = W2Params::instancePost();

        if(empty($fileInfo))
        {
            $token = $params->getString('token');
            if(empty($token))
            {
                return HaoResult::initError('无效token');
            }
        }
        $fileInfo = empty($fileInfo) ? W2AES::aesDecrypt($token): $fileInfo;
        if(empty($fileInfo['filePath']))
        {
            return HaoResult::initError('token已失效');
        }

        empty($_FILES['file']) && $_FILES['file'] = null;
        $file = empty($file) ? $_FILES['file'] : $file;
        // 检查图片资源是否正常
        if(empty($file))
        {
            return HaoResult::initError('请选择图片后上传');
        }
        else
        {
            $errorCode = $file['error'];
            $upfile    = $file;
        }

        $fileInfo = W2File::getFilePath($fileInfo['filePath']);
        $upPath   = empty($fileInfo['upPath']) ? '': $fileInfo['upPath'];

        if($fileInfo['fileExists'] === false)
        {
            if(empty($errorCode) && !empty($upfile["tmp_name"]) )
            {
                W2File::mkDirs($upPath);
                move_uploaded_file($upfile["tmp_name"], $upPath);
            }
            else
            {
               $error = W2File::getUpFileError($errorCode);
            }
        }

        $cropSizeList = static::handlerCropSizeList($upPath);

        $fileInfo = W2File::getFilePath($fileInfo['filePath']);
        if($fileInfo['fileExists'] === true)
        {
            $haoResult =  HaoResult::initOk($fileInfo);
            return $haoResult;
        }

        return HaoResult::initError($error);
    }

    /** [handlerCropSizeList 批量裁剪图片] */
    public static function handlerCropSizeList($upPath ='')
    {
        $cropSizeList = static::loadCropSizeList();
        $cropImgList = [];
        if(!empty($cropSizeList) && !empty($upPath))
        {
            $pathinfo = pathinfo($upPath);
            if(!empty($pathinfo['filename']))
            {
                foreach ($cropSizeList as $key => $value)
                {
                    $quality = isset($value['q']) ? $value['q'] : 100;
                    $width   = $value['w'];
                    $height  = $value['h'];
                    $imgPath = "{$pathinfo['filename']}_{$width}x{$height}.{$pathinfo['extension']}";
                    $newPath = "{$pathinfo['dirname']}/{$imgPath}";
                    $cropImgList[] = $imgPath;
                    if(!file_exists($newPath))
                    {
                        $makeImg = W2Image::makePhotoThumb($upPath,$newPath,$width,$height,$quality);
                    }
                }
            }
        }
        return $cropImgList;
    }

    /** [loadCropSizeList 获取裁剪数据] */
    public static function loadCropSizeList()
    {
        $cropSizeList = [];
        if(!empty($_REQUEST['crop_size']))
        {
            $cropSizeList = Utility::jsonDecode($_REQUEST['crop_size']);
        }
        // D($cropSizeList);
        // D($_REQUEST);
        // die;
        return $cropSizeList;
    }
    /** [actionGetImgInfo 获取上传的token] */
    public static function actionGetImgInfo($params = null,$isGetPath = false, $filePath = '')
    {
        is_null($params) && $params = W2Params::instanceGet();

        // 不能为空参数
        $required =
        [
            // 'path_type' => '路径类型',
            'file_md5'  => '文件md5值',
            'file_size' => '文件大小',
            'file_type' => '文件类型',
        ];
        if(!empty($filePath))
        {
            unset($required['path_type']);
        }

        $haoResult = $params->checkRequired($required);
        if(!$haoResult->isResultsOK()) return  $haoResult;

        if(empty($filePath))
        {
            $date     = date('Ymd');
            $filePath = "{$date}/{$params->getString('fileMd5')}_{$params->getString('fileSize')}.{$params->getString('fileType')}";
        }

        $fileInfo = W2File::getFilePath($filePath);
        if($fileInfo['fileExists'] === false)
        {
            $fileInfo['token'] = $isGetPath ? ['filePath' => $filePath] : W2AES::aesEncrypt(['filePath' => $filePath]);
        }
        else
        {
            $fileInfo['token'] = '';
        }
        $haoResult = HaoResult::initOk($fileInfo);
        return $haoResult;
    }

    public static function upBase64($base64 = '', $pathType = '')
    {
        $fileInfo              = [];
        $fileInfo['base64']    = $base64;
        $fileInfo['path_type'] = $pathType;
        $params    = W2Params::instance($fileInfo);
        $haoResult = static::actionUpBase64($params);

        return $haoResult;
    }

    /**
     * [actionUpBase64 description]
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public static function actionUpBase64($params = null)
    {
        is_null($params) && $params = W2Params::instancePost();

        $haoResult = HaoResult::initError('上传图片出错了');

        $pathType = $params->getString('pathType');
        $base64   = $params->getString('base64');

        if(empty($pathType))
        {
            $haoResult->setErrorStr('图片类型有误');
            return $haoResult;
        }
        if(empty($base64))
        {
            $haoResult->setErrorStr('图片资源有误');
            return $haoResult;
        }

        $fileInfo             = W2File::getBase64ImgInfo($base64);
        $fileInfo['pathType'] = $pathType;
        $params               = W2Params::instance($fileInfo);
        $haoResult            = static::actionGetImgInfo($params,1);

        if($haoResult->isResultsOK())
        {
            $results = $haoResult->getResults();
            if(!empty($results['upPath']))
            {
                W2File::mkDirs($results['upPath']);
                file_put_contents($results['upPath'], $fileInfo['file_data']);
                $haoResult = static::actionGetImgInfo($params,1);
            }
        }

        return $haoResult;
    }

    /**
     * [actionUpImgFile 单独一次性上传图片]
     * @param  array  $file     [description]
     * @param  string $pathType [description]
     * @return [type]           [description]
     */
    public static function actionUpImgFile($file = [], $pathType = '')
    {
        $fileInfo  = W2File::getFileInfo($file);
        // $fileInfo['pathType'] = $pathType;
        $params    = W2Params::instance($fileInfo);
        $haoResult = static::actionGetImgInfo($params,1);

        if($haoResult->isResultsOK())
        {
            $results = $haoResult->getResults();
            if(!empty($results['token']))
            {
                $haoResult = static::actionupImg(null, $results['token'],$file);
            }
        }
        return $haoResult;
    }


    /** [actionGetVersions 检查版本更新] */
    public static function actionGetVersions()
    {
        $clientVersion   = Utility::getHeaderValue('clientversion');
        $devicetype      = Utility::getHeaderValue('devicetype');

        $iosVersions     = 0;
        $androidVersions = 0;
        $versions        =  [ 'versions' => 0, 'description' => "1 优化体验 \n 2 改善BUG", 'url' => '', 'type' => 0, 'status' => 0 ];

        if(class_exists('VersionsHandler'))
        {
            $versionsModel = VersionsHandler::loadModelFirstInList(['client' => $devicetype], ' id desc ');
            if(is_object($versionsModel))
            {
                $nVersions = $versionsModel->getVersions();
                if(version_compare($clientVersion, $nVersions, '<'))
                {
                    $versions['type']        = $versionsModel->getType();
                    $versions['versions']    = $nVersions;
                    $versions['url']         = $versionsModel->getUrl();
                    $versions['description'] = $versionsModel->getDescription();
                }
            }
        }

        $versions['description']  = $versions['type'] == 0 ? '此设备无需更新' : $versions['description'] ;
        $haoResult = HaoResult::initOk($versions);
        return $haoResult;
    }

    /*public static function actionGetVersions()
    {
        $clientVersion   = Utility::getHeaderValue('clientversion');
        $devicetype      = Utility::getHeaderValue('devicetype');

        $iosVersions     = 1.54;
        $androidVersions = 1.54;

        $versions        =  [ 'versions' => '', 'description' => "1 优化体验 \n 2 改善BUG", 'url' => '', 'type' => '0', 'status' => '0' ];
        if  ( $devicetype == DEVICE_TYPE::IOS &&  version_compare($clientVersion, $iosVersions, '<') )
        {
            $versions['type']        = 2;
            $versions['versions']    = $iosVersions;
            $versions['url']         = 'https://itunes.apple.com/us/app/quan-qiu-lu-pai/id1204634597?l=zh&ls=1&mt=8';
            $versions['description'] = " 最新版：{$iosVersions}\n 1 优化用户操作体验 \n 2 修复了一些功能BUG";
        }
        else if ( $devicetype == DEVICE_TYPE::ANDROID  &&  version_compare($clientVersion, $androidVersions, '<') )
        {
            $versions['type']        = 2;
            $versions['versions']    = $androidVersions;
            $versions['url']         = 'http://www.pgyer.com/Ynoo';
            $versions['description'] = " 最新版：{$androidVersions}\n 1 优化用户操作体验 \n 2 修复了一些功能BUG";
        }

        $versions['description']  = $versions['type'] == 0 ? '此设备无需更新' : $versions['description'] ;

        $haoResult = HaoResult::initOk($versions);
        return $haoResult;
    }*/

    public static function actionWxJsSign($params = null)
    {
        is_null($params) && $params = W2Params::instanceGet();

        // 不能为空参数
        $required =
        [
            'url' => '当前页面URL',
        ];

        $haoResult = $params->checkRequired($required);
        if(!$haoResult->isResultsOK()) return  $haoResult;
        // $url =  'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
        $url = $params->getString('url');
        $results = W2Wechat::getInstance()->getJsSign($url);
        $haoResult = HaoResult::initOk($results);
        return $haoResult;

    }

    public static function actionPutAction($params = null)
    {
        is_null($params) && $params = W2Params::instancePost();

        // 行为名称    行为编码
        // 信息浏览    view_activity
        // 关注公众号   subscribe_dww
        // 取消关注公众号 unsubscribe_dww
        // 注册用户    register_dww
        // 点赞项目    somall_click_like
        // 支持项目    somall_support
        // 评论项目    somall_comment
        // 投递职位    delivery_position
        // 注册简历-step1  create_resume_baseinfo
        // 注册简历-step2  create_resume_education
        // 注册简历-step3  create_resume_work
        // 注册简历-step4  create_resume_intention


        $actionType = $params->getString('actionType');

        $action = [];
        $action['actionType']   = $actionType;
        $action['openid']       = $params->getString('openid');
        $action['sourceOpenid'] = $params->getString('sourceOpenid');
        $action['sourceUserID'] = $params->getString('sourceUserID');
        $action['activityKey']  = $params->getString('activityKey');
        $action['activityID']   = $params->getString('activityID');
        $action['userID']       = Utility::getCurrentUserID();
        $action['agent']        = empty($_SERVER['HTTP_USER_AGENT']) ? '': $_SERVER['HTTP_USER_AGENT'];

        switch ($actionType)
        {
            case 'somall_click_like':
            case 'somall_comment':
            case 'somall_project_view':
                // $action['actionName'] = '点赞项目';
                $action['activityID']  = $params->getInt('id');
                break;
            case 'delivery_position':
                $action['activityID']  = $params->getInt('position_id');
                break;

            default: break;
        }
        file_put_action($action);
        return HaoResult::initOk([]);
    }


    /** [actionExpress 快递查询] */
    public static function actionExpress($params = null)
    {
        is_null($params) && $params = W2Params::instanceGet();

        // 不能为空参数
        $required =
        [
            'code'   => '快递编码',
            'number' => '快递单号',
        ];

        $haoResult = $params->checkRequired($required);
        if(!$haoResult->isResultsOK()) return  $haoResult;

        $results   = W2Express::query($params->getString('code'),$params->getString('number'));
        $haoResult = HaoResult::initOk($results);
        return $haoResult;
    }


    public static function actionSendMsg($params = null)
    {
        is_null($params) && $params = W2Params::instancePost();

        // 不能为空参数
        $required =
        [
            'message'   => '消息内容',
            'user_id'   => '用户标识ID',
            'send_type' => '发送类型',
        ];

        $haoResult = $params->checkRequired($required);
        if(!$haoResult->isResultsOK()) return  $haoResult;

        $haoResult = HaoResult::initError('发送消息失败');

        $userID     = $params->getString('userID');
        $message    = $params->getString('message');
        $sendType   = $params->getString('sendType');
        $sendStatus = false;
        $sendRes    = [];

        $sendType = explode(',', $sendType);
        foreach ($sendType as $key => $value)
        {
            switch ($value)
            {
                case 'sms':
                    $pubUserModel = PubUserHandler::loadModelById($userID);
                    if(is_object($pubUserModel) && !empty($pubUserModel->getMobile()))
                    {
                        include_once(AXAPI_ROOT_PATH . '/lib/HTTP_SDK.php');
                        $cpid      = SMS_USER_NAME;
                        $cppsw     = SMS_USER_PWD;
                        $engine    = HTTP_SDK::getInstance($cpid,$cppsw);
                        // $message = '你的手机号为' . 15121032753 . ',验证码为' . 1232 .'【大文网】';
                        $smsMessage = "{$message}【工厂有约】";
                        $sendStatus = $engine->pushMts($pubUserModel->getMobile(),$smsMessage);
                    }
                    break;
                    case 'wechat':
                        $sendRes = UnionLoginHandler::sendMassMessageByUserId($userID,$message);
                    break;

                default:
                    break;
            }
        }


        $res               = [];

        $res['sendStatus'] = $sendStatus;
        $res['sendStatusLabel'] = $sendStatus === '0' ? '短信发送成功': '短信发送失败';

        $countSendRes        = count($sendRes);
        $res['sendRes']      = $sendRes;
        $res['sendResLabel'] = "发送{$countSendRes}个微信帐号";

        $haoResult         = HaoResult::initOk($res);

        return $haoResult;
    }


    public static function actionGetAttributeType($params = null)
    {
        is_null($params) && $params = W2Params::instanceGet();
        $attributeTypeList = [];

        $modelName = $params->getString('modelName');

        $attributeName = $params->getString('attributeName');
        $attributeValue = $params->getString('attributeValue');
        empty($attributeName) && $attributeName = null;
        empty($attributeValue) && $attributeValue = null;

        $modelName = "{$modelName}Handler";
        if( method_exists($modelName, 'loadAttributeTypeModel') )
        {
            $attributeTypeList = $modelName::loadAttributeTypeModel($attributeName, $attributeValue);
        }

        if(!empty($params->getString('isGroup')))
        {
            $attributeTypeModelList = [];
            foreach ($attributeTypeList as $attributeType)
            {
                $attributeTypeModelList[$attributeType->getAttributeName()][] = $attributeType;
            }
            $attributeTypeList = $attributeTypeModelList;
        }

        $haoResult = HaoResult::initOk($attributeTypeList);
        return $haoResult;

    }

}