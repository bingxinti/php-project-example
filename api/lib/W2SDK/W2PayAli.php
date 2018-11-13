<?php
/**
 * 支付宝、微信服务号、微信开放平台、银联支付
 * @package W2
 * @author axing
 * @since 1.0
 * @version 1.0
 */

class W2PayAli {
/**
使用以下命令可以生成密钥和公钥
cd alipay/key
openssl
genrsa -out rsa_private_key.pem 1024
rsa -in rsa_private_key.pem -pubout -out rsa_public_key.pem
*/
    public static $PARTNER                      = null;  //PID 在 https://b.alipay.com/order/pidAndKey.htm
    public static $SELLER_ID                    = null;
    public static $MD5_KEY                      = null;
    public static $ACCOUNT_NAME                 = null;
    public static $PRIVATE_KEY_PATH             = null;
    public static $ALI_PUBLIC_KEY_PATH          = null;
    public static $NOTIFY_URL                   = null;
    public static $NOTIFY_URL_OF_REFUND         = null;
    public static $NOTIFY_URL_OF_TRANS          = null;

    public static $alipay_gateway_new           = 'https://mapi.alipay.com/gateway.do?';
    /**
     * 计算支付宝支付用的订单信息字符串
     * @param  [type] $out_trade_no 商户网站唯一订单号
     * @param  [type] $subject      商品名称
     * @param  [type] $body         商品详情
     * @param  [type] $total_fee    商品金额（单位：元）
     * @return string               订单信息字符串
     * 举例： partner="2088811898816095"&seller_id="qqlp62193@163.com"&out_trade_no="HaoFrame.136"&subject="应用开发服务费"&body="上海浩兮信息技术有限公司"&total_fee="0.01"&notify_url="qqlp62193@163.com"&service="mobile.securitypay.pay"&payment_type="1"&_input_charset="utf-8"&it_b_pay="30m"&return_url="m.alipay.com"&sign="kUXkkyviowVSfIu2NmfnPERq2M6VsdyE2igVnGR9fMkc45G%2FwmK%2BfBm3uCFLLKSuRW%2FgD6SL9lM0nkZVoL3iotVHA40psz3y6A%2F2uOsizvxTjtRXz4q4qUCWy%2B3L%2FLaVrhUTqwo7TF%2BPawVdjuGQkmNSd6KhQZ27MQRMTzJ%2Fxyo%3D"&sign_type="RSA"
     */
    public static function getPayInfo($out_trade_no,$subject,$body,$total_fee)
    {
        $PARTNER          = static::$PARTNER;
        $SELLER           = static::$SELLER_ID;

        $private_key_path = static::$PRIVATE_KEY_PATH;
        $notify_url       = static::$NOTIFY_URL;

        $orderInfo        = 'partner="' . $PARTNER . '"';

        // 签约卖家支付宝账号
        $orderInfo    .= '&seller_id="' . $SELLER . '"';

        // 商户网站唯一订单号
        $orderInfo    .= '&out_trade_no="' . $out_trade_no . '"';

        // 商品名称
        $orderInfo    .= '&subject="' . $subject . '"';

        // 商品详情
        $orderInfo    .= '&body="' .  $body . '"';

        // 商品金额
        $orderInfo    .= '&total_fee="' .$total_fee  . '"';
        // $orderInfo .= '&total_fee="' .'0.01'  . '"';

        // 服务器异步通知页面路径
        $orderInfo    .= '&notify_url="' . $notify_url . '"';

        // 服务接口名称， 固定值
        $orderInfo    .= '&service="mobile.securitypay.pay"';

        // 支付类型， 固定值
        $orderInfo    .= '&payment_type="1"';

        // 参数编码， 固定值
        $orderInfo    .= '&_input_charset="utf-8"';

        // 设置未付款交易的超时时间
        // 默认30分钟，一旦超时，该笔交易就会自动被关闭。
        // 取值范围：1m～15d。
        // m-分钟，h-小时，d-天，1c-当天（无论交易何时创建，都在0点关闭）。
        // 该参数数值不接受小数点，如1.5h，可转换为90m。
        $orderInfo    .= '&it_b_pay="30m"';

        // extern_token为经过快登授权获取到的alipay_open_id,带上此参数用户将使用授权的账户进行支付
        // $orderInfo .= '&extern_token="' . extern_token . '"';

        // 支付宝处理完请求后，当前页面跳转到商户指定页面的路径，可空
        $orderInfo    .= '&return_url="m.alipay.com"';

        // 调用银行卡支付，需配置此参数，参与签名， 固定值 （需要签约《无线银行卡快捷支付》才能使用）
        // $orderInfo .= '&paymethod="expressGateway"';

        //使用客户的密钥加密
        $sign    = urlencode(W2RSA::rsaSign($orderInfo, static::$PRIVATE_KEY_PATH));
        $payInfo = $orderInfo . '&sign="' . $sign . '"&' . 'sign_type="RSA"';

        return $payInfo;
    }

