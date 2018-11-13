<?php
/**
 * [ 接口测试 ApiTest Created by Tool on 2018-11-13 15:07 ]
 * @package Controller
 * @author Tool
 * @since 1.0
 * @version 1.0
 */

class ApiTestModel extends BaseModel
{
    public static $authViewDisabled    = array('createTime','modifyTime');//展示数据时，禁止列表。

    /** @var int 主键自增ID */
    public $id;

    /** @var tinyint 是否有效 1有效 0无效 */
    public $dflag;

    /** @var datetime 创建时间 */
    public $create_time;

    /** @var datetime 修改时间 */
    public $modify_time;

    /** @var varchar(25) 名字 */
    public $name;

    /** @var tinyint 年龄 */
    public $age;


	/** 主键自增ID **/
    public function getId()
    {
        return $this->id;
    }
    /** 主键自增ID **/
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

	/** 是否有效 1有效 0无效 **/
    public function getDflag()
    {
        return $this->dflag;
    }
    /** 是否有效 1有效 0无效 **/
    public function setDflag($dflag)
    {
        $this->dflag = $dflag;
        return $this;
    }

	/** 创建时间 **/
    public function getCreateTime()
    {
        return $this->create_time;
    }
    /** 创建时间 **/
    public function setCreateTime($create_time)
    {
        $this->create_time = $create_time;
        return $this;
    }

	/** 修改时间 **/
    public function getModifyTime()
    {
        return $this->modify_time;
    }
    /** 修改时间 **/
    public function setModifyTime($modify_time)
    {
        $this->modify_time = $modify_time;
        return $this;
    }

	/** 名字 **/
    public function getName()
    {
        return $this->name;
    }
    /** 名字 **/
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

	/** 年龄 **/
    public function getAge()
    {
        return $this->age;
    }
    /** 年龄 **/
    public function setAge($age)
    {
        $this->age = $age;
        return $this;
    }

}