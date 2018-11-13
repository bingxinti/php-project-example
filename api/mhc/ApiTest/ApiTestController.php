<?php
/**
 * [ 接口测试 ApiTest Created by Tool on 2018-11-13 15:07 ]
 * @package Controller
 * @author Tool
 * @since 1.0
 * @version 1.0
 */

class ApiTestController extends BaseController
{
    /** [actionList 列表数据] */
    public static function actionList($params = null)
    {
        is_null($params) && $params = W2Params::instanceGet();
        $haoResult = parent::actionList($params);

        return $haoResult;
    }

    /** [actionDetail 详情信息] */
    public static function actionDetail($params = null)
    {
        is_null($params) && $params = W2Params::instanceGet();
        $haoResult = parent::actionDetail($params);

        return $haoResult;
    }

    /** [actionUpdate 修改] */
    public static function actionUpdate($params = null)
    {
        is_null($params) && $params = W2Params::instancePost();

        // 不能为空参数
        $required =
        [
            'name'        => '名字',
            'age'         => '年龄',
        ];
        $haoResult = $params->checkRequired($required);
        if(!$haoResult->isResultsOK()) return  $haoResult;

        // 允许直接修改的参数
        $attributeKeys =
        [
            'name',        // 名字
            'age',         // 年龄
        ];
        $model = ApiTestHandler::loadModelById($params->getInt('id'));
        if(!is_object($model)) return HaoResult::init(ERROR_CODE::$NO_MODEL_FOUND);
        $model->setAttributes($attributeKeys, $params->getParams());

        $haoResult = static::updateModel($model, $params);

        return $haoResult;
    }

    /** [actionAdd 添加] */
    public static function actionAdd($params = null)
    {
        is_null($params) && $params = W2Params::instancePost();

        // 不能为空参数
        $required =
        [
            'name'        => '名字',
            'age'         => '年龄',
        ];
        $haoResult = $params->checkRequired($required);
        if(!$haoResult->isResultsOK()) return  $haoResult;

        // 允许直接修改的参数
        $attributeKeys =
        [
            'name',        // 名字
            'age',         // 年龄
        ];
        $model = ApiTestModel::instance();
        $model->setAttributes($attributeKeys, $params->getParams());

        $haoResult = static::updateModel($model, $params);

        return $haoResult;
    }

    /** [queryParams 公共查询Where] */
    /*public static function queryParams($params = null, $attribute = [])
    {
        $pWhere = parent::queryParams($params, $attribute);

        return $pWhere;
    }*/

    /** [updateModel 公共修改模型] */
    /*public static function updateModel($model = null, $params = null)
    {
        $haoResult = parent::updateModel($model, $params);

        return $haoResult;
    }*/
}