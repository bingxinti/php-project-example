<?php
// if(!empty($_REQUEST['debug']))
// {
//     phpinfo();
//     exit;
// }

ini_set('display_errors',1);            //错误信息
ini_set('display_startup_errors',1);    //php启动错误信息

error_reporting(-1);                    //打印出所有的 错误信息

date_default_timezone_set('Asia/Shanghai');//设定时区

define("AX_TIMER_START", microtime (true));//记录请求开始时间


    //加载配置文件
    require_once(__dir__.'/../config.php');

    //数据库操作工具
    require_once(AXAPI_ROOT_PATH.'/lib/DBTool/DBModel.php');

    //加载基础方法
    require_once(AXAPI_ROOT_PATH.'/components/Utility.php');

    // ajax跨域访问授权
    Utility::ajaxCrossDomain();

    AX_DEBUG('start');

    // if(!function_exists('getallheaders'))
    // {
    //     function getallheaders()
    //     {
    //         return array();
    //     }
    // }

    /**
     * 插入日志，之所以方法放这里，是因为index.php代码改动最少，这个方法存活率最高，因为是用来记日志的嘛。
     * @param  string|array $p_content [description]
     * @param  string $p_type    类型，用于组成文件名
     * @param  int $userID    指定用户ID，则不在查询数据库
     */
    function file_put_log($p_content='',$p_type='access', $userID = null)
    {
        if(is_null($userID))
        {
            $userID = Utility::getIntCurrentUserID();
        }
        $_api_url = $GLOBALS['_api_url'];
        file_put_contents(sprintf('%s/%s-%s.log'
                            ,AXAPI_ROOT_PATH.'/logs/'
                            ,$p_type
                            ,strftime('%Y%m%d'))
                            ,sprintf("[%s] [%s] [%s] [%d] [%s] [%d] [%s] [%s]: %s\n"
                                        ,W2Time::microtimetostr(AX_TIMER_START)
                                        ,number_format(microtime (true) - AX_TIMER_START, 5, '.', '')
                                        ,Utility::getCurrentIP()
                                        ,Utility::getHeaderValue('Devicetype')
                                        ,Utility::getHeaderValue('Clientversion')
                                        ,$userID
                                        ,count($_POST)>0?'POST':'GET'
                                        ,$_api_url
                                        ,is_string($p_content)?$p_content:Utility::json_encode_unicode($p_content)
                                    )
                            ,FILE_APPEND);



    }

    function add_action_log()
    {
        $userID = Utility::getIntCurrentUserID();
        $apiUrl = $GLOBALS['_api_url'];
        if(empty($apiUrl) || $apiUrl =='/')
        {
            return '';
        }
        $default =
        [
            'second'     => str_replace(' Asia/Shanghai','',W2Time::microtimetostr(AX_TIMER_START)),
            'time'       => number_format(microtime (true) - AX_TIMER_START, 5, '.', ''),
            'ip'         => Utility::getCurrentIP(),
            'deviceType' => Utility::getHeaderValue('devicetype'),
            'version'    => Utility::getHeaderValue('clientversion'),
            'signature'  => Utility::getHeaderValue('signature'),
            'apiName'    => $apiUrl,
            'userID'     => $userID,
            'params'     => Utility::jsonEncode($_REQUEST),
            'agent'      => empty($_SERVER['HTTP_USER_AGENT']) ? '': $_SERVER['HTTP_USER_AGENT'],
            'method'     => empty($_SERVER['REQUEST_METHOD']) ? (count($_POST) ? 'POST': 'GET'): $_SERVER['REQUEST_METHOD'],
        ];

        $model = new DBModel('actionLog');
        $model->insert($default);
        // D($default);
    }

    // 追加写文件
    function file_put_content($p_content ='', $p_type='other')
    {
        $path = sprintf('%s/%s-%s.log'
                            ,AXAPI_ROOT_PATH.'/logs/'
                            ,$p_type
                            ,strftime('%Y%m%d'));

        file_put_contents($path, $p_content, FILE_APPEND);
    }

    // 统计日志
    function file_put_action($arrLog = array())
    {
            // 'actionName'         => '',
            // 'actionKey'          => '',

        $default =
        [
            'event'         => 'ACTION_LOG',
            'time'          => str_replace(' Asia/Shanghai','',W2Time::microtimetostr(AX_TIMER_START)),
            'ip'            => Utility::getCurrentIP(),
            // 'devicetype' => Utility::getHeaderValue('devicetype'),
            'devicetype'    => 'H5',

            'actionType'    => '',

            'openid'        => '',
            'userID'        => '',

            'sourceOpenid'  => '',
            'sourceUserID'  => '',

            'activityKey'   => '',
            'activityID'    => '',

            // 设备ID
            'deviceid'      => '',

            'agent'         => '',
        ];

        // 日志类型 ,时间戳.毫秒 ,IP ,终端类型 ,行为类型 ,当前用户openid ,当前用户ID ,来源用户openid ,来源用户ID ,活动key标识 ,活动ID ,设备ID，浏览器标识（agent）

        $content = Utility::getStrPutActionLog($default, $arrLog);
        file_put_content( $content . "\n", 'action');
    }

    /**
     * 主要用于捕捉致命错误，每次页面处理完之后执行检查
     * @return [type] [description]
     */
    function catch_fatal_error()
    {
      // Getting Last Error
       $last_error =  error_get_last();

        // Check if Last error is of type FATAL
        if(isset($last_error['type']))
        {
            // Fatal Error Occurs
            // Do whatever you want for FATAL Errors
            $errorMsg = null;
            switch ($last_error['type']) {
                case E_ERROR:
                    $errorMsg = '严重错误：服务器此时无法处理您的请求，请稍后或联系管理员。';
                    break;
                case E_PARSE:
                    $errorMsg = '代码拼写错误：是Peter干的吗，请向管理员举报Peter。';
                    break;
                case E_WARNING:
                    $errorMsg = '警告：出现不严谨的代码逻辑，请告知管理员这个问题。';
                    break;
                case E_NOTICE:
                    $errorMsg = '警告：出现不严谨的代码逻辑，请告知管理员这个问题。';
                    break;
            }

            if(AXAPI_DEPLOY_STATUS === 1)
            {
                $errorMsg = Utility::json_encode_unicode($last_error);
            }


            if (!is_null($errorMsg))
            {
                //记录错误日志
                file_put_log($_REQUEST,'error');
                file_put_log($last_error,'error');

                //返回错误信息
                @ob_end_clean();//要清空缓冲区， 从而删除PHPs " 致命的错误" 消息。
                $results = HaoResult::init(array(RUNTIME_CODE_ERROR_UNKNOWN,$errorMsg,$errorMsg),null,defined('IS_AX_DEBUG')?array('errorContent'=>'Error on line '.$last_error['line'].' in '.$last_error['file'].': '.$last_error['message'].''):null);
                echo Utility::json_encode_unicode($results->properties());
                exit;
            }
        }

    }
    register_shutdown_function('catch_fatal_error');

    if(!empty($_REQUEST['r']))
    {
        $apiPaths = explode('/',$_REQUEST['r']);
    }
    else
    {
        $apiPaths = explode('/', preg_replace ("/(\/*[\?#].*$|[\?#].*$|\/*$)/", '', $_SERVER['REQUEST_URI']));
    }
    if (count($apiPaths)<3)
    {
        list ($apiController, $apiAction) = explode ("/", W2HttpRequest::getRequestString('r',false,'/'), 2);
    }
    else
    {
        $apiController = $apiPaths[1];
        $apiAction = $apiPaths[2];
    }

    $_api_url = "{$apiController}/{$apiAction}";
    // D($_api_url);

    //接口格式校验
    $results = Utility::getAuthForApiRequest();
    if ($results->isResultsOK() || in_array($_api_url, [
        // 支付回调
        'pay_alipay_log/notifyPay',
    ]))
    {
        //调用对应接口方法
        try {

            // $method = new ReflectionMethod(W2String::camelCase($apiController.'Controller'), W2String::camelCase('action'.$apiAction));
            // $results = $method->invoke(null,0);
            $apiController = W2String::camelCase($apiController.'Controller');
            $apiAction     = W2String::camelCase('action'.$apiAction);
            // DD($apiController,$apiAction, method_exists($apiController,$apiAction));
            if (method_exists($apiController,$apiAction))
            {
                $results = $apiController::$apiAction();

                // $results = SysConfigController::checkBanStatus();
                // if($results->isResultsOK())
                // {
                //     $results = $apiController::$apiAction();
                // }
            }
            else
            {
                $results = HaoResult::init(ERROR_CODE::$UNKNOWN_API_ACTION);
            }
        } catch (Exception $e) {
            $results = HaoResult::init(array($e->getCode()==0?RUNTIME_CODE_ERROR_UNKNOWN:$e->getCode(),$e->getMessage(),$e->getMessage()),null,defined('IS_AX_DEBUG')?array('errorContent'=>'Error on line '.$e->getLine().' in '.$e->getFile().': '.$e->getMessage().''):null);
        }
    }

    $errorCode = 0;
    $errorStr  = '';
    //打印接口返回的数据
    if (is_object($results) && get_class($results) == 'HaoResult' )
    {
        $errorCode = $results->errorCode;
        $errorStr  = $results->getErrorStr();
        if (!defined('IS_AX_DEBUG'))
        {
            header('Content-Type:application/json; charset=utf-8');
        }

        HaoResult::$findPaths   = W2HttpRequest::getRequestArray('find_paths');
        HaoResult::$searchPaths = W2HttpRequest::getRequestArray('search_paths');
        echo Utility::json_encode_unicode($results->properties());
    }
    else if (is_string($results))
    {
        echo $results;
    }
    else
    {
        echo Utility::json_encode_unicode($results);
    }

    //记录接口日志
    file_put_log(Utility::json_encode_unicode($_REQUEST) . " [{$errorCode}:{$errorStr}]",'access');

    // 记录数据库日志
    // add_action_log();

