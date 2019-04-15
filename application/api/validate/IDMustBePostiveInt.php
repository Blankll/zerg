<?php
namespace app\api\validate;

use app\api\validate\BaseValidate;

class IDMustBePostiveInt extends BaseValidate {
    protected $rule = [
        'id' => 'require|isInt'
    ];
    protected $message = [
        'id.require' => 'id不能为空',
        'id.isInt' => 'id必须是正整数'
    ];
}
