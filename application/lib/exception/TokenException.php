<?php
namespace app\lib\exception;

use app\lib\exception\BaseException;

class TokenException extends BaseException {
    //HTTP 状态码
    public $code = 401;
    //错误具体信息
    public $message = 'Token已过期或无效';
    //自定义错误码
    public $error_code = 10001;

}
