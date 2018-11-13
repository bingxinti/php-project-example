<?php
/**
 * 身份证处理
 * @package W2
 * @author Demon
 * @since 1.0
 * @version 1.0
 */

class W2IDCard
{
	public static function existsIdCard($idcard)
	{
		$isArea=static::isAreaCode($idcard);
		$isDate=static::isDate($idcard);
		$isIdCard= static::isIdCard($idcard);
		if (!preg_match("/^(\d{18,18}|\d{15,15}|\d{17,17}(?:\d|x|X))$/",$idcard) || !$isIdCard || !$isArea || !$isDate){
			return false;
		}
		return true;
	}


    /** [getSex 获取性别] */
    public static function getSex($cid)
    {
        //根据身份证号，自动返回性别
        if(!static::isIdCard($cid)) return '';
        $sexint = (int)substr($cid,16,1);
        return $sexint % 2 === 0 ? 0 : 1;
    }

    public static function getConstellationByDateTime($dateTime = '')
    {
        $dateTime = strtotime($dateTime);
        return static::getConstellationByMonth( date('m',$dateTime),date('d',$dateTime) );
    }

    public static function getConstellationByMonth($month = 0,$day=0)
    {
        $strValue = '';
        if(($month == 1 && $day <= 21) || ($month == 2 && $day <= 19)) {
            $strValue = "水瓶座";
        }else if(($month == 2 && $day > 20) || ($month == 3 && $day <= 20)) {
            $strValue = "双鱼座";
        }else if (($month == 3 && $day > 20) || ($month == 4 && $day <= 20)) {
            $strValue = "白羊座";
        }else if (($month == 4 && $day > 20) || ($month == 5 && $day <= 21)) {
            $strValue = "金牛座";
        }else if (($month == 5 && $day > 21) || ($month == 6 && $day <= 21)) {
            $strValue = "双子座";
        }else if (($month == 6 && $day > 21) || ($month == 7 && $day <= 22)) {
            $strValue = "巨蟹座";
        }else if (($month == 7 && $day > 22) || ($month == 8 && $day <= 23)) {
            $strValue = "狮子座";
        }else if (($month == 8 && $day > 23) || ($month == 9 && $day <= 23)) {
            $strValue = "处女座";
        }else if (($month == 9 && $day > 23) || ($month == 10 && $day <= 23)) {
            $strValue = "天秤座";
        }else if (($month == 10 && $day > 23) || ($month == 11 && $day <= 22)) {
            $strValue = "天蝎座";
        }else if (($month == 11 && $day > 22) || ($month == 12 && $day <= 21)) {
            $strValue = "射手座";
        }else if (($month == 12 && $day > 21) || ($month == 1 && $day <= 20)) {
            $strValue = "魔羯座";
        }
        return $strValue;
    }

    // PHP根据身份证号，自动获取对应的星座函数
    public static function getConstellation($cid) {
        // 根据身份证号，自动返回对应的星座
        if (!static::isIdCard($cid)) return '';
        $bir = substr($cid,10,4);
        $month = (int)substr($bir,0,2);
        $day = (int)substr($bir,2);
        $strValue = static::getConstellationByMonth($month,$day);
        return $strValue;
    }

    public static function getZodiac($cid)
    {
        //根据身份证号，自动返回对应的生肖
        if(!static::isIdCard($cid)) return '';
        $start = 1901;
        $end = $end = (int)substr($cid,6,4);
        $x = ($start - $end) % 12;
        $value = "";
        if($x == 1 || $x == -11){
            $value = "鼠";
        }
        if($x == 0) {
            $value = "牛";
        }
        if($x == 11 || $x == -1){
            $value = "虎";
        }
        if($x == 10 || $x == -2){
            $value = "兔";
        }
        if($x == 9 || $x == -3){
            $value = "龙";
        }
        if($x == 8 || $x == -4){
            $value = "蛇";
        }
        if($x == 7 || $x == -5){
            $value = "马";
        }
        if($x == 6 || $x == -6){
            $value = "羊";
        }
        if($x == 5 || $x == -7){
            $value = "猴";
        }
        if($x == 4 || $x == -8){
            $value = "鸡";
        }
        if($x == 3 || $x == -9){
            $value = "狗";
        }
        if($x == 2 || $x == -10){
            $value = "猪";
        }
        return $value;
    }

