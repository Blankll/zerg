<?php
namespace app\lib\exception;

use app\lib\exception\BaseException;

class ThemeException extends BaseException {
    //HTTP 状态码
    public $code = 400;
    //错误具体信息
    public $message = '请求的主题不存在';
    //自定义错误码
    public $error_code = 30000;
}
