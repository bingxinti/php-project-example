// apiList.push({
//   "title":"接口工具:Say Hello Word"
//   ,"desc":""
//   ,"time":""
//   ,"action":"/axapi/SayHello"
//   ,"method":"get"
//   ,"request":[
//      { "key":"name",       "type":"string",  "title":"name",       "desc":"",    "required":false,  "test-value":"wanyaxing"}
//     ,{ "key":"password",   "type":"md5",     "title":"password",   "desc":"",    "required":false,  "test-value":"123456"}
//     ,{ "key":"avatar",     "type":"file",    "title":"avatar",     "desc":"",    "required":false,  "test-value":""}
//     ,{ "key":"photos[]",   "type":"file",    "title":"avatar",     "desc":"",    "required":false,  "test-value":""}
//     ,{ "key":"age",        "type":"int",     "title":"age",        "desc":"",    "required":false,  "test-value":"29"}
//     ,{ "key":"content",    "type":"string",  "title":"content",    "desc":"",    "required":false,  "test-value":"see more detail"}
//   ]
// });

/*apiList.push({
  "title":"接口工具:测试首页数据"
  ,"desc":""
  ,"time":""
  ,"action":"/axapi/get_home_table_for_test"
  ,"method":"get"
  ,"request":[
      { "key":"sleep",        "type":"int",     "title":"延迟时间（单位：秒）",        "desc":"page",    "required":false,  "test-value":"0"}
  ]
});*/

// apiList.push({
//   "title":"接口工具:查看日志（限管理员）"
//   ,"desc":""
//   ,"time":""
//   ,"action":"/axapi/LoadLogList"
//   ,"method":"get"
//   ,"request":[
//      { "key":"page"                  ,"type":"int"        ,"required": true ,"test-value":"1"                        ,"title":"分页，第一页为1，第二页为2，最后一页为-1" ,"desc":"" }
//     ,{ "key":"size"                  ,"type":"int"        ,"required": true ,"test-value":"10"                       ,"title":"分页大小" ,"desc":"" }
//     ,{ "key":"type"                 ,"type":"string"      ,"required":true ,"test-value":"error"                         ,"title":"日志类型" ,"desc":"限以下值（access: 访问日志, error:错误日志）" }
//     ,{ "key":"datetime"             ,"type":"datetime"      ,"required":false ,"test-value":""                         ,"title":"指定日志所在日期（默认当日）" ,"desc":"" }
//   ]
// });


// apiList.push({
//   "title":"接口工具:MHC代码文件快速生成（限管理员）"
//   ,"desc":""
//   ,"time":""
//   ,"action":"/axapi/create_mhc_with_table_name"
//   ,"method":"post"
//   ,"request":[
//      { "key":"-t"                  ,"type":"string"        ,"required": true ,"test-value":""                        ,"title":"表名" ,"desc":"严格大小写" }
//     ,{ "key":"-name"               ,"type":"string"        ,"required": false ,"test-value":""                       ,"title":"接口分类（中文）如：用户、设备、留言" ,"desc":" 也可不填，可以取表COMMENT" }
//     ,{ "key":"-pri"               ,"type":"string"        ,"required": false ,"test-value":""                       ,"title":"默认取PRI且auto_increment的字段。若取不到，则可以在此处填一个字段，否则就是空了哦" ,"desc":" 也可不填，可以取表COMMENT" }
//     ,{ "key":"-rm"                 ,"type":"string"      ,"required":false ,"test-value":""                         ,"title":"是否删除代码文件" ,"desc":"yes / no" }
//     ,{ "key":"-update"             ,"type":"string"      ,"required":false ,"test-value":""                         ,"title":"是否更新代码文件" ,"desc":"yes / no" }
//   ]
// });

apiList.push({
  "title":"接口工具:生成MHC代码（限管理员）"
  ,"desc":""
  ,"time":""
  ,"action":"/axapi/auto_create"
  ,"method":"post"
  ,"request":[
     { "key":"tableName"                  ,"type":"string"        ,"required": true ,"test-value":""                        ,"title":"表名" ,"desc":"严格大小写,多个逗号隔开" },
     { "key":"resetFile"                  ,"type":"integer"        ,"required": true ,"test-value":"no"                        ,"title":"yes重新生成 no新建" ,"desc":"重新创建文件" }
  ]
});


// apiList.push({
//   "title":"接口工具:HaoConnect代码文件快速生成（限管理员）"
//   ,"desc":""
//   ,"time":""
//   ,"action":"/axapi/update_codes_of_hao_connect"
//   ,"method":"post"
//   ,"request":[
//     { "key":"-clear"                 ,"type":"string"      ,"required":false ,"test-value":"no"                         ,"title":"是否先清理代码文件再重新生成" ,"desc":"yes / no" }
//   ]
// });

// apiList.push({
//   "title":"接口工具:获得Model对应字段的描述"
//   ,"desc":""
//   ,"time":""
//   ,"action":"/axapi/get_descriptions_in_model"
//   ,"method":"get"
//   ,"request":[
//     { "key":"model_name"             ,"type":"string"      ,"required":true ,"test-value":"user"                         ,"title":"model名" ,"desc":"" }
//   ]
// });

// apiList.push({
//   "title":"接口工具:获得一个验证码图像"
//   ,"desc":""
//   ,"time":""
//   ,"action":"/axapi/get_captcha"
//   ,"method":"get"
//   ,"request":[
//   ]
// });

// apiList.push({
//   "title":"接口工具:确认图像验证码是否正确"
//   ,"desc":""
//   ,"time":""
//   ,"action":"/axapi/check_captcha"
//   ,"method":"post"
//   ,"request":[
//      { "key":"captcha_key"              ,"type":"string"      ,"required":true ,"test-value":"170b1d79c01c2f7b8c6eec46d804e6e9_1465116169"                         ,"title":"验证码对应的key" ,"desc":"" }
//     ,{ "key":"captcha_code"             ,"type":"string"      ,"required":true ,"test-value":"1234"                         ,"title":"验证码" ,"desc":"" }
//   ]
// });


/*apiList.push({
  "title":"接口工具:协议"
  ,"desc":""
  ,'time':'2016-11-25 10:03:04'
  ,"action":"/axapi/agreement"
  ,"method":"get"
  ,"request":[
      { "key":"type",        "type":"string",     "title":"协议类型 用户注册user,商家注册store，商家入驻settled ",        "desc":"",    "required":true,  "test-value":"user"}
  ]
});

apiList.push({
  "title":"接口工具:获取网页URL"
  ,"desc":""
  ,'time':'2016-11-25 10:03:04'
  ,"action":"/axapi/webUrl"
  ,"method":"get"
  ,"request":[
      { "key":"type",  "type":"string",     "title":"不指定类型获取所有，指定类型或者执行的如 aboutUser",        "desc":"",    "required":true,  "test-value":"aboutUser"}
  ]
});
*/