<?php
namespace app\api\validate;

use app\api\validate\BaseValidate;

class TokenGet extends BaseValidate {
    protected $rule = [
        'code' => 'require|notEmpty'
    ];
    protected $message = [
        'code' => 'code不能为空'
    ];

}
