<?php
namespace app\api\validate;

use app\api\validate\BaseValidate;

class AppTokenGetValidate extends BaseValidate {
    protected $rule = [
        'account' => 'require|notEmpty',
        'password' => 'require|notEmpty'
    ];
    protected $message = [
        'account' => '帐号不能为空',
        'password' => '密码不能为空'
    ];
}