    public static function isIDCard($number)
    {
    	if (!preg_match("/^(\d{18,18}|\d{15,15}|\d{17,17}(?:\d|x|X))$/",$number))
    	{
    		return '';
    	}
        //检查是否是身份证号
        // 转化为大写，如出现x
        $number = strtoupper($number);
        if( !(strlen($number) == 15  || strlen($number) == 18) )
        {
            return false;
        }
        if(strlen($number) == 18){ //检查18位  
             //加权因子
            $wi = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
            //校验码串
            $ai = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');

            //按顺序循环处理前17位
            $sigma = 0;
            for($i = 0;$i < 17;$i++)
            {
                //提取前17位的其中一位，并将变量类型转为实数
                $b = (int) $number{$i};      //提取相应的加权因子
                $w = $wi[$i];     //把从身份证号码中提取的一位数字和加权因子相乘，并累加
                $sigma += $b * $w;
            }
            //计算序号
            $snumber = $sigma % 11;
            //按照序号从校验码串中提取相应的字符。
            $check_number = $ai[$snumber];
            if($number{17} == $check_number){
                return true;
            }else{
                return false;
            }
        }elseif(strlen($number) == 15){    //检查15位 
            $regx = "/^(\d{6})+(\d{2})+(\d{2})+(\d{2})+(\d{3})$/"; 
          
            @preg_match($regx, $number, $arr_split); 
            //检查生日日期是否正确 
            $dtm_birth = "19".$arr_split[2] . '/' . $arr_split[3]. '/' .$arr_split[4]; 
            if(!strtotime($dtm_birth)) 
            { 
              return FALSE; 
            } else { 
              return TRUE; 
            }
        }
    }
    public static function isAreaCode($idcard)
    {
        if(strlen($idcard)<3)
        {
            return '';
        }
    	$key = substr( $idcard,0,2);
    	$city = array(
    			"11" =>"北京",
    			"12"=>"天津",
    			"13"=>"河北",
    			"14"=>"山西",
    			"15"=>"内蒙古",
    			"21"=>"辽宁",
    			"22"=>"吉林",
    			"23"=>"黑龙江",
    			"31"=>"上海",
    			"32"=>"江苏",
    			"33"=>"浙江",
    			"34"=>"安徽",
    			"35"=>"福建",
    			"36"=>"江西",
    			"37"=>"山东",
    			"41"=>"河南",
    			"42"=>"湖北",
    			"43"=>"湖南",
    			"44"=>"广东",
    			"45"=>"广西",
    			"46"=>"海南",
    			"50"=>"重庆",
    			"51"=>"四川",
    			"52"=>"贵州",
    			"53"=>"云南",
    			"54"=>"西藏",
    			"61"=>"陕西",
    			"62"=>"甘肃",
    			"63"=>"青海",
    			"64"=>"宁夏",
    			"65"=>"新疆",
    			"71"=>"台湾",
    			"81"=>"香港",
    			"82"=>"澳门",
    			"91"=>"国外");
    	if (array_key_exists($key, $city)) {
    		return $city[$key];
    	}
    	return false ;
    }
    public static function getAge($idcard)
    {
    	$Year = substr($idcard,6, 4);// 年份
    	$Month = substr($idcard,10, 2);// 月份
    	$Day = substr($idcard,12, 2);// 天数

    	$age = date('Y') - $Year;
    	if (date('m') < $Month || (date('m') == $Month && date('d') < $Day)) $age--;
    	return $age;
    }

    public static function getBirthday($idcard)
    {
        $Year = substr($idcard,6, 4);// 年份
        $Month = substr($idcard,10, 2);// 月份
        $Day = substr($idcard,12, 2);// 天数
        return $Year.$Month.$Day;
    }

    public static function isDate($idcard)
    {
    	$Year = substr($idcard,6, 4);// 年份
    	$Month = substr($idcard,10, 2);// 月份
    	$Day = substr($idcard,12, 2);// 天数

    	$curr = date("Y");
    	$diff = ($curr- $Year);
    	if ( $diff > 150 || $diff<0) {
    		return false;
    	}
    	if ($Month>12 || $Month<1) {
    		return false;
    	}
    	if ($Day>31 || $Day<1) {
    		return false;
    	}
    	return true;
    }
}