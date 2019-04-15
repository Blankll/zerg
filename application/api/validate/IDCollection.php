<?php
 namespace app\api\validate;

 use app\api\validate\BaseValidate;

 class IDCollection extends BaseValidate{
     protected $rule = [
         'ids' => 'require|isIntArrya'
     ];
     protected $message = [
         'ids' => 'ids参数必须是以逗号分隔的多个正整数'
     ];
     //检测数组中传递的是否为整整数
     protected function isIntArrya($value)
     {
         $values = explode(',', $value);
         if(empty($values)) return false;
         foreach ($values as $key => $value)
         {
             if(!$this->isInt($value)) return false;
         }
         return true;
     }
 }
