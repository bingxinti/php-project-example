<?php
/**
 * 控制器基类
 * @package Controller
 * @author Demon
 * @since 1.0
 * @version 1.0
 */
class BaseController extends AbstractController
{
    public static function checkLoginUser()
    {
        if(empty(Utility::getIntCurrentUserID()))
        {
            throw new Exception('请登录后操作');
        }
    }

    public static function checkValidUserID($userID = 0)
    {
        $userCount = UserHandler::countAll(['id' => $userID]);
        if(empty($userCount))
        {
            throw new Exception('无效用户');
        }
    }
    // 是否后台客户端
    public static function checkIsAdmin()
    {
        static::checkLoginUser();

        if(!Utility::isAdmin())
        {
            throw new Exception('您没权限操作后台功能');
        }
    }

    /** [listParams description] */
    public static function queryParams($params = null, $attribute = [])
    {
        $pWhere = [];
        foreach ($params as $key => $value)
        {
            $key       = W2String::under_score($key);
            // 数据库 Where不区分大小写，统一大写比对 如  Userid = USERID
            $upKey     = strtoupper($key);
            $attribute = array_change_key_case($attribute,CASE_UPPER);

            // D($upKey,$attribute);
            if(isset($attribute[$upKey]) && isset($value) )
            {
                $value = is_array($value) ? implode(',',$value): $value;
                $pWhere[$upKey] =  sprintf('%s', $value);
            }
            // search_username   搜索开头的字段都like
            if( strpos($key,'search') === 0 )
            {
                $search = strtoupper(substr($key, 6,strlen($key)));
                // D($key,$search,$attribute);
                if(isset($attribute[$search]) && isset($value) )
                {
                    $pWhere[] = sprintf(" {$search} like '%%%s%%' ", $value);
                }
            }
        }
        return $pWhere;
    }

    /** [actionList 列表数据] */
    public static function actionList($params = null)
    {
        is_null($params) && $params = W2Params::instanceGet();

        $handlerlName = static::getHandlerName();
        $pWhere = static::queryParams($params->getParams(),  $handlerlName::loadModelAttributes() );

        $pPageIndex = 1;
        $pPageSize  = DEFAULT_PAGE_SIZE;
        $pCountThis = -1;
        $pOrder     = $handlerlName::getTabelIdName();

        empty($params->getString('ids')) OR  $pWhere['id in (%s)'] = $params->getString('ids');
        empty($params->getString('order')) OR  $pOrder             = $params->getString('order');
        empty($params->getInt('page')) OR  $pPageIndex             = max($params->getString('page'),1);
        empty($params->getInt('size')) OR  $pPageSize              = max($params->getString('size'),0);
        empty($params->getInt('iscountall')) OR  $pCountThis       = $params->getInt('iscountall');

        if( strpos($pOrder,' ') === false) // 如果没有手动指定排序
        {
            // ticket_id
            // $pOrder    = W2String::under_score($pOrder);
            $attribute = $handlerlName::loadModelAttributes();
            if(isset($attribute[$pOrder]))
            {
                $arrParams = $params->getParams();
                if(isset($arrParams['isreverse']) && $params->getInt('isreverse') == 0 )
                {
                    $pOrder .=' ASC';
                }
                else
                {
                    $pOrder .=' DESC';
                }
            }
        }

        $list   = $handlerlName::loadModelList($pWhere, $pOrder, $pPageIndex, $pPageSize, $pCountThis);
        foreach ($list as & $model)
        {
            $model = static::handlerlModel($model, $params);
        }



        if(!$params->isEmpty('attributeKey'))
        {
            $attributeKey = $params->getString('attributeKey');
            $listModel = null;
            foreach ($list as $key => $model)
            {
                $method = 'get' . $attributeKey;
                if(method_exists($model, $method))
                {
                    $attributeValue = call_user_func(array($model, $method));
                    $listModel[$attributeValue] = $model;
                }
            }
            is_null($listModel) OR  $list  = $listModel;
        }

        if (is_object($list) && get_class($list)=='HaoResult')
        {
            return $list;
        }

        $pageMax    = ($pCountThis>0 && $pPageSize>0)?(intval(($pCountThis-1)/$pPageSize)+1):-1;
        $pPageIndex = ($pPageIndex<0 && $pageMax>0)?($pageMax + $pPageIndex + 1):$pPageIndex;
        $haoResult  =  HaoResult::init(ERROR_CODE::$OK,$list, array('page'=>$pPageIndex,'size'=>$pPageSize,'pageMax'=>$pageMax,'countTotal'=>$pCountThis));

        if(!$params->isEmpty('loadAttributeType'))
        {
            $attributeType = $handlerlName::loadAttributeType();
            $haoResult->addExtraInfo('attributeType', $attributeType);
        }
        return $haoResult;
    }

    /** [actionDetail 详情数据] */
    public static function actionDetail($params = null)
    {
        is_null($params) && $params = W2Params::instanceGet();
        $handlerlName = static::getHandlerName();
        $pWhere       = static::queryParams($params->getParams(),  $handlerlName::loadModelAttributes() );
        // D($pWhere);
        $model        = $handlerlName::loadModelFirstInList($pWhere);
        if(!is_object($model)) return HaoResult::init(ERROR_CODE::$DATA_EMPTY);
        $model = static::handlerlModel($model, $params);
        $haoResult    =  HaoResult::init(ERROR_CODE::$OK,$model);
        if(!$params->isEmpty('loadAttributeType'))
        {
            $attributeType = $handlerlName::loadAttributeType();
            $haoResult->addExtraInfo('attributeType', $attributeType);
        }
        return $haoResult;
    }

