<?php
namespace app\api\validate;

use app\api\validate\BaseValidate;

class Count extends BaseValidate {
    protected $rule = [
        'count' =>  'isInt|between:1,16'
    ];
    protected $message = [
        'count.isInt' => 'count必修是正整数',
        'count.between' => 'count 参数必须在1-16之间'
    ];
}
