<?php
namespace app\lib\exception;

use think\Exception;

class BaseException extends Exception {
    //HTTP 状态码
    public $code = 400;
    //错误具体信息
    public $message = '参数错误';
    //自定义错误码
    public $error_code = 10000;

    /**
     * @see func_num_args()     获得传入的所有参数的个数
     * @see func_get_args()     获得传入的所有参数的数组
     * @see func_get_arg($key)  获取单个参数的值
     * @see array_key_exists($key,$arr) 判断数组中是否有索引为$key的字段
     */
    public function construct1(string $message = '参数错误', int $error_code = 10000, int $code = 400)
    {
        $param_num = func_num_args();
        if(0 == $param_num) return;
        $data = func_get_args();
        if(1 == $param_num) $this->message = $data[0];
        if(2 == $param_num) $this->error_code = $data[1];
        if(3 == $param_num) $this->code = $data[2];
    }
    public function construct2($param = [])
    {
        if(!is_array($param)) return;
        if(array_key_exists('message',$param)) $this->message = $param['message'];
        if(array_key_exists('error_code',$param)) $this->error_code = $param['error_code'];
        if(array_key_exists('code',$param)) $this->code = $param['code'];
    }

/***************************************************************
| count($array, $kind) :统计数组的元素个数
+ $kind COUNT_RECURSIVE 递归检测数组元素
|       CONT_NOMAL    只检测一维的
+ $numb=array(
|            array(10,15,30),array(10,15,30),array(10,15,30)
+ );
| echo count($numb,1);  //= 12
+
| call_user_func(),call_user_func_array() 主要区别，参数格式。
+ call_user_func(array($foo, 'test'), [$param, $param1, $param2])这种格式会把后面数组作为一个参数传递过去
| call_user_func_array(array($foo, 'test'), [$param, $param1, $param2]) 这种格式会作为三个参数传递给回调函数，如果存在key ,忽略。
+
******************************************************************/
/**
 * 可以传入数组或者一个或多个基本类型参数的构造函数
 *
 *如果传入的参数是数组，则需要是关联数组，
 *如果传入一个或多个基本类型参数，第一个参数为string的message信息，第二个参数为int的error_code，第三个参数为int的code
 */
    public function __construct()
    {
        $param_num = func_num_args();
        if(0 == $param_num) return;
        $params = func_get_args();
        if(count($params) == count($params,1))
             call_user_func_array(array($this,'construct1'),$params);
        else call_user_func_array(array($this,'construct2'),$params);
    }

    /****************************************************************
    +  方法2：都写在一个构造函数中
    |****************************************************************
    public function __construct()
    {
        $param_num = func_num_args();
        if(0 == $param_num) return;
        $params = func_get_args();
        if(count($params) == count($params,1))
        {
            $array_count = count($params);
            if(1 == $array_count) $this->message = $params[0];
            if(2 == $array_count) $this->error_code = $params[1];
            if(3 == $array_count) $this->code = $params[2];
        }
        else
        {
            $param = $params[0];
            if(array_key_exists('message',$param)) $this->message = $param['message'];
            if(array_key_exists('error_code',$param)) $this->error_code = $param['error_code'];
            if(array_key_exists('code',$param)) $this->code = $param['code'];
        }
    }
    **********************************************************************/
}
