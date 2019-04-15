<?php

namespace app\api\model;

use app\api\model\BaseModel;

class UserModel extends BaseModel
{
    //定义数据表名
    protected $table = "user";
    //关联模型
    public function address()
    {
        /***********************************************************************
        | 在模型中关联一对一关系时，拥有外键字段表对应的模型内定义模型关联用belongsTo()
        + 在没有外键字段[被关联的模型对应表内有与关联模型表对应的表相关联的外键字段]的表对应的
        | 模型内定义关联用hasOne()
        ************************************************************************/
        return $this->hasOne('UserAddressModel', 'user_id', 'id');
    }
    /**
     * 获取openid对应的用户信息
     *
     * @param string $openid 用户微信openid
     * @return mixed $user 查询到的用户信息
     */
    public static function getByOpenID($openid)
    {
        $user = self::where('openid', '=', $openid)->find();

        return $user;
    }
}
