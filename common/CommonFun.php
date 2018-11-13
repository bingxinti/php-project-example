<?php

/**
* 公共函数
*/
class CommonFun
{
	// 当前模块对应配置数据 （loadConfig直接在此数组里面查找path）
	public static $config = null;
	// 缓存已加载的配置数据，避免多次require多次读取文件
	public static $_configs = [];

	// 公共配置文件类型
	CONST COMMON_FILE = 'common';

	/**
	 * loadConfig
	 * 获取指定key对应的值，支持路径获取
	 *
	 * @param  string $path [description]
	 * CommonFun::loadConfigByFile('','common_api');  空字符串，或者null 默认返回所有配置
	 * CommonFun::loadConfigByFile('DEVICE_TYPE','common_api');
	 * CommonFun::loadConfigByFile('DEVICE_TYPE.BROWSER','common_api');
	 * CommonFun::loadConfigByFile('DEVICE_TYPE.BROWSER.type','common_api');
	 *
	 * @param  string $config [指定查找的原数组，不指定默认static::$config]
	 */
	public static function loadConfig($path = '', $config = null)
	{
		if(is_null($config))
		{
			$config = static::$config;
		}

		if(!empty($config) && !empty($path) )
		{
			// 路径查找
			if (strpos($path,'.')!==false)
			{
				$arrPath = explode('.', $path);
				foreach ($arrPath as $key => $findKey)
				{
					// D($arrPath, isset($config[$findKey]) );
					if(isset($config[$findKey]))
					{
						$config = 	$config[$findKey];
					}
					else
					{
						$config = null;
					 	break;
					}
				}
			}
			// 正常KEY获取
			else
			{
				$config = isset($config[$path]) ? $config[$path] : null ;
			}
		}
		return $config;
	}

	// 加载顺序：优先加载本地配置，次级加载公共配置
	// 优先加载本地配置（xxxx_conf.php），次级加载公共配置 common_conf.php
    public static function loadLocalConfig($path = '')
    {
    	$data = CommonFun::loadConfig($path);
    	if(is_null($data))
    	{
    		$data = CommonFun::loadConfigByFile($path,STATIC::COMMON_FILE);
    	}
    	return $data;
    }

    // 加载公共配置
    public static function loadCommonConfig($path = '')
    {
    	return CommonFun::loadConfigByFile($path,STATIC::COMMON_FILE);
    }

	/**
	 * [loadConfigByFile 读取本地配置文件缓存到$_configs数组]
	 * @param  string $path [路径]
	 * @param  string $file [文件类型 如 common_api 实际加载的是  common_api_conf.php 配置 ]
	 * @return [type]       [description]
	 */
	public static function loadConfigByFile($path = '', $file = '')
	{
		$config = null;

		// 做一层存储，放在静态变量数组里，避免require多次消耗性能
		if(!isset(static::$_configs[$file]))
		{
			$filePath = __DIR__ . "/conf/{$file}_conf.php";
			if(file_exists($filePath))
			{
				$config = require $filePath;
				static::$_configs[$file] = $config;
			}
		}
		else
		{
			$config = static::$_configs[$file];
		}

		$config = static::loadConfig($path, $config);
		return $config;
	}

	/**
	 * [setConfigByFile 设置当前模块对应配置数据]
	 * @param string $file [description]
	 */
	public static function setConfigByFile($file = '')
	{
		static::$config = static::loadConfigByFile(null, $file);
		return static::$config;
	}


	// 为了加载数据方便，提供下面函数

	/** [loadSecretValue 加载设备类型对应的私钥值 如 BROWSER] */
    public static function loadDeviceSecret($client = '')
    {
		$path   = "DEVICE_TYPE.{$client}.secret";
		$secret = CommonFun::loadConfigByFile($path,STATIC::COMMON_FILE);
    	return empty($secret) ? '': "secret={$secret}";
    }
    /** [loadDeviceType 加载设备类型 如 BROWSER] */
    public static function loadDeviceType($client = '')
    {
		$path = "DEVICE_TYPE.{$client}.type";
		// $type = CommonFun::loadConfig($path);
		$type = CommonFun::loadConfigByFile($path,STATIC::COMMON_FILE);
    	return $type;
    }

    // 读取当前环境配置
    public static function loadDeployStatus($path = 'DEPLOY_STATUS')
    {
    	$deployStatus = static::loadLocalConfig($path);
    	return $deployStatus;
    }
}