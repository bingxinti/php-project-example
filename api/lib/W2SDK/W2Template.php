<?php
/**
 * 模板生成
 * @package Controller
 * @author Demon
 * @since 1.0
 * @version 1.0
 */

class W2Template
{
	 /** [columnType description] */
    public static function getAttributeType(  $column = '' )
    {
        $dataType = 'string';
        $columnType =
        [

            'tinyint'    => 'integer',
            'smallint'   => 'integer',
            'mediumint'  => 'integer',
            'integer'    => 'integer',
            'bigint'     => 'integer',

            'float'      => 'float',
            'double'     => 'float',
            'decimal'    => 'float',

            'varchar'    => 'string',
            'tinyblob'   => 'string',
            'tinytext'   => 'string',
            'blob'       => 'string',
            'text'       => 'string',
            'mediumblob' => 'string',
            'mediumtext' => 'string',
            'logngblob'  => 'string',
            'longtext'   => 'string',

            'YEAR'       => 'date',
            'date'       => 'date',
            'time'       => 'time',
            'datetime'   => 'datetime',
            'timestamp'  => 'datetime',

        ];

        if ( isset( $columnType[ strtolower( $column ) ] ) )
        {
            $dataType =  $columnType[$column];
        }

        return $dataType;
    }

	/** [getUpdateAttributeKeys 允许修改的参数] */
	public static function getUpdateAttributeKeys($arrAttributes = [])
	{
		$modelAttributes = [];
		$maxKeyLength    = static::getMaxAttributesKeyLength($arrAttributes);
		foreach ($arrAttributes as $key => $value)
		{
			if(!in_array(Utility::camelCase($key),['id','status','createTime','modifyTime','dflag']))
			{
				empty($value['comment']) && $value['comment'] = $key;
				$strKeyLength      = $maxKeyLength - strlen($key);
				$strKeyLength      = str_pad('',$strKeyLength,' ',STR_PAD_RIGHT);
				// $_key           = W2String::under_score($key);
				$_key              = $key;
				$strContent        = empty($modelAttributes) ?"'{$_key}',{$strKeyLength}":"            '{$_key}',{$strKeyLength}";
				$strContent        .= " // {$value['comment']}";
				$modelAttributes[] = $strContent;
			}
		}
		$modelAttributes  = implode("\n", $modelAttributes);
		return $modelAttributes;
	}

	/** [getMaxAttributesKeyLength description] */
	public static function getMaxAttributesKeyLength($arrAttributes = [])
	{
		$maxKeyLength = 0;
		foreach ($arrAttributes as $key => $value)
		{
			$maxKeyLength = max($maxKeyLength,strlen($key));
		}
		return $maxKeyLength;
	}
	/** [getUpdateRequiredKeys 必须的参数] */
	public static function getUpdateRequiredKeys($arrAttributes = [])
	{
		$modelAttributes = [];
		$maxKeyLength    = static::getMaxAttributesKeyLength($arrAttributes);
		foreach ($arrAttributes as $key => $value)
		{
			$strKeyLength = $maxKeyLength - strlen($key);
			$strKeyLength = str_pad('',$strKeyLength,' ',STR_PAD_RIGHT);
			if(!in_array(Utility::camelCase($key),['id','status','createTime','modifyTime','dflag']))
			{
				// $_key = W2String::under_score($key);
				empty($value['comment']) && $value['comment'] = $key;
				$_key              = $key;
				$strContent        = empty($modelAttributes) ? "'{$_key}'{$strKeyLength} => '{$value['comment']}', ": "            '{$_key}'{$strKeyLength} => '{$value['comment']}', ";
				$modelAttributes[] = $strContent;
			}
		}
		$modelAttributes  = implode("\n", $modelAttributes);
		return $modelAttributes;
	}



	/** [getModelReplace description] */
	public static function getModelReplace($modelPathReplace = [], $arrAttributes = [])
	{
		$disabledKeys     = [];
		$publicFuntions   = [];
		$publicAttributes = [];
		foreach ($arrAttributes as $key => $value)
		{
			$caseKey = Utility::camelCase($key);
			if(in_array($caseKey,['status','createTime','modifyTime']))
			{
				$disabledKeys[] = "'{$caseKey}'";
			}

			$strPublicAttributes = '';
			$length = empty($value['length']) ? '' : "({$value['length']})" ;
			empty($value['comment']) OR $strPublicAttributes .= "    /** @var {$value['type']}{$length} {$value['comment']} */\n";
			$strPublicAttributes .= "    public \${$key};";
			$publicAttributes[] = $strPublicAttributes;
			$keyFirst = W2String::camelCaseWithUcFirst($key);
			$publicFuntion = '';
			$publicFuntion .= "
	/** {$value['comment']} **/
    public function get{$keyFirst}()
    {
        return \$this->{$key};
    }
    /** {$value['comment']} **/
    public function set{$keyFirst}(\${$key})
    {
        \$this->{$key} = \${$key};
        return \$this;
    }";
			$publicFuntions[] = $publicFuntion;
		}

		$modelPathReplace['disabledKeys']     = implode(',', $disabledKeys);
		$modelPathReplace['publicAttributes'] = implode("\n\n", $publicAttributes);
		$modelPathReplace['publicFuntion']    = implode("\n", $publicFuntions);
		return $modelPathReplace;
	}

