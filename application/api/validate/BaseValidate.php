<?php
namespace app\api\validate;

use think\Validate;
use think\Request;
use app\lib\exception\ParameterException;

class BaseValidate extends Validate {
    public function checkRequestId()
    {
        //获取传入的http参数
        $request = Request::instance();
        $params = $request->param();
        //对传入的参数进行检验
        if($this->check($params)) return true;
        throw new ParameterException([
            'message' => $this->error,
            'error_code' => 666,
            'code' => 888
        ]);
    }
    /**
     * 验证参数是否为正整数
     *
     * @param $value: 传入的参数
     * @param $rule: 传入规则
     * @param $data: 全部数据（开发者文档上的描述）
     * @param $field: 字段描述
     * @is_numeric: 判断变量是否为数字
     * @is_int: 判断变量是否为整数
     *
     */
     protected function isInt($value, $rule = '', $data = '', $field = '')
     {
         if(is_numeric($value) && is_int($value + 0) && ($value + 0) > 0) return true;
         else //return $field.'参数必须是正整数';
                return false;
     }

     /**
      * 验证参数是否不为空
      *
      * @param $value: 传入的参数
      * @param $rule: 传入规则
      * @param $data: 全部数据（开发者文档上的描述）
      * @param $field: 字段描述
      * @return boolean
      */
      protected function notEmpty($value, $rule = '', $data = '', $field = '')
      {
          if(empty($value)) return false;
          else              return true;
      }

      /**
       * 验证手机号码格式是否正确
       *
       * @param $phone_number: 传入的手机号码
       * @return boolean
       */
       protected function isMobile($phone_number)
       {
           //正则规则
           $rule = '^1(3|4|5|7|8)[0-9]\d{8}$^';
           //验证参数
           $result = false;
           if(preg_match($rule, $phone_number)) $result =  true;

           return $result;
       }

      /**
       * 获取经过验证的所需要的安全参数
       *
       * @param array $params request的所有参数
       * @return array $result 需要的安全参数
       */
      public function getDataByRule($params)
      {
          //检验参数是否合法
          if(array_key_exists('user_id', $params) | array_key_exists('uid', $params))
          throw new ParameterException('参数中包含有非法的参数名user_id或uid');

          //获取需要的参数
          $result = [];
          foreach($this->rule as $key => $value) $result[$key] = $params[$key];

          return $result;
      }
}
