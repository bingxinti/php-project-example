<?php
/**
 * http请求处理函数库文件
 * @package W2
 * @author zb
 * @since 1.0
 * @version 1.0
 */
// class W2Params extends W2HttpRequest
class W2Params
{
    protected $params = [];

    public static function instance($arrParams = [])
    {
        $obj = new static();
        $params = [];
        foreach ($arrParams as $key => $value)
        {
            $params[ W2String::camelCase($key) ] = addslashes($value);
        }
        $obj->params = $params;
        return $obj;
    }


    /**
     * [instanceGet description]
     * @return [type] [description]
     */
    public static function instanceGet()
    {
        $params = static::instance($_GET);
        return $params;
    }

    public static function instancePost()
    {
        $params = static::instance($_POST);
        // $params = static::instance($_REQUEST);
        return $params;
    }

    /*public static function instanceRequest()
    {
        $params = static::instance($_REQUEST);
        return $params;
    }*/

    /** [isEmpty description] */
    public function isEmpty($key = '')
    {
        return empty($this->getParams()[$key]);
    }

    // 获取参数类型
    public function getInt($key = '', $default = 0)
    {
        $value = isset($this->params[$key]) ? intval($this->params[$key]) : $default;
        return $value;
    }

    public function getString($key = '', $default = '' )
    {
        $value = isset($this->params[$key]) ? strval($this->params[$key]) : $default;
        return $value;
    }

    public function getDateTime($key = '')
    {
       $value = $this->getString($key, null);
       return $value;
    }

    public function getParams()
    {
        return $this->params;
    }

    protected function setParams($params)
    {
        $this->params = $params;

        return $this;
    }

    /**
     * [setParamsKey description]
     * @param string $key   [description]
     * @param [type] $value [description]
     */
    public function setParamsKey($key = '',$value = null)
    {
        $key = W2String::camelCase($key);
        $this->params[$key] = $value;
        return $this;
    }

    /**
     * [checkRequired 接口检查必须的参数值]
     * @param  array  $params       [检查的参数数组]
     * @param  array  $actionRequired [检查的字段值]
     * @return [type]               [description]
     */
    public function checkRequired( $actionRequired = [] , $params = null, $error = '不能为空')
    {
        $haoResult = HaoResult::init(ERROR_CODE::$OK);
        $params = is_null($params) ? $this->getParams() : $params ;

        // D($params->getParams());
        // exit;

        if( !empty($actionRequired) )
        {
            foreach ($actionRequired as $key => $value)
            {
                $_key = W2String::camelCase($key);
                // 如果参数不存在此KEY，或者参数key是字符串就命中错误提示
                if(!array_key_exists($_key,$params) || (isset($params[$_key]) && $params[$_key] == ''))
                {
                    $strError =  isset($actionRequired[$key]) ? "{$value}" : "{$key}" ;
                    $strError .= $error;
                    $haoResult = HaoResult::initError($strError);
                    break;
                }
            }
        }
        return $haoResult;
    }
}