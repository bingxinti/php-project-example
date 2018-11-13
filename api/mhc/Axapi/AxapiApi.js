
apiList.push({
        // 'title':'公共接口:上传文件(支持图片，音频，视频，文档等)'
        'title':'公共接口:上传文件图片'
        ,'desc':''
        ,'time':'2017-12-07 11:10:52'
        ,'action':'axapi/up_file'
        ,'method':'post'
        ,'request':
        [
            { 'key':'file'              ,'type':'file'     ,'required': true ,'test-value':''                             ,'title':'上传图片' ,'desc':'' }
            ,{ 'key':'filePath'              ,'type':'string'     ,'required': false ,'test-value':''                             ,'title':'上传图片相对路径保存数据库的字段' ,'desc':'' }
            ,{ 'key':'upPath'              ,'type':'string'     ,'required': false ,'test-value':''                             ,'title':'图片服务器的物理路径' ,'desc':'' }
            ,{ 'key':'preview'              ,'type':'string'     ,'required': false ,'test-value':''                             ,'title':'预览路径' ,'desc':'' }
            ,{ 'key':'fileExists'              ,'type':'Boolean'     ,'required': false ,'test-value':''                             ,'title':'是否上传成功 true成功 false 失败' ,'desc':'' }
        ]
      });



apiList.push({
        'title':'公共接口:获取模型model'
        ,'desc':''
        ,'time':'2017-08-31 11:10:52'
        ,'action':'axapi/get_attribute_type'
        ,'method':'get'
        ,'request':
        [
            { 'key':'modelName'              ,'type':'string'     ,'required': false ,'test-value':'user'                             ,'title':'模型名称' ,'desc':'' }
            ,{ 'key':'attributeName'              ,'type':'string'     ,'required': false ,'test-value':''                             ,'title':'属性名称' ,'desc':'' }
            ,{ 'key':'attributeValue'              ,'type':'string'     ,'required': false ,'test-value':''                             ,'title':'属性值' ,'desc':'' }
            ,{ 'key':'isGroup'              ,'type':'string'     ,'required': false ,'test-value':''                             ,'title':'是否分组' ,'desc':'' }
        ]
      });
