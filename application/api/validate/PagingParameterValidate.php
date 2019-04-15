<?php
namespace app\api\validate;

use app\api\validate\BaseValidate;

class PagingParameterValidate extends BaseValidate {
    //验证规则
    protected $rule = [
        'page' => 'require|isInt',
        'size' => 'require|isInt'
    ];
    protected $message = [
        'page.isInt' => '分页参数必须是正整数',
        'size.isInt' => '分页参数必须时正整数',
        'page.require' => '分页参数不能为空',
        'size.require' => '分页参数不能为空'
    ];
}
