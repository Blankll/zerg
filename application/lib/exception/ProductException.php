<?php
namespace app\lib\exception;

use app\lib\exception\BaseException;

class ProductException extends BaseException {
    //HTTP 状态码
    public $code = 404;
    //错误具体信息
    public $message = '指定商品不存在，请检查商品';
    //自定义错误码
    public $error_code = 20000;
}
