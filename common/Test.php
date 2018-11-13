<?php


// 首先建议项目入口（如index.php或者 config.php）文件引入公共函数
// 加载完毕之后setConfigByFile设置模块对应的配置，然后loadConfig加载对应模块值
// 通常自己模块通过loadConfig加载配置 ，少些配置(如db,公用的短信帐号密码，支付等)通过loadConfigByFile临时加载其它模块配置

// 注意项：
// 如果是DBConnect连接数据库 DBConnect 存在有个bug 旧的是 strlen 改为 strval
// DBConnect 16行正确代码是 $this->conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE,defined('DB_PORT')?strval(DB_PORT):'3306',MYSQL_CLIENT_INTERACTIVE);

// 调试代码 可以放在你的入口（index.php OR config.php 等）文件 进行测试
if(!empty($_REQUEST['debug']))
{
    // 示例：
    // 引入公共函数 （注意项目的相对路径）
    // require_once(__dir__.'/../../common/CommonFun.php');
    // 设置加载配置文件
    // CommonFun::setConfigByFile('common_api');

    // 格式化输出
    echo '<pre />';

    // NULL  没设置文件类型之前，加载默认是null
    var_dump(CommonFun::loadConfig());

    // 设置文件类型之后 如 common_api 实际加载的是  common_api_conf.php 配置
    CommonFun::setConfigByFile('common_api');

    // key为空 默认加载的是common_api的所有数据
    print_r(CommonFun::loadConfig());

    // 指定加载对应的 key值
    print_r(CommonFun::loadConfig('DEVICE_TYPE.BROWSER'));

    // 临时加载其它文件类型对应的key
    print_r(CommonFun::loadConfigByFile('SMS_USER_NAME','common'));


    // 注意： 为了对现有代码改动较小，建议下面方式修改 给予类常量，静态变量，宏定义（常量） 赋值方式
    // define 支持赋值一个变量（赋值一次，TEST1就是固定常量了）
    define('TEST1' , CommonFun::loadConfig('DEFAULT_PAGE_SIZE'));
    define('TEST2' , CommonFun::loadConfig('DEFAULT_PAGE_SIZE'));
    // 如果担心被多次定义常量，可以defined or 语法
    defined('TEST3') OR define('TEST3', 'TEST3'); // 白话：如果不存在 TEST3 就定义一个 TEST3 常量

    // 测试类
    class TestA
    {
        // 代码会报错， 不允许给静态变量赋值一个变量，但是可以赋值一个常量
        // public static $test1 = CommonFun::loadConfig('DEFAULT_PAGE_SIZE');
        // 正确赋值方式
        public static $test1 = TEST1;

        // CONST 方式和静态变量方式一样
        CONST TEST2 = TEST2;
    }

    exit; // 结束
}

// 7-24 CommonFun更新日志

// 加载当前项目环境配置
// CommonFun::loadDeployStatus();

// 加载顺序：优先加载本地配置，次级加载公共配置
// 优先加载本地配置（xxxx_conf.php），次级加载公共配置 common_conf.php
// CommonFun::loadLocalConfig();

// 加载公共配置
// CommonFun::loadCommonConfig();


// PS:新版本HaoConnect原来根据环境配置请求域名，统一改为了读取配置  里面会主动引入 require_once CommonFun 类
// 客户端请求的域名会根据统一配置的域名进行请求

//请求接口客户端
require_once(__DIR__.'/../../HaoConnect/api/HaoConnect/HaoConnect.php');

// 客户端类型
HaoConnect::$Devicetype         = CommonFun::loadDeviceType('SOMALL_API');
HaoConnect::$SECRET_HAX_CONNECT = CommonFun::loadDeviceSecret('SOMALL_API');

// 可以默认设置为请求公共API，如果请求其它模块API，再次调用设置域名函数 setSoMallApi setJobApi setCommonApi setWkApi
HaoConnect::setCommonApi();


