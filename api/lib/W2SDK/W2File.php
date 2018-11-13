<?php
/**
 * 文件处理函数库文件
 * @package W2
 * @author 琐琐
 * @since 1.0
 * @version 1.0
 */

class W2File {
    /**
     * 读取文件内容,返回字符串
     * @param string $p_filePath 文件路径
     * @return sting 文件内容
     */
    public static function loadContentByFile($p_filePath){
        if(!file_exists($p_filePath) || filesize($p_filePath)==0){
            return null;
        }
        $fp = fopen($p_filePath, 'r');
        $c = fread($fp, filesize($p_filePath));
        fclose($fp);
        return $c;
    }

    /**
     * 读取文件内容中的数组
     * @param string $p_filePath 文件路径
     * @return sting 数组
     */
    public static function loadArrayByFile($p_filePath){
        return json_decode(W2File::loadContentByFile($p_filePath),true);
    }

    /**
     * 读取文件内容中的对象
     * @param string $p_filePath 文件路径
     * @return sting 对象
     */
    public static function loadObjectByFile($p_filePath){
        $o = json_decode(W2File::loadContentByFile($p_filePath),false);
        return is_array($o)?(object)$o:$o;
    }

    /**
     * 将对象或文本写入文件
     * @param string $p_filePath 文件路径
     * @param mixed $p_content 要写入的内容, 内容为对象或者数组时, 会自动转换为json格式写入
     * @param string $p_mode 文件打开方式,默认为w
     */
    public static function writeFile($p_filePath, $p_content, $p_mode='w'){
        $fp = fopen($p_filePath, $p_mode);
        fwrite($fp, (is_array($p_content)||is_object($p_content))?json_encode($p_content):$p_content);
        fclose($fp);
    }
    /**
     * 判断目标文件夹是否存在，如不存在则尝试以0777创建
     * @param string $dir 文件路径
     */
    public static function directory($dir){
        // echo $dir;
        return   is_dir ( $dir )  or  (W2File::directory(dirname( $dir ))  and  mkdir ( $dir ) );
    }

    /**
     * 删除目标文件夹（及其所有子文件）
     * @param  [type] $dir [description]
     * @return [type]      [description]
     */
    public static function deldir($dir) {
        //先删除目录下的文件：
        if (is_dir($dir))
        {
            $dh = opendir($dir);
            while ($file = readdir($dh)) {
                if ($file != "." && $file != "..") {
                    $fullpath = $dir."/".$file;
                    if (!is_dir($fullpath)) {
                        unlink($fullpath);
                    } else {
                        static::deldir($fullpath);
                    }
                }
            }

            closedir($dh);
            //删除当前文件夹：
            if (rmdir($dir))
            {
                return true;
            }
        }
        return false;
    }

    /** 无视大小写，获得真实路径 */
    public static function realpath($filePath,$fileDir = '')
    {
        if (file_exists($fileDir.$filePath))
        {
            return realpath($fileDir.$filePath);
        }
        $guessPath = preg_replace_callback('/([A-Za-z])/', function($matches){
                                                return '['.strtolower($matches[1]).strtoupper($matches[1]).']';
                                            }, $filePath);
        foreach (glob(AXAPI_JOB_PATH.$guessPath) as $_file) {
            return $_file;
        }
        return null;
    }

    /** [mkdirs description] */
    public static function mkDirs($dir){
        $dir = dirname($dir);
        if(!is_dir($dir))
        {
            if(!static::mkDirs($dir)){
                return false;
            }
            if(!mkdir($dir,0777)){
                return false;
            }
        }
        return true;
    }


    /**
     * [getUpFileError description]
     * @param  [type] $type [description]
     * @return [type]       [description]
     */
    public static function getUpFileError($type = null)
    {
        $error = '';
        switch ($type)
        {
            case '0': $error = '未发现错误'; break;
            case '1': $error = '文件大小超出了服务器的空间大小'; break;
            case '2': $error = '要上传的文件大小超出浏览器限制'; break;
            case '3': $error = '文件仅部分被上传'; break;
            case '4': $error = '没有找到要上传的文件'; break;
            case '5': $error = '服务器临时文件夹丢失'; break;
            case '6': $error = '文件写入到临时文件夹出错'; break;
            default: $error = '上传错误'; break;
        }
        return $error;
    }

