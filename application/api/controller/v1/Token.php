<?php
namespace app\api\controller\v1;

use app\api\validate\{TokenGet, AppTokenGetValidate};
use app\api\service\UserToken;
use app\lib\exception\ParameterException;
use app\api\service\AppTokenService;

class Token {
    /**
     * 获取token
     *
     * @param string $code
     * @return array token
     */
    public function getToken($code = '')
    {
        //验证参数
        (new TokenGet())->checkRequestId();
        //调用service获取token
        $ut = new UserToken($code);
        $token = $ut->get();

        return ['token' => $token];
    }
    /**
     * 验证客户端传递的token
     *
     * @param string $token 客户端传来的token
     * @
     */
     public function verifyToken($token = '')
     {
         if(!$token) throw new ParameterException('token不允许为空');
         $valid = UserToken::verifyToken($token);

         return ['isValid' => $valid];
     }

     /**
      *
      * 第三方应用获取令牌
      *
      * @param string $account 帐号
      * @param string $password 密码
      * @return array token
      */
      public function getAppToken($account='', $password='')
      {
          //验证请求参数
          (new AppTokenGetValidate())->checkRequestId();
          //获取token
          $app = new AppTokenService();
          $token = $app->get($account, $password);

          return ['token' => $token];
      }
}
