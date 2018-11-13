/**
 * [ 接口测试 ApiTest Created by Tool on 2018-11-13 15:07 ]
 * @author Tool
 * @since 1.0
 * @version 1.0
 */

apiList.push(
{
  'title':'接口测试:新建',
  'desc':'',
  'time':'2018-11-13 15:07:42',
  'action':'api_test/add',
  'method':'post',
  'request':
  [
    {'key':'name', 'type':'string', 'required': false, 'time':'', 'test-value':'', 'title':'名字', 'desc':'长度25' }
    ,{'key':'age', 'type':'integer', 'required': false, 'time':'', 'test-value':'', 'title':'年龄', 'desc':'' }
  ]
});

apiList.push(
{
  'title':'接口测试:更新',
  'desc':'',
  'time':'2018-11-13 15:07:42',
  'action':'api_test/update',
  'method':'post',
  'request':
  [
    {'key':'id', 'type':'string', 'required': true, 'time':'', 'test-value':'', 'title':'修改数据的ID', 'desc':'' }
//     ,{'key':'id', 'type':'string', 'required': false, 'time':'', 'test-value':'', 'title':'主键自增ID', 'desc':'' }
//     ,{'key':'dflag', 'type':'integer', 'required': false, 'time':'', 'test-value':'', 'title':'是否有效 1有效 0无效', 'desc':'' }
//     ,{'key':'create_time', 'type':'datetime', 'required': false, 'time':'', 'test-value':'', 'title':'创建时间', 'desc':'' }
//     ,{'key':'modify_time', 'type':'datetime', 'required': false, 'time':'', 'test-value':'', 'title':'修改时间', 'desc':'' }
    ,{'key':'name', 'type':'string', 'required': false, 'time':'', 'test-value':'', 'title':'名字', 'desc':'长度25' }
    ,{'key':'age', 'type':'integer', 'required': false, 'time':'', 'test-value':'', 'title':'年龄', 'desc':'' }
  ]
});

apiList.push(
{
  'title':'接口测试:列表',
  'desc':'',
  'time':'2018-11-13 15:07:42',
  'action':'api_test/list',
  'method':'get',
  'request':
  [
    {'key':'page', 'type':'integer', 'required': false, 'time':'', 'test-value':'1', 'title':'第几分页', 'desc':'' }
    ,{'key':'size', 'type':'integer', 'required': false, 'time':'', 'test-value':'10', 'title':'每页显示的数量', 'desc':'' }
    ,{'key':'attribute_key', 'type':'integer', 'required': false, 'time':'', 'test-value':'', 'title':'指定键值', 'desc':'' }
    ,{'key':'load_attribute_type', 'type':'integer', 'required': false, 'time':'', 'test-value':'', 'title':'是否加载模型类型 1是 0否', 'desc':'' }
    ,{'key':'iscountall', 'type':'integer', 'required': false, 'time':'', 'test-value':'', 'title':'是否统计总数 1是 0否', 'desc':'' }
    ,{'key':'order', 'type':'string', 'required': false, 'time':'', 'test-value':'', 'title':'排序的字段 默认id', 'desc':'' }
    ,{'key':'isreverse', 'type':'integer', 'required': false, 'time':'', 'test-value':'', 'title':'是否倒序 0否 1是 默认1', 'desc':'' }
    ,{'key':'ids', 'type':'string', 'required': false, 'time':'', 'test-value':'', 'title':'多个id用逗号隔开', 'desc':'' }
    ,{'key':'id', 'type':'string', 'required': false, 'time':'', 'test-value':'', 'title':'主键自增ID', 'desc':'' }
    ,{'key':'dflag', 'type':'integer', 'required': false, 'time':'', 'test-value':'', 'title':'是否有效 1有效 0无效', 'desc':'' }
    ,{'key':'create_time', 'type':'datetime', 'required': false, 'time':'', 'test-value':'', 'title':'创建时间', 'desc':'' }
    ,{'key':'modify_time', 'type':'datetime', 'required': false, 'time':'', 'test-value':'', 'title':'修改时间', 'desc':'' }
    ,{'key':'name', 'type':'string', 'required': false, 'time':'', 'test-value':'', 'title':'名字', 'desc':'长度25' }
    ,{'key':'age', 'type':'integer', 'required': false, 'time':'', 'test-value':'', 'title':'年龄', 'desc':'' }
  ]
});

apiList.push(
{
  'title':'接口测试:详情',
  'desc':'',
  'time':'2018-11-13 15:07:42',
  'action':'api_test/detail',
  'method':'get',
  'request':
  [
    {'key':'id', 'type':'string', 'required': false, 'time':'', 'test-value':'1', 'title':'详情数据的ID', 'desc':'' }
    ,{'key':'load_attribute_type', 'type':'integer', 'required': false, 'time':'', 'test-value':'', 'title':'是否加载模型类型 1是 0否', 'desc':'' }
    ,{'key':'dflag', 'type':'integer', 'required': false, 'time':'', 'test-value':'', 'title':'是否有效 1有效 0无效', 'desc':'' }
    ,{'key':'create_time', 'type':'datetime', 'required': false, 'time':'', 'test-value':'', 'title':'创建时间', 'desc':'' }
    ,{'key':'modify_time', 'type':'datetime', 'required': false, 'time':'', 'test-value':'', 'title':'修改时间', 'desc':'' }
    ,{'key':'name', 'type':'string', 'required': false, 'time':'', 'test-value':'', 'title':'名字', 'desc':'长度25' }
    ,{'key':'age', 'type':'integer', 'required': false, 'time':'', 'test-value':'', 'title':'年龄', 'desc':'' }
  ]
});
