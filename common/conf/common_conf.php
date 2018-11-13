<?php

// 公共配置
return
[
    //////// 公共帐号密码 ////////

    // 短信帐号密码
    'SMS_USER_NAME'   => 'XXX',
    'SMS_USER_PWD'    => 'XXX',

    //邮件账号
    'EMAILHOST'       => 'smtp.exmail.qq.com',
    'EMAIL_USER_NAME' => 'XX@XX.com',
    'EMAIL_USER_PWD'  => 'test',

    // 接口的 环境配置 // 1是开发模式 D调试函数会正常打印数据 // 2是正式环境
    // 优先加载本地配置（xxxx_conf.php），次级加载公共配置 common_conf.php
    'DEPLOY_STATUS'   => 1,

    // 上传图片路径
    // 'UPLOAD_IMG_PATH' => '/data/web/njart/admin/webroot/',

    // 展示图片URL域名前缀
    'UPLOAD_IMG_URL'  => 'http://zuoye-admin.dawennet.com/',

    // 允许ajax跨域的域名列表
    'allowOrigin' => [
        'http://XXX.com',
    ],

    // 公共接口API,注册用户同步到对应接口
    'commonApi' => 'XXXX.com',

    // 图片服务处理接口
    'imgApi' => 'XXX.com',

    // 检查下面接口 临时更改为图片服务器域名
    // 'checkImgApi' =>
    // [
    //     'axapi/upload_file',
    //     'axapi/up_base64',
    //     'axapi/up_file',
    // ],

    // 客户端类型及私钥
    'DEVICE_TYPE' =>
    [
        // 客户端 HaoConnect
        'BROWSER' =>
        [
            'type'   => 1,
            'secret' => 'NWr0oXQi1y',
        ],
        'PC' =>
        [
            'type'   => 2,
            'secret' => 'MIcer0EF1s',
        ],
        'ANDROID' =>
        [
            'type'   => 3,
            'secret' => 'XLPfjMsCVw',
        ],
        'IOS' =>
        [
            'type'   => 4,
            'secret' => 'AORcgBGUXH',
        ],
        'WXAPP' =>
        [
            'type'   => 5,
            'secret' => 'ORGU321AXH',
        ],

        // 此客户端请求公共接口，使用这个身份标识
        //  njartapi -> commonapi 标识
        'NY_API' =>
        [
            'type'   => 11,
            'secret' => 'CF7SfAch8T',
        ],
    ]

];
