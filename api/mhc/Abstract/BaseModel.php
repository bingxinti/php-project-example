<?php
/**
 * @package Model
 * @author Demon
 * @since 1.0
 * @version 1.0
 */
class BaseModel extends AbstractModel
{
    /**
     * $label = static::loadModelTypeLabel('logType',$this->getLogType());
     * [loadModelTypeLabel 转换模型类型对应的标签]
     * @param  [type] $attributeName  [description]
     * @param  [type] $attributeValue [description]
     * @return [type]                 [description]
     */
    public function loadModelTypeLabel($attributeName = null, $attributeValue = null)
    {
        $label       = '';
        $className   = static::loadClassName();
        $handlerName = str_replace('Model','Handler',$className);
        if(method_exists($handlerName, 'loadAttributeTypeLabel'))
        {
            $label = $handlerName::loadAttributeTypeLabel($attributeName,$attributeValue);
        }
        return $label;
    }

    /**
     * [setProperties description]
     */
    public function setAttributes($attributeKeys = [], $params =[])
    {
		// $attributes = $this->getObjModel()->getAttributes();
    	foreach ($attributeKeys as $attribute)
    	{
            $attribute = Utility::camelCase($attribute);
            // DD($attribute, isset($params[$attribute]), $params);
    		if(isset($params[$attribute]))
    		{
    			$method = "set{$attribute}";
                // DD($method, method_exists($this,$method));
    			if (method_exists($this,$method))
    			{
                    $value = $params[$attribute];
                    $value = is_array($value) ? implode(',',$value): $value;
            		call_user_func(array($this, $method), $value);
    			}
    		}
    	}
    	return $this;
    }

    public function setAttrData($params =[])
    {
        $oDBModel = $this->loadObjModel();
        $cacheKey = 'table_field_'.$oDBModel->tableName;
        $attributesList = W2FileCache::getKey($cacheKey);
        if(empty($attributesList))
        {
            $attributesList = $oDBModel->getAttributes();
            W2FileCache::setKey($cacheKey, $attributesList);
        }
        $attributes = [];
        foreach ($attributesList as $key => $value)
        {
            $attributes[] = Utility::camelCase($value['name']);;
        }
        // D($attributes);
        // D($params);
        return static::setAttributes($attributes, $params);
    }


    /**
     * 初始化方法，如果需要，各模型必须重写此处
     * @param int|array 如果是整数, 赋值给对象的id,如果是数组, 给对象的逐个属性赋值
     * @return TestModel
     */
    public static function instance($pData=null) {
        $_o = parent::instanceModel(static::loadClassName(), $pData);

        $tmpVars = get_object_vars($_o);
        $tmpVars['snapshot'] = '';
        $_o->snapshot = $tmpVars;//初始化完成后，记录当前状态
        return $_o;
    }

    public function loadObjModel()
    {
    	$tableName  = lcfirst(str_replace('Model','', static::loadClassName()));
    	$model      = new DBModel($tableName);
    	return $model;
    }


    /** @var array [特殊的一个变量，用于和model的属性进行合并返回] */
    protected $propertieInfo;

    public function getPropertieInfo()
    {
        return $this->propertieInfo;
    }

    public function setPropertieInfo($propertieInfo)
    {
        $this->propertieInfo = $propertieInfo;

        return $this;
    }

    public function addPropertieInfo($key = '', $value = null)
    {
        $propertieInfo = $this->getPropertieInfo();
        empty($propertieInfo) && $propertieInfo = [];
        $propertieInfo[$key] = $value;
        $this->setPropertieInfo($propertieInfo);
        return $this;
    }


    /**
     * 获取模型实例的所有属性转化成数组  实例-》数组
     * @param string|array $p_exclude 排除字段
     * @return array 类的所用属性
     */
    public function properties($p_foundDeepModelList=array(),$p_exclude=null,$p_path=array(),$rootModel=null) {
        $_classid = get_class($this).'.'.$this->getId();
        if (in_array($_classid,$p_foundDeepModelList))
        {
            return null;
        }
        else
        {
            $p_foundDeepModelList[] =$_classid;
        }
        $_ps = array();
        if (is_string($p_exclude))
        {
            $p_exclude = explode(',', $p_exclude);
        }
        $isRootModel = is_null($rootModel);
        if (is_null($rootModel))
        {
            $rootModel = get_class($this);
        }
        $_ms = get_class_methods(get_class($this));
        $getPropertieInfo = 'getPropertieInfo';
        foreach ($_ms as $_name) {
            $_nameGet = null;
            if (substr($_name, 0, 3) == 'get'  && $_name != $getPropertieInfo) {
                $_nameGet = $_name;
            }
            if (isset($_nameGet))
            {
                $_n = lcfirst(substr($_nameGet, 3));
                if (
                        ($p_exclude===null || (is_array($p_exclude) && !in_array($_n, $p_exclude) ))
                        && (!in_array($_n, static::$authViewDisabled) && !in_array('*', static::$authViewDisabled))
                    )
                {
                    $result = call_user_func(array($this, $_nameGet));
                    static::propertyDeep($_ps,$_n,$result,$p_foundDeepModelList,$p_path,$rootModel);
                }
            }
        }
        if ($isRootModel && isset($rootModel::$searchPaths) && count($rootModel::$searchPaths)>0 )
        {
            W2Array::unsetEmptyArray($_ps['results']);
        }

        if(in_array($getPropertieInfo, $_ms))
        {
           $arrPropertieInfo = call_user_func(array($this, $getPropertieInfo));
            // D($getPropertieInfo);
           if(is_array($arrPropertieInfo))
           {
            // D($_ps);
            $_ps = array_merge($_ps, $arrPropertieInfo);
            // D($_ps);
            // exit;
           }
        }
        return $_ps;
    }

}