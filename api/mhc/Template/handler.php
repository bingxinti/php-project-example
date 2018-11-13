<?php
/**
 * [ Template-description ]
 * @package Controller
 * @author Tool
 * @since 1.0
 * @version 1.0
 */

class TemplateHandler extends BaseHandler
{
    public static $cache         = array();//
    public static $isUseCache    = True;//类是否开启类缓存

    /**
     * 根据主键值查询单条记录
     * @return TemplateModel 对应的model 实例
     */
    public static function loadModelById($pId=null)
    {
        return parent::loadModelById($pId);
    }

    /**
     * 根据筛选条件，筛选获得对象数组的第一个数据
     * @see AbstractHandler::loadModelList()
     * @return TemplateModel         对象模型
     */
    public static function loadModelFirstInList($pWhere=array(),$pOrder=null,$pPageIndex=1,$pPageSize=1,&$pCountThis=-1)
    {
        return parent::loadModelFirstInList($pWhere,$pOrder,$pPageIndex,$pPageSize,$pCountThis);
    }

    /**
    * 指定ids查询，根据多个主键值查询多条记录,注意，这里返回的数组以传入的id顺序一致
    * @param  array $pIds 数组id,或逗号隔开的id字符串
    * @return TemplateModel[]        对应的model 实例数组
    */
    public static function loadModelListByIds($pIds=null)
    {
        return parent::loadModelListByIds($pIds);
    }

    /**
     * 批量查询，根据筛选条件，筛选获得对象数组
     * @param  array   $pWhere     这是一个数组字典，用来约束筛选条件，支持多种表达方式，如array('id'=>'13','replyCount>'=>5,'lastmodifTime>now()'),注意其中的key value的排列方式。
     * @param  string  $pOrder     排序方式，如'lastmodifytime desc'
     * @param  integer $pPageIndex 分页，第一页为1，第二页为2
     * @param  integer  $pPageSize  分页数据量
     * @param  integer  $pCountThis  计数变量，注意，若需要进行计数统计，则调用此处时需传入一个变量，当方法调用结束后，会将计数赋值给该变量。
     * @return TemplateModel[]         对象模型数组
     */
    public static function loadModelList($pWhere=array(),$pOrder=null,$pPageIndex=1,$pPageSize=DEFAULT_PAGE_SIZE,&$pCountThis=-1)
    {
        return parent::loadModelList($pWhere,$pOrder,$pPageIndex,$pPageSize,$pCountThis);
    }

    /**
     * 存储或更新模型对象
     * @param  object $pModel 新建或改动后的模型
     * @return TemplateModel         返回更新后的模型对象
     */
    public static function saveModel($pModel)
    {
        return parent::saveModel($pModel);
    }
}