	/**
	 * [getScriptReplace description]
	 * @param  array  $modelPathReplace [description]
	 * @param  array  $arrAttributes    [description]
	 * @return [type]                   [description]
	 */
	public static function getScriptReplace($scriptReplace, $arrAttributes = [])
	{
		$apiAdd = $apiUpdate = $apiList = $apiDetail = [];
		foreach ($arrAttributes as $key => $info)
		{
			$_key         = W2String::under_score($key);
			$desc         = empty($info['length']) ? '' : "长度{$info['length']}";
			$type         = static::getAttributeType($info['type']);
			$default      = $info['default'];
			$default      == 'CURRENT_TIMESTAMP' &&  $default =  '';
			$title        = $info['comment'];
			$title        = addslashes($title);
			$default      = '';
			$scriptRow    = "    ,{'key':'{$_key}', 'type':'{$type}', 'required': false, 'time':'', 'test-value':'{$default}', 'title':'{$title}', 'desc':'{$desc}' }";
			$scriptUpdate = $scriptRow;
			if(!in_array(Utility::camelCase($key),['id','status','createTime','modifyTime','dflag']))
			{
				if(empty($apiAdd))
				{
					// 第一条数据时
					$scriptRow = "    {'key':'{$_key}', 'type':'{$type}', 'required': false, 'time':'', 'test-value':'{$default}', 'title':'{$title}', 'desc':'{$desc}' }";
				}
				$apiAdd[] = $scriptRow;
			}
			else
			{
				$scriptUpdate = "// {$scriptRow}";
			}

			$apiUpdate[] = $scriptUpdate;

			$default   = '';
			$scriptRow = "    ,{'key':'{$_key}', 'type':'{$type}', 'required': false, 'time':'', 'test-value':'{$default}', 'title':'{$title}', 'desc':'{$desc}' }";
			$apiList[] = $scriptRow;
			$key!='id' && $apiDetail[] = $scriptRow;
 		}

		$scriptReplace['[api_add]']    = trim(implode("\n",$apiAdd),',');
		$scriptReplace['[api_update]'] = trim(implode("\n",$apiUpdate),',');
		$scriptReplace['[api_list]']   = trim(implode("\n",$apiList),',');
		$scriptReplace['[api_detail]'] = trim(implode("\n",$apiDetail),',');
		// D($scriptReplace);

		return $scriptReplace;
	}

