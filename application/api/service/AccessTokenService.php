<?php
namespace app\api\service;

use think\Env;

class AccessTokenService {
    private $tokenUrl;
    //access_token key 存入缓存
    const TOKEN_CACHED_KEY = 'ACCESS';
    //过期时间
    const TOKEN_EXPIRE_IN = 7000;

    //设置请求参数
    function __construct()
    {
        $url = Env::get('WX.ACCESS_TOKEN_URL');
        $app_id = Env::get('WX.APP_ID');
        $app_secret =Env::get('APP_SECRET');
        $url = sprintf($url, $app_id, $app_secret);
        $this->tokenUrl = $url;
    }
    /**
     *
     * 获取access_token
     * 小规模时可以直接去服务器请求最新token
     * 接口限制一天可以请求2000次
     *
     * @return string $token
     */
     public function get()
     {
         $token = $this->getFromCache();
         if(!$token) $token = $this->getFromWxServer();

         return $token;
     }

     /**
      *
      * 从缓存获取accesstoken
      *
      *
      *
      */
      private function getFromCache()
      {
          $token = cache(self::TOKEN_CACHED_KEY);
          if(!$token) return $token;

          return $token;
      }
      /**
       *
       * 从服务器获取accesstoken,并将accesstoken信息写入缓存中
       *
       *
       */
       private function getFromWxServer()
       {
           $token = curl_get($this->tokenUrl);
           $token = json_decode($token, true);
           if(!$token) throw new Exception('获取acces_token异常');
           if(!empty($token['errmsg'])) throw new Exception($token['errmsg']);
           $this->saveToCache($token);

           return $token['access_token'];
       }
       /**
        *
        * 数据存入缓存
        */
        private function saveToCache($token)
        {
            cache(self::TOKEN_CACHED_KEY, $token, self::TOKEN_EXPIRE_IN);
        }

}
