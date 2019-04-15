<?php
namespace app\lib\exception;

use app\lib\exception\BaseException;
use think\Exception;

class SuccessMessage extends BaseException {
    //HTTP 状态码
    public $code = 201;
    //错误具体信息
    public $message = '操作成功';
    //自定义错误码
    public $error_code = 0;

    // public function __construct()
    // {
    //     $param_num = func_num_args();
    //     if(0 == $param_num) return;
    //     $params = func_get_args();
    //     if(1 != count($params)) throw new Exception('message参数错误');
    //     if(count($params) == count($params, 1))
    //          call_user_func_array(array($this, 'construct1'), $params);
    //     else call_user_func_array(array($this, 'construct2'), $params);
    // }
    //
    // public function construct1($message)
    // {
    //     $this->message = $message;
    // }
    // public function construct2($params = [])
    // {
    //     if(!is_array($params)) throw new Exception('message参数错误');
    //     if(array_key_exists('code')) $this->code = $params['code'];
    //     if(array_key_exists('message')) $this->message = $params['message'];
    //     if(array_key_exists('error_code')) $this->error_code = $params['error_code'];
    // }
}