    /**
     * [upFile description]
     * @param  array  $upfile [description]
     * @return [type]         [description]
     */
    /*public static function upFile( $upfile = [], $type = '')
    {
        $baseUrl  = UPLOAD_IMG_URL;
        $basePath =  UPLOAD_IMG_PATH;

        // D($_FILES);
        // D($upfile);
        // exit;
        $arrFileInfo             = [];
        $arrFileInfo['fileName'] = $upfile["name"];
        $name                    = empty($_REQUEST['fileName']) ? $upfile["name"] : $_REQUEST['fileName'] ;
        $arrFileInfo['fileType'] = pathinfo( $name )['extension'];
        $arrFileInfo['fileSize'] = $upfile["size"];
        $arrFileInfo['md5File']  = md5_file($upfile["tmp_name"]);
        $md5Hash                 = "{$arrFileInfo['md5File']}_{$arrFileInfo['fileSize']}.{$arrFileInfo['fileType']}";
        $arrFileInfo['fileHash'] = $md5Hash;

        $toDay = date('Ymd');
        $arrFileInfo['filePath'] = "{$type}/{$toDay}/{$md5Hash}";
        $arrFileInfo['upPath']   = $basePath . $arrFileInfo['filePath'];
        D($arrFileInfo,$upfile);
        exit;
        if (file_exists($arrFileInfo['upPath']))
        {
            $arrFileInfo['uploaded'] = true;
        }
        else
        {
            W2File::mkDirs($arrFileInfo['upPath']);
            $arrFileInfo['uploaded'] = move_uploaded_file($upfile["tmp_name"], $arrFileInfo['upPath']);
        }
        $arrFileInfo['uploaded'] &&  $arrFileInfo['urlPath'] = $baseUrl . $filePath;
        return $arrFileInfo;
    }*/

    /** [getBase64ImgInfo description] */
    public static function getBase64ImgInfo($base64 = '')
    {
        $base64Info =
        [
            'file_md5'  => '',
            'file_size' => '',
            'file_type' => '',
            'file_data' => '',
        ];
        if(preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64, $result))
        {
            // D($result);
            $base64Info['file_type'] = $result[2];
            $base64Img               = base64_decode(str_replace($result[1], '', $base64));
            $base64Info['file_data'] = $base64Img;
            $base64Info['file_size'] = strlen($base64Img);
            $base64Info['file_md5']  = md5($base64Img);
        }
        return $base64Info;
    }

    /** [getBase64ImgInfo description] */
    public static function getStrImgInfo($strImg = '',$imgName = '')
    {
        $fileInfo =
        [
            'file_md5'  => '',
            'file_size' => '',
            'file_type' => '',
            'file_data' => '',
        ];

        $pathinfo              = pathinfo( $imgName );
        $fileInfo['file_type'] = empty($pathinfo['extension']) ? '' : $pathinfo['extension'];
        $fileInfo['file_data'] = $strImg;
        $fileInfo['file_size'] = strlen($strImg);
        $fileInfo['file_md5']  = md5($strImg);
        return $fileInfo;
    }


    /** [getFilePath 获取文件路径信息] */
    public static function getFilePath($filePath = '')
    {
        $fileInfo               = [];
        $fileInfo['filePath']   = $filePath;
        $fileInfo['upPath']     = UPLOAD_IMG_PATH . $filePath;
        $fileInfo['preview']    = UPLOAD_IMG_URL . $filePath;
        $fileInfo['fileExists'] = file_exists($fileInfo['upPath']);
        return $fileInfo;
    }

    /** [getFileInfo 获取文件基本信息] */
    public static function getFileInfo($file)
    {
        $fileInfo =
        [
            'file_md5'  => '',
            'file_size' => '',
            'file_type' => '',
        ];
        //curl request file 情况下
        // if('application/octet-stream' == $arrFile["type"] )
        // {
        //     $arrImgInfo['fileType']       = pathinfo( $arrFile["name"])['extension'];
        // }
        // else
        // {
        //     $arrImgInfo['fileType']       =     trim(stristr($arrFile["type"],'/'),'/');
        // }

        // die(json_encode($file));
        if(isset($file['error']) && $file['error'] === 0)
        {

            if($file['type'] == 'application/octet-stream' && !empty($file['source_name']) )
            {
                $file['name'] = $file['source_name'];
            }
            $fileInfo['file_md5']  = md5_file($file['tmp_name']);
            $fileInfo['file_size'] = $file['size'];
            $pathinfo              = pathinfo( $file['name'] );
            $fileInfo['file_type'] = empty($pathinfo['extension']) ? '' : $pathinfo['extension'];
        }
        return $fileInfo;
    }
}

/**
 * unit test
 */
/*
if(array_key_exists('argv', $GLOBALS) && realpath($argv[0]) == __file__){
    $f1 = '/Users/Wan/Project/_file-upload/aa';
    writeFile($f1, array(1,2,3,4));
    var_dump(loadContentByFile($f1));
    var_dump(loadArrayByFile($f1));
    var_dump(loadObjectByFile($f1));

    $f2 = '/Users/Wan/Project/_file-upload/bb';
    writeFile($f2, array('a'=>1,'b'=>2,'c'=>3,'d'=>4));
    var_dump(loadContentByFile($f2));
    var_dump(loadArrayByFile($f2));
    var_dump(loadObjectByFile($f2));
}
*/
