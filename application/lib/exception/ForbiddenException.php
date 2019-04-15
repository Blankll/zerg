<?php
namespace app\lib\exception;

use app\lib\exception\BaseException;

class ForbiddenException extends BaseException {
    //HTTP 状态码
    public $code = 403;
    //错误具体信息
    public $message = '访问权限不足';
    //自定义错误码
    public $error_code = 10001;

}
