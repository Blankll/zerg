<?php
namespace app\api\service;

use app\api\service\BaseToken;
use app\api\model\{ThirdAppModel};
use app\lib\exception\TokenException;

class AppTokenService extends BaseToken {

    /**
     *
     * 第三方平台获取token
     *
     * @param string $account 帐号
     * @param string $password 密码
     * @return string $token
     */
    public function get($account, $password)
    {
        //从数据库中获取并验证帐号信息
        $app = ThirdAppModel::check($account, $password);
        //验证失败抛出异常
        if(!$app) throw new TokenException([
            'message' => '授权失败',
            'error_code' => 10004,
            'code' => 401
        ]);
        //获取用户信息
        $value = [
            'scope' => $app->scope,
            'u_id' => $app->id
        ];
        //存入缓存中
        $token = $this->saveToCache($value);

        return $token;
    }
    /**
     * 将用户信息存入缓存中
     *
     * @param array $value 用户信息
     * @return string $token token
     */
     private function saveToCache($value)
     {
         //token key
         $token = self::generateToken();
         //out time
         $expire_in = \think\Env::get('TOKEN.EXPIRE_IN');
         //save to cache
         $result = cache($token, json_encode($value), $expire_in);
         //fail to save
         if(!$result) throw new TokenException([
             'message' => '服务器缓存异常',
             'error_code' => 10005
         ]);

         return $token;
     }
}
