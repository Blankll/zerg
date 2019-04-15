<?php
namespace app\lib\exception;

use app\lib\exception\BaseException;

class OrderException extends BaseException {
    //HTTP 状态码
    public $code = 404;
    //错误具体信息
    public $message = '订单不存在，请检查ID';
    //自定义错误码
    public $error_code = 80000;

}
