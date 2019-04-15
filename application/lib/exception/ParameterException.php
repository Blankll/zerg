<?php

namespace app\lib\exception;

use app\lib\exception\BaseException;


class ParameterException extends BaseException{
    //HTTP状态码
    public $code = 400;
    //错误信息
    public $message = '参数错误';
    //自定义错误码
    public $error_code = 10000;
    
}
