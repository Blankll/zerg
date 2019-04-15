<?php
namespace app\lib\exception;

use app\lib\exception\BaseException;

class CategoryException extends BaseException {
    //HTTP 状态码
    public $code = 404;
    //错误具体信息
    public $message = '指定类目不存在，请检查参数';
    //自定义错误码
    public $error_code = 50000;
}