	/** [putTemplate description] */
    public static function putTemplate($params = [])
    {
    	$templatePath = AXAPI_ROOT_PATH . '/mhc/';

        $arrFilePutStatus = [];
        $createTime = date('Y-m-d H:i');

		$strModel      = W2File::loadContentByFile( $templatePath . 'Template/model.php' );
		$strController = W2File::loadContentByFile( $templatePath . 'Template/controller.php' );
		$strHandler    = W2File::loadContentByFile( $templatePath . 'Template/handler.php' );
		$strModel      = W2File::loadContentByFile( $templatePath . 'Template/model.php' );
		$strScript     = W2File::loadContentByFile( $templatePath . 'Template/apiScript.js' );

        $resetFile = !empty($params['resetFile']) && $params['resetFile'] === 'yes' ;

		$arrMenueList = explode(',', $params['tableName']);
		foreach ($arrMenueList as $strFileName)
		{
			$dbModel         = new DBModel($strFileName);
			$arrTables = $dbModel->getTables();

			if(empty($arrTables[$strFileName]))  continue;
			$arrAttributes   = $dbModel->getAttributes();
			$tableComment    = $arrTables[$strFileName]['comment'];

			if($tableComment =='模板' || empty($tableComment) )
			{
				$tableComment = $strFileName;
				// throw new Exception("请修改 {$strFileName} 的描述", 1);
			}

			$modelAttributes = static::getUpdateAttributeKeys($arrAttributes);
			$requiredKeys    = static::getUpdateRequiredKeys($arrAttributes);
			// D($modelAttributes);
			// D($requiredKeys);
			// exit;
			// AuthRuleHandler::autoSaveTableRule($strFileName, $tableComment);

			$strFileName = Utility::camelCase($strFileName);
			$strFileName = ucfirst($strFileName);
            // $strFileName                                 = stristr($strFileName,'.', true);
			$tableComment = strtr($tableComment, [' ' =>'','//' => '']);


			// // Model
			$modelPath          = $templatePath."{$strFileName}/{$strFileName}Model.php";
			$modelPathReplace   = ['Template-description' => "{$tableComment} {$strFileName} Created by Tool on {$createTime}", 'Template' => $strFileName];
			$modelPathReplace   = static::getModelReplace($modelPathReplace, $arrAttributes);
			$modelPathData      = strtr($strModel, $modelPathReplace);
			$arrFilePutStatus[$strFileName.'Model.php'] = static::filePutTemplate($modelPath, $modelPathData, $resetFile);

            // Handler
			$handlerPath        = $templatePath."{$strFileName}/{$strFileName}Handler.php";
			$handlerPathReplace = ['Template-description' => "{$tableComment} {$strFileName} Created by Tool on {$createTime}", 'Template' => $strFileName ];
			$handlerPathData    = strtr($strHandler, $handlerPathReplace);
			$arrFilePutStatus[$strFileName.'Handler.php'] = static::filePutTemplate($handlerPath, $handlerPathData, $resetFile);

            // Controller
			$conterllerPath     = $templatePath."{$strFileName}/{$strFileName}Controller.php";
			$conterllerReplace  = ['Template-description' => "{$tableComment} {$strFileName} Created by Tool on {$createTime}", 'Template' => $strFileName, '#modelAttributes#' => $modelAttributes, '#requiredKeys#' => $requiredKeys ];
			$conterllerData     = strtr($strController, $conterllerReplace);
			$arrFilePutStatus[$strFileName.'Controller.php'] = static::filePutTemplate($conterllerPath, $conterllerData, $resetFile);

			// Script
			$scriptPath    = $templatePath."{$strFileName}/{$strFileName}Api.js";
			$scriptReplace = ['Template-description' => "{$tableComment} {$strFileName} Created by Tool on {$createTime}", 'tableComment' => $tableComment, 'CreatedTime' => date('Y-m-d H:i:s'), 'table_name' => W2String::under_score($strFileName) ];
			$scriptReplace = static::getScriptReplace($scriptReplace, $arrAttributes);
			$scriptData    = strtr($strScript, $scriptReplace);
			$arrFilePutStatus[$strFileName.'Api.js'] = static::filePutTemplate($scriptPath, $scriptData, $resetFile);
		}
		return $arrFilePutStatus;
    }


    public static  function deleteAll($path)
    {
    	if(is_dir($path))
    	{
		    $op = dir($path);
		    while(false != ($item = $op->read())) {
		        if($item == '.' || $item == '..') {
		            continue;
		        }
		        if(is_dir($op->path.'/'.$item)) {
		            deleteAll($op->path.'/'.$item);
		            rmdir($op->path.'/'.$item);
		        } else {
		            unlink($op->path.'/'.$item);
		        }
		    }
		    rmdir($path);
    	}
	}

    /** [filePutTemplate description] */
    public static function filePutTemplate( $contenPath = './', $content = '', $resetFile = false)
    {
    	$pathInfo = pathinfo($contenPath);
    	// if($resetFile)
    	// {
    	// 	static::deleteAll( $pathInfo['dirname'] );
    	// }
    	if(!is_dir($pathInfo['dirname']))
    	{
    		if(mkdir( $pathInfo['dirname'] ))
    		{
    			chmod($pathInfo['dirname'], 0775);
    		}
    	}
    	if(file_exists($contenPath))
    	{
    		if($resetFile)
    		{
    			unlink($contenPath);
    		}
    	}
    	// D($pathInfo);
    	// D($contenPath);
    	// exit;
    	// D($content);
        if ( !file_exists($contenPath) )
        {
            return static::filePut($contenPath, $content, false);
        }
        return '已存了';
    }

    /** [filePut description] */
    public static function filePut( $path = './', $content = '', $append  = false )
    {
        // D(__DIR__);
        if($append)
        {
            $putStatus  =  file_put_contents($path, $content, FILE_APPEND);
        }
        else
        {
            $putStatus  =  file_put_contents($path, $content);
        }

        if($putStatus)
        {
            chmod($path, 0664);
        }
        return $putStatus;
    }
}