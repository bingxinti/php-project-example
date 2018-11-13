<?php
/**
 * @package Handler
 * @author Demon
 * @since 1.0
 * @version 1.0
 */
class BaseHandler extends AbstractHandler
{
    /** [loadModelAttributes 获取属性字段] */
    public static function loadModelAttributes()
    {
        $oDBModel = static::newDBModel();
        $cacheKey = 'table_field_'.$oDBModel->tableName;
        $attributes = W2FileCache::getKey($cacheKey);
        if(empty($attributes))
        {
            $attributes = $oDBModel->getAttributes();
            W2FileCache::setKey($cacheKey, $attributes);
        }
        return $attributes;
    }


    /** [loadModelListAll 加载所有列表] */
    public static function loadModelListAll($pWhere=array(),$pOrder=null,$pPageIndex=1,$pPageSize=DEFAULT_PAGE_SIZE,&$pCountThis=-1)
    {
        $pPageSize = 9999;
        $list = static::loadModelList($pWhere,$pOrder,$pPageIndex,$pPageSize,$pCountThis);
        return $list;
    }

    /** [loadModelList 加载列表] */
    public static function loadModelList($pWhere=array(),$pOrder=null,$pPageIndex=1,$pPageSize=DEFAULT_PAGE_SIZE,&$pCountThis=-1)
    {
        // 如果条件里没有dflag 默认给予1
        // if(!isset($pWhere['dflag']) && isset(static::loadModelAttributes()['dflag']))
        if(!array_key_exists('dflag',$pWhere) && isset(static::loadModelAttributes()['dflag']))
        {
            $pWhere['dflag'] = DFLAG_NORMAL;
        }
        // if(isset($pWhere['status']) &&  empty($pWhere['status'])) unset($pWhere['status']);
        $list = parent::loadModelList($pWhere,$pOrder,$pPageIndex,$pPageSize,$pCountThis);
        return $list;
    }

    /**
     * [loadAttributeTypeModel 获取模型属性字典]
     * @param  [type] $attributeName  [属性名称]
     * @param  [type] $attributeValue [属性值]
     * @return [type]                 [字典列表]
     */
    public static function loadAttributeTypeModel($attributeName = null, $attributeValue = null)
    {
        $attributeType          = [];
        $where                  = [];
        $where['modelName']     = static::getTabelName();
        is_null($attributeName) OR  $where['attributeName']   = $attributeName;
        if(!is_null($attributeValue))
        {
            if(strpos($attributeValue,',')!==false)
            {
                $where[] = sprintf(' attributeValue in (%s)', $attributeValue);
            }
            else
            {
                $where['attributeValue'] = $attributeValue;
            }

        }
        $attributeType = AttributeTypeHandler::loadModelListAll($where);

        /*if(isset($_REQUEST['debug']))
        {
            // D($attributeValue);
            // D($where);
            // D($attributeType);
            // exit;
        }*/
        return $attributeType;
    }


    /**
     * [loadAttributeType 获取模型属性字典]
     * @param  [type] $attributeName  [属性名称]
     * @param  [type] $attributeValue [属性值]
     * @return [array]            [字典列表]
     */
    public static function loadAttributeType($attributeName = null, $attributeValue = null)
    {
        $attributeType          = [];
        $attributeTypeModelList = static::loadAttributeTypeModel($attributeName,$attributeValue);
        foreach ($attributeTypeModelList as $key => $attributeTypeModel)
        {
            $attributeTypeInfo                   = [];
            $attributeTypeInfo['attributeName']  = $attributeTypeModel->getAttributeName();
            $attributeTypeInfo['attributeLabel'] = $attributeTypeModel->getAttributeLabel();
            $attributeTypeInfo['attributeValue'] = $attributeTypeModel->getAttributeValue();
            $attributeTypeInfo['modelType']      = $attributeTypeModel->getModelType();
            if($attributeName == null)
            {
                $attributeType[$attributeTypeInfo['attributeName']][] = $attributeTypeInfo;
            }
            else
            {
                $attributeType[] = $attributeTypeInfo;
            }
        }
        return $attributeType;
    }


    /**
     * [loadAttributeType 获取类型对应的标签]
     * @param  string $attributeValue [description]
     * @return [type]                 [description]
     */
    public static function loadAttributeTypeLabel($attributeName = null, $attributeValue = null)
    {
        if(is_null($attributeValue))
        {
            return null;
        }
        $attributeTypeLabel = [];
        $attributeTypeModelList =  static::loadAttributeTypeModel($attributeName, $attributeValue);
        // D($attributeName);
        // D($attributeValue);
        // D($attributeTypeModelList);
        if(!empty($attributeTypeModelList))
        {
            foreach ($attributeTypeModelList as $key => $attributeTypeModel)
            {
                if(method_exists($attributeTypeModel, 'getAttributeLabel'))
                {
                    $attributeTypeLabel[] = $attributeTypeModel->getAttributeLabel();
                }
            }
        }

        $attributeTypeLabel  = implode(',', $attributeTypeLabel);
        return $attributeTypeLabel;
    }

    /**
     * 当前handler所面向的表的主键名，如果没有就为空。（开发时最好每个表都指定主键）
     * @return string 表名
     */
    public static function getTabelIdName()
    {
        if(empty(static::$tableIdName))
        {
            return 'id';
        }
        return static::$tableIdName;
    }

    // 重写父类逻辑
    public static function getModelName()
    {
        static::$modelName = ucfirst(str_replace('Handler','Model',get_called_class()));//取得对应的model类名
        return static::$modelName;
    }