    /** [actionDelete description] */
    public static function actionDelete($params = null)
    {
        is_null($params) && $params = W2Params::instancePost();
        $handlerlName = static::getHandlerName();
        $model = $handlerlName::loadModelById($params->getInt('id'));
        // D($model,$handlerlName, $params);
        if(!is_object($model)) return HaoResult::init(ERROR_CODE::$NO_MODEL_FOUND);

        if(method_exists($model,'setStatus'))
        {
            $model->setStatus(DFLAG_DISABLED);
        }

        // $func = new ReflectionClass('HaoResult');
        // D($func);
        // exit;

        $_model = $handlerlName::saveModel($model);
        $haoResult =  HaoResult::initOK($model);
        return $haoResult;
    }


    /**
     * [updateModelAttributes 修改和删除数据，默认情况下，对字段的赋值]
     * @param  [type] $model         [description]
     * @param  [type] $params        [description]
     * @param  [type] $arrAttributes [description]
     * @param  string $editType      [description]
     * @return [type]                [description]
     */
    private static function updateModelAttributes($model = null, $params = null, $arrAttributes = null, $editType = '')
    {
        // 创建数据
        if($editType == 'add')
        {
            // 默认设置创建时间
            if(method_exists($model,'setCreateTime'))
            {
                if(!empty($arrAttributes['create_time']))
                {
                    switch ($arrAttributes['create_time']['type'])
                    {
                        case 'int':
                        case 'integer':
                        case 'mediumint':
                        case 'bigint':
                            $model->setCreateTime(time());
                            break;

                        case 'datetime':
                            $model->setCreateTime(date('Y-m-d H:i:s'),time());
                            break;
                    }
                }
            }

            // 默认设置创建时间
            if(method_exists($model,'setCreatetime'))
            {
                if(!empty($arrAttributes['createtime']))
                {
                    switch ($arrAttributes['createtime']['type'])
                    {
                        case 'int':
                        case 'integer':
                        case 'mediumint':
                        case 'bigint':
                            $model->setCreateTime(time());
                            break;

                        case 'datetime':
                            $model->setCreateTime(date('Y-m-d H:i:s'),time());
                            break;
                    }
                }
            }

            if(method_exists($model,'setDflag'))
            {
                $model->setDflag(DFLAG_NORMAL);
            }
        }

        // 修改数据
        else if($editType == 'update')
        {
             // 默认设置修改时间
            if(!empty($arrAttributes['modify_time']))
            {
                switch ($arrAttributes['modify_time']['type'])
                {
                    case 'int':
                    case 'integer':
                    case 'mediumint':
                    case 'bigint':
                        $model->setModifyTime(time());
                        break;

                    case 'datetime':
                        $model->setModifyTime(date('Y-m-d H:i:s'),time());
                        break;
                }
            }

            // 默认设置创建时间
            if(method_exists($model,'setModifytime'))
            {
                if(!empty($arrAttributes['modifytime']))
                {
                    switch ($arrAttributes['modifytime']['type'])
                    {
                        case 'int':
                        case 'integer':
                        case 'mediumint':
                        case 'bigint':
                            $model->setModifyTime(time());
                            break;

                        case 'datetime':
                            $model->setModifyTime(date('Y-m-d H:i:s'),time());
                            break;
                    }
                }
            }

        }
        return $model;
    }


    /** [updateModel description] */
    public static function updateModel($model = null, $params = null)
    {
        $handlerlName  = static::getHandlerName();
        $arrAttributes = $handlerlName::loadModelAttributes();
        $haoResult     = static::cehckUpdateModel($model, $arrAttributes);
        if(!$haoResult->isResultsOK())  return $haoResult;

        $editType = empty($model->getId()) ?  'add': 'update' ;
        $model    = static::updateModelAttributes($model, $params, $arrAttributes, $editType);

        $model = $handlerlName::saveModel($model);
        if(is_null($model)) return HaoResult::initError('添加数据有误');
        $haoResult = HaoResult::init(ERROR_CODE::$OK,$model);
        return $haoResult;
    }

    /**
     * [cehckUpdateModel description]
     * @param  [type] $model           [description]
     * @param  array  $modelAttributes [description]
     * @return [type]                  [description]
     */
    public static function cehckUpdateModel($model = null, $modelAttributes = [])
    {
        $haoResult = HaoResult::init(ERROR_CODE::$OK);
        foreach ($modelAttributes as $attribute => $attributes)
        {
            $method = "get{$attribute}";
            if(method_exists($model, $method))
            {
                $modelValue = call_user_func(array($model, $method), null);
                // 字符串检查长度
                if(in_array($attributes['type'], ['char','varchar']))
                {
                    $modelValueLength = W2String::mbStrlen($modelValue);
                    if( $modelValueLength > $attributes['length'] )
                    {
                        $haoResult = HaoResult::initError("{$attribute} {$attributes['comment']} 最大长度{$attributes['length']}个字符");
                        break;
                    }
                }
            }
        }
        return $haoResult;
    }

    /**
     * [handlerlModel description]
     * @param  [type] $model [description]
     * @return [type]        [description]
     */
    public static function handlerlModel($model = null, $params = null)
    {
        return $model;
    }
}