    /**
     * 退款
     * @param  string $batch_no     每进行一次即时到账批量退款，都需要提供一个批次号，通过该批次号可以查询这一批次的退款交易记录，对于每一个合作伙伴，传递的每一个批次号都必须保证唯一性。格式为：退款日期（8位）+流水号（3～24位）。不可重复，且退款日期必须是当天日期。流水号可以接受数字或英文字符，建议使用数字，但不可接受“000”。
     * @param  string $trade_no 原付款支付宝交易号
     * @param  string $refund_fee   退款金额
     * @param  string $desc         退款理由；
     * @return array               {'url':'http://xxx','formData':{...}}
     */
    public static function refundSingle($batch_no,$trade_no,$refund_fee,$desc)
    {
        $refundList = array(
                array(
                         'trade_no'=>$trade_no
                        ,'refund_fee'  =>$refund_fee
                        ,'desc'        =>$desc
                    )
            );
        return static::refundMany($batch_no,$refundList);
    }
    /**
     * 退款
     * @param  string $batch_no     每进行一次即时到账批量退款，都需要提供一个批次号，通过该批次号可以查询这一批次的退款交易记录，对于每一个合作伙伴，传递的每一个批次号都必须保证唯一性。格式为：退款日期（8位）+流水号（3～24位）。不可重复，且退款日期必须是当天日期。流水号可以接受数字或英文字符，建议使用数字，但不可接受“000”。
     * @param  array  $refundList  支付清单 [ {'trade_no':'xxx','refund_fee':'xxx','desc':'xxx'},{},... ]
     * @return array               {'url':'http://xxx','formData':{...}}
     */
    public static function refundMany($batch_no,$refundList=array())
    {
        $detail_data = array();
        foreach ($refundList as $refundData) {
            $detail_data[] = $refundData['trade_no'] . '^' . $refundData['refund_fee'] . '^' . $refundData['desc'];
        }
        $detail_data = implode('#',$detail_data);
        return static::refund($batch_no,$detail_data);
    }
    //https://doc.open.alipay.com/doc2/detail.htm?spm=a219a.7629140.0.0.8jmjPJ&treeId=66&articleId=103600&docType=1
    public static function refund($batch_no,$detail_data)
    {
        $xmlArray = array();

        $xmlArray['service']        = 'refund_fastpay_by_platform_pwd';  // 服务接口名称， 固定值
        $xmlArray['partner']        = static::$PARTNER;                  //PID 签约的支付宝账号对应的支付宝唯一用户号。以2088开头的16位纯数字组成。
        $xmlArray['_input_charset'] = 'utf-8';                           //参数编码， 固定值
        $xmlArray['seller_email']   = static::$SELLER_ID;                // 签约卖家支付宝账号
        $xmlArray['notify_url']     = static::$NOTIFY_URL_OF_REFUND;     // 服务器异步通知页面路径

        $xmlArray['refund_date']    = date('Y-m-d H:i:s');               // 退款请求的当前时间。格式为：yyyy-MM-dd hh:mm:ss。
        $xmlArray['batch_no']       = $batch_no;                         // 每进行一次即时到账批量退款，都需要提供一个批次号，通过该批次号可以查询这一批次的退款交易记录，对于每一个合作伙伴，传递的每一个批次号都必须保证唯一性。格式为：退款日期（8位）+流水号（3～24位）。不可重复，且退款日期必须是当天日期。流水号可以接受数字或英文字符，建议使用数字，但不可接受“000”。
        $xmlArray['batch_num']      = substr_count($detail_data,'#')+1;  // 即参数detail_data的值中，“#”字符出现的数量加1，最大支持1000笔（即“#”字符出现的最大数量为999个）。
        $xmlArray['detail_data']    = $detail_data;                      // 退款请求的明细数据。格式详情参见下面的“单笔数据集参数说明”。
/*
单笔数据集参数说明
单笔数据集格式为：第一笔交易退款数据集#第二笔交易退款数据集#第三笔交易退款数据集…#第N笔交易退款数据集；
交易退款数据集的格式为：原付款支付宝交易号^退款总金额^退款理由；
不支持退分润功能。
单笔数据集（detail_data）注意事项
detail_data中的退款笔数总和要等于参数batch_num的值；
“退款理由”长度不能大于256字节，“退款理由”中不能有“^”、“|”、“$”、“#”等影响detail_data格式的特殊字符；
detail_data中退款总金额不能大于交易总金额；
一笔交易可以多次退款，退款次数最多不能超过99次，需要遵守多次退款的总金额不超过该笔交易付款金额的原则。
*/

        $orderString             = static::createLinkstring($xmlArray);
        $xmlArray['sign']        = W2RSA::rsaSign($orderString, static::$PRIVATE_KEY_PATH);                             //签名方式
        $xmlArray['sign_type']   = 'RSA';                                   //签名方式

        $result = array();
        $result['url'] = static::$alipay_gateway_new.'_input_charset='.trim(strtolower($xmlArray['_input_charset']));
        $result['formData'] = $xmlArray;
        return $result;

    }



