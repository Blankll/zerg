<?php
namespace app\api\validate;

use app\api\validate\BaseValidate;

class AddressValidate extends BaseValidate {
    protected $rule = [
        'name' => 'require|notEmpty',
        'mobile' => 'require|notEmpty',
        'detail' => 'require|notEmpty',
        'city' => 'require|notEmpty',
        'province' => 'require|notEmpty',
        'country' => 'require|notEmpty'
    ];
}
