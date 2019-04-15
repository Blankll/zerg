<?php
namespace app\api\service;

use think\{Request, Cache, Exception};
use app\lib\enum\ScopeEnum;
use app\lib\exception\{TokenException, ForbiddenException};

class BaseToken {
    /**
     * 生成token的key
     *
     * @return string $result 生成的key
     */
    public static function generateToken()
    {
        //32个字符组成随机字符串
        $randChars = getRandChar(32);
        //获取系统当前时间戳
        $time_stamp = $_SERVER['REQUEST_TIME_FLOAT'];
        //salt 盐
        $salt = \think\Env::get('TOKEN.SALT');
        //用3组字符串进行md5加密
        $result = md5($randChars.$time_stamp.$salt);

        return $result;
    }

    /**
     * 获取token的value中key对应的值
     *
     * @param string $key Token值中的key
     * @return mixed 键对应的value
     */
    public static function getCurrentTokenValue($key)
    {
        //获取请求传入的token
        $token = Request::instance()->header('token');
        //从缓存中换取Token的value
        $values = Cache::get($token);
        //检验value是否为空
        if(!$values) throw new TokenException();
        //将vaule变为数组格式
        if(!is_array($values)) $values = json_decode($values, true);
        //检验目标键值对是否存在
        if(!array_key_exists($key, $values)) throw new Exception('尝试获取的Token变量不存在');

        return $values[$key];
    }

    /**
     * 获取当前请求用户的u_id
     *
     * @return int $u_id 用户id
     */
    public static function getCurrentUid() :int
    {
        $u_id = self::getCurrentTokenValue('u_id');

        return $u_id;
    }

    /**
     * 用户和管理员都可以访问的接口权限
     *
     * @return boolean
     */
    public static function needPrimaryScope()
    {
        //获取scope值
        $scope = self::getCurrentTokenValue('scope');
        //scope获取失败
        if(!$scope) throw new TokenException();
        //权限不足
        if($scope < ScopeEnum::USER) throw new ForbiddenException();

        return true;
    }
    /**
     * 只有用户才能访问的的接口权限
     *
     * @return boolean
     */
     public static function needExclusiveScope()
     {
         //获取scope值
         $scope = self::getCurrentTokenValue('scope');
         //scope获取失败
         if(!$scope) throw new TokenException();
         //非用户level权限
         if($scope != ScopeEnum::USER) throw new ForbiddenException();

         return true;
     }

     /**
      * 检测当前登录用户是否为生成订单用户
      *
      * @param int $order_u_id 生成订单用户
      * @return boolean 比对结果
      */
      public static function checkOrderUser($order_u_id)
      {
          //检测传入参数
          if(!$order_u_id) throw new Exception('检测uid时必须传入一个需要检测的订单用户id');
          //获取当前登录的用户id
          $current_u_id = self::getCurrentUid();
          //比对订单用户id与当前用户id是否相同
          if($order_u_id == $current_u_id) return true;
          else return false;
      }
      /**
       *
       *
       *
       *
       */
       public static function verifyToken($token)
       {
           $exists = Cache::get($token);
           if($exists) return true;
           else        return false;
       }
}