    /**
     * 付款给单个支付宝账号
     * 操作者的电脑需要安装支付宝数字证书哦
     * @param  int    $batch_no     批量付款批次号。11～32位的数字或字母或数字与字母的组合，且区分大小写。注意：批量付款批次号用作业务幂等性控制的依据，一旦提交受理，请勿直接更改批次号再次上传。
     * @param  string $trans_no      流水号1
     * @param  string $trans_account 收款方账号1
     * @param  string $trans_name    收款账号姓名1
     * @param  bool   $trans_fee     付款金额1
     * @param  string $trans_desc    备注说明1
     * @return array               {'url':'http://xxx','formData':{...}}
     */
    public static function batchTransSingle($batch_no,$trans_no,$trans_account,$trans_name,$trans_fee,$trans_desc)
    {
        $transList = array(
                array(
                         'trans_no'         =>$trans_no
                        ,'trans_account'    =>$trans_account
                        ,'trans_name'       =>$trans_name
                        ,'trans_fee'        =>$trans_fee
                        ,'trans_desc'       =>$trans_desc
                    )
            );
        return static::batchTransMany($batch_no,$transList);
    }
    /**
     * 批量付款给多个支付宝账号
     * @param  string $batch_no
     * @param  array  $transList  支付清单 [ {'trans_no':'xxx','trans_account':'xxx','trans_name':'xxx','trans_fee':'xxx','trans_desc':'xxx'},{},... ]
     * @return array               {'url':'http://xxx','formData':{...}}
     */
    public static function batchTransMany($batch_no,$transList=array())
    {
        $detail_data = array();
        foreach ($transList as $transData) {
            $detail_data[] = $transData['trans_no'] . '^' . $transData['trans_account'] . '^' . $transData['trans_name'] . '^' . $transData['trans_fee'] . '^' . $transData['trans_desc'];
        }
        $detail_data = implode('|',$detail_data);
        return static::batchTrans($batch_no,$detail_data);
    }
    //https://doc.open.alipay.com/doc2/detail.htm?spm=a219a.7629140.0.0.Q5NyE3&treeId=64&articleId=103773&docType=1
    public static function batchTrans($batch_no,$detail_data)
    {
        $xmlArray = array();

        $xmlArray['service']        = 'batch_trans_notify';              // 服务接口名称， 固定值
        $xmlArray['_input_charset'] = 'utf-8';                           // 参数编码， 固定值
        $xmlArray['partner']        = static::$PARTNER;                  // PID 签约的支付宝账号对应的支付宝唯一用户号。以2088开头的16位纯数字组成。
        $xmlArray['notify_url']     = static::$NOTIFY_URL_OF_TRANS;      // 服务器异步通知页面路径
        $xmlArray['account_name']   = static::$ACCOUNT_NAME;             // 付款方的支付宝账户名。
        $xmlArray['email']          = static::$SELLER_ID;                // 付款方的支付宝账号。
        $xmlArray['batch_no']       = $batch_no;                         // 批量付款批次号。11～32位的数字或字母或数字与字母的组合，且区分大小写。注意：批量付款批次号用作业务幂等性控制的依据，一旦提交受理，请勿直接更改批次号再次上传。

        $xmlArray['pay_date']       = date('Ymd');                       // 支付时间（必须为当前日期）。 格式：YYYYMMDD。
        $xmlArray['detail_data']    = $detail_data;                      // 付款的详细数据，最多支持1000笔。 格式为：流水号1^收款方账号1^收款账号姓名1^付款金额1^备注说明1|流水号2^收款方账号2^收款账号姓名2^付款金额2^备注说明2。 每条记录以“|”间隔。
        $xmlArray['batch_num']      = substr_count($detail_data,'|')+1;  // 批量付款笔数（最多1000笔）。
        $batch_fee = 0;
        foreach (explode('|',$detail_data) as $detail) {
            list ($trans_no,$trans_account,$trans_name,$trans_fee,$trans_desc) = explode('^',$detail);
            $batch_fee += $trans_fee;
        }
        $xmlArray['batch_fee']      = $batch_fee;                        // 付款文件中的总金额。 格式：10.01，精确到分。


        $orderString             = static::createLinkstring($xmlArray);
        $xmlArray['sign']        = W2RSA::rsaSign($orderString, static::$PRIVATE_KEY_PATH);                             //签名方式
        $xmlArray['sign_type']   = 'RSA';                                   //签名方式
        // $xmlArray['sign']        = md5($orderString . static::$MD5_KEY);                             //签名方式
        // $xmlArray['sign_type']   = 'MD5';                                   //签名方式

        $result = array();
        $result['url'] = static::$alipay_gateway_new.'_input_charset='.trim(strtolower($xmlArray['_input_charset']));
        $result['formData'] = $xmlArray;
        return $result;

    }

