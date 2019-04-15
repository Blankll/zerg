<?php
namespace app\lib\exception;

use app\lib\exception\BaseException;

class UserException extends BaseException {
    //HTTP 状态码
    public $code = 404;
    //错误具体信息
    public $message = '用户不存在';
    //自定义错误码
    public $error_code = 60000;
}
