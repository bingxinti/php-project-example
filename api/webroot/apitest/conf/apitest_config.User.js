
/*
apiList.push({
        'title':'用户:查看表结构（限管理员）'
        ,'desc':''
        ,'genre':''
        ,'action':'user/columns'
        ,'method':'get'
        ,'request':[]
      });



apiList.push({
        'title':'用户:新建'
        ,'desc':''
        ,'time':'2016-11-06 11:11:53'
        ,'action':'user/add'
        ,'method':'post'
        ,'request':[
         { 'key':'telephone'             ,'type':'string'     ,'required': true ,'test-value':''                         ,'title':'用户手机号' ,'desc':'' }
         ,{ 'key':'username'              ,'type':'string'     ,'required': true ,'test-value':''                         ,'title':'用户名' ,'desc':'' }
         ,{ 'key':'email'                 ,'type':'string'     ,'required': false ,'test-value':''                         ,'title':'邮箱' ,'desc':'' }
         ,{ 'key':'password'              ,'type':'md5'        ,'required': true ,'test-value':''                         ,'title':'密码' ,'desc':'' }
         ,{ 'key':'last_login_time'       ,'type':'datetime'   ,'required': false ,'test-value':''                         ,'title':'最后一次登录时间' ,'desc':'' }
         ,{ 'key':'last_password_time'    ,'type':'datetime'   ,'required': false ,'test-value':''                         ,'title':'最后一次密码修改时间' ,'desc':'' }
         ,{ 'key':'auth_role_ids'                 ,'type':'integer'    ,'required': false ,'test-value':''                         ,'title':'角色组 ' ,'desc':'*限管理员可用' }
         ,{ 'key':'level'                 ,'type':'integer'    ,'required': false ,'test-value':''                         ,'title':'0: 未激活用户 1：普通用户 5：普通管理员  9：超级管理员' ,'desc':'*限管理员可用' }
         ,{ 'key':'status'                ,'type':'integer'    ,'required':false ,'test-value':''                         ,'title':'0: 已删除  1: 正常 2: 封号  3：禁言' ,'desc':'*限管理员可用' }
         ,{ 'key':'create_time'           ,'type':'datetime'   ,'required':false ,'test-value':''                         ,'title':'创建时间' ,'desc':'*限管理员可用' }
         ,{ 'key':'modify_time'           ,'type':'datetime'   ,'required':false ,'test-value':''                         ,'title':'修改时间' ,'desc':'*限管理员可用' }
        ]
      });



apiList.push({
        'title':'用户:更新'
        ,'desc':''
        ,'time':'2016-11-06 11:11:53'
        ,'action':'user/update'
        ,'method':'post'
        ,'request':[
           { 'key':'id'                    ,'type':'int'        ,'required': true ,'time':'' ,'test-value':'1'                        ,'title':'id' ,'desc':'' }
          ,{ 'key':'telephone'             ,'type':'string'     ,'required':false ,'time':'' ,'test-value':''                         ,'title':'用户手机号' ,'desc':'' }
          ,{ 'key':'username'              ,'type':'string'     ,'required':false ,'time':'' ,'test-value':''                         ,'title':'用户名' ,'desc':'' }
          ,{ 'key':'email'                 ,'type':'string'     ,'required':false ,'time':'' ,'test-value':''                         ,'title':'邮箱' ,'desc':'' }
          ,{ 'key':'password'              ,'type':'md5'        ,'required':false ,'time':'' ,'test-value':''                         ,'title':'密码' ,'desc':'' }
          ,{ 'key':'status'                ,'type':'integer'    ,'required':false ,'time':'' ,'test-value':''                         ,'title':'0: 已删除  1: 正常 2: 封号  3：禁言' ,'desc':'' }
//           ,{ 'key':'last_login_time'       ,'type':'datetime'   ,'required':false ,'time':'' ,'test-value':''                         ,'title':'最后一次登录时间' ,'desc':'' }
//           ,{ 'key':'last_password_time'    ,'type':'datetime'   ,'required':false ,'time':'' ,'test-value':''                         ,'title':'最后一次密码修改时间' ,'desc':'' }
          ,{ 'key':'level'                 ,'type':'integer'    ,'required':false ,'time':'' ,'test-value':''                         ,'title':'0: 未激活用户 1：普通用户 5：普通管理员  9：超级管理员' ,'desc':'*限管理员可用' }
//           ,{ 'key':'create_time'           ,'type':'datetime'   ,'required':false ,'time':'' ,'test-value':''                         ,'title':'创建时间' ,'desc':'*限管理员可用' }
//           ,{ 'key':'modify_time'           ,'type':'datetime'   ,'required':false ,'time':'' ,'test-value':''                         ,'title':'修改时间' ,'desc':'*限管理员可用' }
        ]
      });



apiList.push({
        'title':'用户:列表'
        ,'desc':''
        ,'time':'2016-11-06 11:11:53'
        ,'action':'user/list'
        ,'method':'get'
        ,'request':[
           { 'key':'page'                  ,'type':'int'        ,'required': true ,'time':'' ,'test-value':'1'                        ,'title':'分页，第一页为1，第二页为2，最后一页为-1' ,'desc':'' }
          ,{ 'key':'size'                  ,'type':'int'        ,'required': true ,'time':'' ,'test-value':'10'                       ,'title':'分页大小' ,'desc':'' }
          ,{ 'key':'iscountall'            ,'type':'bool'       ,'required':false ,'time':'' ,'test-value':''                         ,'title':'是否统计总数 1是 0否' ,'desc':'' }
          ,{ 'key':'order'                 ,'type':'string'     ,'required':false ,'time':'' ,'test-value':''                         ,'title':'排序方式' ,'desc':'限以下值（id , telephone , username , email , level , status , last_login_time , last_password_time , create_time , modify_time）' }
          ,{ 'key':'isreverse'             ,'type':'int'        ,'required':false ,'time':'' ,'test-value':''                         ,'title':'是否倒序 0否 1是' ,'desc':'（默认1）' }
          ,{ 'key':'ids'                   ,'type':'string'     ,'required':false ,'time':'' ,'test-value':''                         ,'title':'多个id用逗号隔开' ,'desc':'' }
          ,{ 'key':'id'                    ,'type':'integer'    ,'required':false ,'time':'' ,'test-value':''                         ,'title':'' ,'desc':'' }
          ,{ 'key':'telephone'             ,'type':'string'     ,'required':false ,'time':'' ,'test-value':''                         ,'title':'用户手机号' ,'desc':'' }
          ,{ 'key':'username'              ,'type':'string'     ,'required':false ,'time':'' ,'test-value':''                         ,'title':'用户名' ,'desc':'' }
          ,{ 'key':'email'                 ,'type':'string'     ,'required':false ,'time':'' ,'test-value':''                         ,'title':'邮箱' ,'desc':'' }
          ,{ 'key':'password'              ,'type':'md5'        ,'required':false ,'time':'' ,'test-value':''                         ,'title':'密码' ,'desc':'' }
          ,{ 'key':'level'                 ,'type':'integer'    ,'required':false ,'time':'' ,'test-value':''                         ,'title':'0: 未激活用户 1：普通用户 5：普通管理员  9：超级管理员' ,'desc':'' }
          ,{ 'key':'status'                ,'type':'integer'    ,'required':false ,'time':'' ,'test-value':''                         ,'title':'0: 已删除  1: 正常 2: 封号  3：禁言' ,'desc':'*限管理员可用' }
          ,{ 'key':'last_login_timestart'  ,'type':'datetime'   ,'required':false ,'time':'' ,'test-value':''                         ,'title':'>=起始时间（之后）：最后一次登录时间' ,'desc':'' }
          ,{ 'key':'last_login_timeend'    ,'type':'datetime'   ,'required':false ,'time':'' ,'test-value':''                         ,'title':'<结束时间（之前）：最后一次登录时间' ,'desc':'' }
          ,{ 'key':'last_password_timestart' ,'type':'datetime'   ,'required':false ,'time':'' ,'test-value':''                         ,'title':'>=起始时间（之后）：最后一次密码修改时间' ,'desc':'' }
          ,{ 'key':'last_password_timeend' ,'type':'datetime'   ,'required':false ,'time':'' ,'test-value':''                         ,'title':'<结束时间（之前）：最后一次密码修改时间' ,'desc':'' }
          ,{ 'key':'create_timestart'      ,'type':'datetime'   ,'required':false ,'time':'' ,'test-value':''                         ,'title':'>=起始时间（之后）：创建时间' ,'desc':'' }
          ,{ 'key':'create_timeend'        ,'type':'datetime'   ,'required':false ,'time':'' ,'test-value':''                         ,'title':'<结束时间（之前）：创建时间' ,'desc':'' }
          ,{ 'key':'modify_timestart'      ,'type':'datetime'   ,'required':false ,'time':'' ,'test-value':''                         ,'title':'>=起始时间（之后）：修改时间' ,'desc':'' }
          ,{ 'key':'modify_timeend'        ,'type':'datetime'   ,'required':false ,'time':'' ,'test-value':''                         ,'title':'<结束时间（之前）：修改时间' ,'desc':'' }
          ,{ 'key':'keyword'               ,'type':'string'     ,'required':false ,'time':'' ,'test-value':''                         ,'title':'检索关键字' ,'desc':'' }
        ]
      });



apiList.push({
        'title':'用户:详情'
        ,'desc':''
        ,'time':'2016-11-06 11:11:53'
        ,'action':'user/detail'
        ,'method':'get'
        ,'request':[
           { 'key':'id'                    ,'type':'int'        ,'required': true ,'time':'' ,'test-value':'1'                        ,'title':'id' ,'desc':'' }
        ]
      });



apiList.push({
        'title':'用户:修改密码（不登录，需要验证短信）'
        ,'desc':''
        ,'time':'2016-11-06 11:11:53'
        ,'action':'/user/update_with_verify_code'
        ,'method':'post'
        ,'request':[
           { 'key':'telephone'             ,'type':'string'     ,'required': true ,'test-value':'13774298448'                         ,'title':'用户手机号' ,'desc':'' }
          ,{ 'key':'verify_code'            ,'type':'string'    ,'required': true ,'test-value':'123456'                             ,'title':'验证码' ,'desc':'必需' }
          ,{ 'key':'newpassword'           ,'type':'md5'        ,'required': true ,'test-value':'123456'                              ,'title':'密码' ,'desc':'' }
        ]
      });

apiList.push({
        'title':'用户:修改密码／邮箱/手机（需要登录，并提供原始密码）'
        ,'desc':'（修改手机需要验证新手机）<br/>（联合登录用户，初次设定密码不需要原始密码）'
        ,'time':'2016-11-06 11:11:53'
        ,'action':'/user/update_with_oldpassword'
        ,'method':'post'
        ,'request':[
           { 'key':'oldpassword'              ,'type':'md5'        ,'required': false ,'test-value':'123456'                              ,'title':'旧密码' ,'desc':'' }
          ,{ 'key':'newpassword'              ,'type':'md5'        ,'required': false ,'test-value':'654321'                              ,'title':'新密码' ,'desc':'' }
          ,{ 'key':'email'                 ,'type':'string'     ,'required':false ,'time':'' ,'test-value':'wyx2@haoxitech.com'                         ,'title':'邮箱' ,'desc':'' }
          ,{ 'key':'telephone'             ,'type':'string'     ,'required': false ,'test-value':'13774298448'                         ,'title':'用户手机号' ,'desc':'' }
          ,{ 'key':'verify_code'            ,'type':'string'    ,'required': false ,'test-value':'123456'                             ,'title':'验证码' ,'desc':'如果修改手机号，需要验证新手机号。' }
        ]
      });


apiList.push({
        'title':'用户:登录'
        ,'desc':''
        ,'time':'2016-11-06 11:11:53'
        ,'action':'/user/login'
        ,'method':'post'
        ,'request':[
           { 'key':'account'               ,'type':'string'     ,'required':true ,'test-value':'13774298448'                     ,'title':'支持手机号、用户名、邮箱登录' ,'desc':'' }
          ,{ 'key':'password'              ,'type':'md5'     ,'required':true ,'test-value':'123456'                         ,'title':'密码' ,'desc':'' }
       ]
      });

apiList.push({
        'title':'用户:联合登录'
        ,'desc':''
        ,'time':'2016-11-06 11:11:53'
        ,'action':'/user/union_login'
        ,'method':'post'
        ,'request':[
           { 'key':'union_type'              ,'type':'int'     ,'required':true ,'test-value':'2'                         ,'title':'登录方式：2QQ 3微博 4微信' ,'desc':'' }
          ,{ 'key':'union_token'             ,'type':'string'     ,'required':true ,'test-value':'398ADCFAED79A49ACBE516EE89F7950B'                     ,'title':'联合登录唯一识别码' ,'desc':'' }
       ]
      });

apiList.push({
        'title':'用户:登录后绑定对应联合登录'
        ,'desc':'登录后调用该接口可新增绑定'
        ,'time':'2016-11-06 11:11:53'
        ,'action':'/user/set_union_login'
        ,'method':'post'
        ,'request':[
           { 'key':'union_type'              ,'type':'int'     ,'required':true ,'test-value':'2'                         ,'title':'登录方式：2QQ 3微博 4微信' ,'desc':'' }
          ,{ 'key':'union_token'             ,'type':'string'     ,'required':true ,'test-value':'398ADCFAED79A49ACBE516EE89F7950B'                     ,'title':'联合登录唯一识别码' ,'desc':'' }
       ]
      });

apiList.push({
        'title':'用户:注销'
        ,'desc':'用户点击注销，本地删除其登录信息，同时调用本接口以便服务器解除其账号与设备的绑定信息。'
        ,'time':'2016-11-06 11:11:53'
        ,'action':'/user/log_out'
        ,'method':'get'
        ,'request':[

       ]
      });

apiList.push({
        'title':'用户:我的信息'
        ,'desc':''
        ,'time':'2016-11-06 11:11:53'
        ,'action':'/user/get_my_detail'
        ,'method':'get'
        ,'request':[

          ]
      });



apiList.push({
        'title':'用户:删除（仅供管理员测试期间用)'
        ,'desc':''
        ,'time':'2016-11-06 11:11:53'
        ,'action':'/user/delete'
        ,'method':'post'
        ,'request':[
           { 'key':'ids'                   ,'type':'string'     ,'required':false ,'test-value':''                         ,'title':'多个id用逗号隔开' ,'desc':'' }
          ,{ 'key':'id'                    ,'type':'integer'    ,'required':false ,'test-value':''                         ,'title':'' ,'desc':'' }
          ,{ 'key':'telephone'             ,'type':'string'     ,'required':false ,'test-value':'13112345678'              ,'title':'用户手机号' ,'desc':'' }
        ]
      });

*/