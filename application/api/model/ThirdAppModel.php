<?php
namespace app\api\model;

use app\api\model\BaseModel;

class ThirdAppModel extends BaseModel {
    //指定表名
    protected $table ='third_app';
    /**
     *
     * 验证帐号密码
     * @param string $account 帐号
     * @param string $password 密码
     * @return object $app 用户信息
     */
     public static function check($account, $password)
     {
         $app = self::where('app_id', '=', $account)
         ->where('app_secret', '=', $password)->find();

         return $app;
     }
}
