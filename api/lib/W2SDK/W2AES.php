<?php

/**
 * zb
 */
class W2AES
{
	/**
	 *
	 * DD($kk = W2AES::aesEncrypt($filePath));
	 * DD(W2AES::aesDecrypt($kk));
	 *
	 */

	/** @var [type] [加密私钥KEY] */
    private static  $aes_encrypt_key = AES_ENCRYPT_KEY;
    private static  $aes_confuse_key = AES_CONFUSE_KEY;

    // 加密
    public static  function aesEncrypt ($value)
    {
        if (empty($value)) return '';
        $value = is_array($value) ? Utility::jsonEncode($value) : $value;
        $value .= static::enPassword($value);
        return  self::aesEncode($value);
    }

    // 解密
    public static  function aesDecrypt($value)
    {
        if (empty(trim($value))) return '';
        $aesCode = self::aesDecode($value);
        if  ( static::checkPassword( substr($aesCode,-32) ) )
        {
            $arrAesCode =   substr($aesCode,0,strlen($aesCode)-32) ;
            $arrCodeInfo = Utility::jsonDecode($arrAesCode);
            return  is_array($arrCodeInfo) ? $arrCodeInfo : $arrAesCode;
        }
        return null;
    }

    public static final function aesEncode($plain_text)
    {
        $key = self::$aes_encrypt_key;
        return base64_encode(openssl_encrypt($plain_text, "aes-256-cbc", $key, true, str_repeat(chr(0), 16)));
    }

    public static  final function aesDecode($base64_text)
    {
        $key = self::$aes_encrypt_key;

        return openssl_decrypt(base64_decode($base64_text), "aes-256-cbc", $key, true, str_repeat(chr(0), 16));
    }


    public static  final function aesEncryptCode ($value)
    {
        $key = self::$aes_encrypt_key;
        $padSize = 16 - (strlen ($value) % 16) ;
        $value = $value . str_repeat (chr ($padSize), $padSize) ;
        $output = mcrypt_encrypt (MCRYPT_RIJNDAEL_128, $key, $value, MCRYPT_MODE_CBC, str_repeat(chr(0),16)) ;
        return base64_encode ($output) ;
    }

    public static  final function aesDecryptCode($value)
    {
        $key = self::$aes_encrypt_key;
        $value = base64_decode ($value) ;
        $output = mcrypt_decrypt (MCRYPT_RIJNDAEL_128, $key, $value, MCRYPT_MODE_CBC, str_repeat(chr(0),16)) ;

        $valueLen = strlen ($output) ;
        if ( $valueLen % 16 > 0 )
            $output = "";

        $padSize = ord ($output{$valueLen - 1}) ;
        if ( ($padSize < 1) or ($padSize > 16) )
            $output = "";                // Check padding.

         // DD($value);
         // DD($padSize);
         // exit;
        for ($i = 0; $i < $padSize; $i++)
        {
            if ( ord ($output{$valueLen - $i - 1}) != $padSize )
            {
                $output = "";
            }
        }
        $output = substr ($output, 0, $valueLen - $padSize) ;

        return $output;
    }


    /** [enPassword 此函数千万别改否则] */
    public static final function enPassword( $password =  '' )
    {
        if($password == '') return '';
        $password = md5(strval(trim($password)));
        $md5Password = md5($password . '@_' . substr($password, 2, 12));
        return $md5Password;
    }

    /** [checkPassword description] */
    public static final function checkPassword( $password =  '' )
    {
        return is_string($password) && !empty(trim($password)) && strlen($password) == 32 && static::enPassword($password);
    }
}