    /**
     * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
     * @param $para 需要拼接的数组
     * return 拼接完成以后的字符串
     */
    public static function createLinkstring($para) {
        ksort($para);
        reset($para);
        $arg  = "";
        while (list ($key, $val) = each ($para)) {
            if($key != 'sign' && $key != 'sign_type' && !is_null($val) && !(is_array($val) && count($val)==0) ){
                $arg.=$key."=".$val."&";
            }
        }
        //去掉最后一个&字符
        $arg = substr($arg,0,count($arg)-2);

        //如果存在转义字符，那么去掉转义
        if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}

        return $arg;
    }

    /**
     * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串，并对字符串做urlencode编码
     * @param $para 需要拼接的数组
     * return 拼接完成以后的字符串
     */
    public static function createLinkstringUrlencode($para)
    {
        $arg  = "";
        while (list ($key, $val) = each ($para)) {
            if($key != 'sign' && $key != 'sign_type' && !is_null($val) && !(is_array($val) && count($val)==0) ){
                $arg.=$key."=".urlencode($val)."&";
            }
        }
        //去掉最后一个&字符
        $arg = substr($arg,0,count($arg)-2);

        //如果存在转义字符，那么去掉转义
        if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}

        return $arg;
    }

    /**
     * 判断POST数据是否可验支付宝家的公钥
     * @param  array $post   数据字典
     * @param  string $sign  校验串
     * @return bool       true
     */
    public static function getSignVeryfy($post=null,$sign=null)
    {
        if (!isset($post))
        {
            $post = $_POST;
        }
        if (!isset($sign) && isset($_POST['sign']))
        {
            $sign = $_POST['sign'];
        }
        $orderString = static::createLinkstring($post);
        if (isset($post['sign_type']))
        {
            switch ($post['sign_type']) {
                case 'RSA':
                    return W2RSA::rsaVerify($orderString,static::$ALI_PUBLIC_KEY_PATH,$sign);
                    break;
                case 'MD5':
                    return md5($orderString . static::$MD5_KEY) == $sign;
                    break;

                default:
                    # code...
                    break;
            }
        }
        return false;
    }

}

if (W2PayAli::$PARTNER==null && defined('W2PAYALI_PARTNER'))
{
    W2PayAli::$PARTNER                   = W2PAYALI_PARTNER;
    W2PayAli::$SELLER_ID                 = W2PAYALI_SELLER_ID;
    W2PayAli::$MD5_KEY                   = W2PAYALI_MD5_KEY;
    W2PayAli::$ACCOUNT_NAME              = W2PAYALI_ACCOUNT_NAME;
    W2PayAli::$PRIVATE_KEY_PATH          = W2PAYALI_PRIVATE_KEY_PATH;
    W2PayAli::$ALI_PUBLIC_KEY_PATH       = W2PAYALI_ALI_PUBLIC_KEY_PATH;
    W2PayAli::$NOTIFY_URL                = W2PAYALI_NOTIFY_URL;
    W2PayAli::$NOTIFY_URL_OF_REFUND      = W2PAYALI_NOTIFY_URL_OF_REFUND;
    W2PayAli::$NOTIFY_URL_OF_TRANS       = W2PAYALI_NOTIFY_URL_OF_TRANS;
}
