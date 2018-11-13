<?php

// 公共接口配置
return  [

    // 环境配置 // 1是开发模式 D调试函数会正常打印数据 // 2是正式环境
    // 优先加载本地配置，次级加载公共配置
    // 'DEPLOY_STATUS'     => 1,

    // 默认分页数据量
    'DEFAULT_PAGE_SIZE' => 10,
    'DEFAULT_MAX_PAGE_SIZE' => 999,

    // API项目名（最好全网唯一） 缓存前缀用，目前用不到
    'AXAPI_PROJECT_NAME' => 'project-dww-api',

    // * 混淆方案- 用户信息加密混淆
    'USER_COOKIE_RANDCODE' => '4cl8oEPRDQMdQ9gEXQgwRv7Uj',
    // 混淆方案- 密码加密存储混淆
    'PASSWORD_RANDCODE' => 'UiWBEp2J41zGKNflbDn0T9FRK',
    // 混淆方案- 图像校验加密混淆
    'CAPTCHA_RANDCODE' => 'XJyKINwkkspB9BErgecDCZWKiNYp61',

    // * @var [type] [AES加密私钥KEY]
    'AES_ENCRYPT_KEY' => 'a2bcdef3g146hijkpqlm5nortxuswvzy',
    'AES_CONFUSE_KEY' => 'Confuse_the_encryption_KEY_2016',

];