    /**
     * 当前handler所面向的主要表名称，默认会从类名中截取字符作为表名
     * @return string 表名
     */
    public static function getTabelName()
    {
        static::$tableName = lcfirst(str_replace('Handler','',get_called_class()));
        static::$tableName = Utility::under_score(static::$tableName);
        return static::$tableName;
    }

    /**
     * 获得常规的表格字段数据
     * @return array 表格字段
     */
    public static function getTableDataKeys($isWithSingleQuote = false){
        $_dbModel = static::newDBModel();
        static::$tableDataKeys = $_dbModel->getMeta();
        if ($isWithSingleQuote)
        {
            $quotedKeys = array();
            foreach (static::$tableDataKeys as $key => $value) {
                $quotedKeys[] = '`'.trim($value,'`').'`';
            }
            return $quotedKeys;
        }
        return static::$tableDataKeys;
    }


    /**
     * 统计符合条件的数量
     * @param  array $pWhere 条件
     * @return int          总数
     */
    public static function count($pWhere=array())
    {
        if(!isset($pWhere['dflag']) && isset(static::loadModelAttributes()['dflag']))
        {
            $pWhere['dflag'] = DFLAG_NORMAL;
        }
        // if(isset($pWhere['dflag']) &&  empty($pWhere['dflag'])) unset($pWhere['dflag']);
        return static::selectField('count(*)',$pWhere);
    }


    /**
     * 存储或更新模型对象
     * @param  AbstractModel $pModel 新建或改动后的模型
     * @return AbstractModel         返回更新后的模型对象
     */
    public static function saveModel($pModel)
    {
        if (!isset($pModel) || get_class($pModel)!= static::getModelName())
        {
            throw new Exception('此处需要传入'.static::getModelName().'类型的对象');
        }

        $_dbModel = static::newDBModel();

        $_updateData = array();
        foreach ($pModel->propertiesModified() as $_key => $_value) {
            if ($_value===null)
            {
                continue;
            }
            /** 更新缓存池 */
            if ( in_array($_key, static::getTableDataKeys() )  )
            {
                // if ($_key!='id' && $_key!=static::getTabelIdName() )
                // {
                    $_updateData[$_key] = $_value;
                // }

                // if ((is_int($_value) || (is_string($_value) && strlen($_value)<10 ) ))
                // {
                //     $w2CacheKeyPool = sprintf('hao_%s_pool_list_%s_key_%s_value_%s'
                //                         ,AXAPI_PROJECT_NAME
                //                         ,static::getTabelName()
                //                         ,$_key
                //                         ,$_value
                //                         );
                //     W2Cache::resetCacheKeyPool($w2CacheKeyPool);
                //     AX_DEBUG('更新缓存池：'.$w2CacheKeyPool);
                // }

                $_valueOriginal = $pModel->properyOriginal($_key);
                if ((is_int($_valueOriginal) || (is_string($_valueOriginal) && strlen($_valueOriginal)<10 ) ))
                {
                    if ($_valueOriginal!==null)
                    {
                        $w2CacheKeyPool = sprintf('hao_%s_pool_list_%s_key_%s_value_%s'
                                            ,AXAPI_PROJECT_NAME
                                            ,static::getTabelName()
                                            ,$_key
                                            ,$_valueOriginal
                                            );
                        W2Cache::resetCacheKeyPool($w2CacheKeyPool);
                        AX_DEBUG('更新缓存池：'.$w2CacheKeyPool);
                    }
                }
            }
        }

        AX_DEBUG($_updateData);
        $newWhere = null;
        if ($pModel->isNewModel())
        {
            /** 新数据 */
            // 新增时指定插入的新增ID
            $strTabelIdName = static::getTabelIdName();
            if(!isset($_updateData[$strTabelIdName]))
            {
                $tableName   = static::getTabelName();
                // $funcNextval = Utility::funcNextval($tableName);
                // if(!empty($funcNextval))
                // {
                //     $_updateData[$strTabelIdName] = $funcNextval;
                // }
            }

            $_dbModel -> insert($_updateData);
            static::updateCacheKeyPoolOfSql($_dbModel->sqlOfInsert($_updateData));//更新缓存池
            if (static::getTabelIdName()!=null)
            {
                $newWhere = $_dbModel->init()
                            ->where($_updateData)->order(sprintf('%s desc',static::getTabelIdName()))
                            ->field(static::getTabelIdName())
                            ->selectSingle();
            }
            else
            {
                $newWhere = $_updateData;
            }
        }
        else
        {
            if (static::getTabelIdName()!=null)
            {
                $newWhere = array(static::getTabelIdName() => $pModel->getId());
                $_dbModel -> where($newWhere)
                          -> limit(1)
                          ->update($_updateData);
                static::resetW2CacheByModelId($pModel->getId());//更新缓存
            }
            else
            {
                $_dbModel -> where(static::filterTableDataKeysInArray($pModel->properiesOriginal(),true))
                          -> limit(1)
                          ->update($_updateData);
                $newWhere = static::filterTableDataKeysInArray($pModel->properiesValue(),true);
            }
            static::updateCacheKeyPoolOfSql($_dbModel->sqlOfUpdate($_updateData));//更新缓存池
        }
        return static::loadModelFirstInList($newWhere);
    }

    /** [saveModel description] */
    /*public static function saveModel($pModel)
    {
        $model = parent::saveModel($pModel);
        return $model;
    }*/
}