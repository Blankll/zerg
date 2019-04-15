<?php
namespace app\api\service;

use app\lib\exception\WeChatException;
use app\api\model\UserModel;
use app\api\service\BaseToken;
use app\lib\enum\ScopeEnum;
use \think\Env;

/**
 *
 *
 *
 */
class UserToken extends BaseToken {
    protected $code;
    protected $wx_app_id;
    protected $wx_app_secret;
    protected $wx_login_url;

    //构造函数，对成员属性进行赋值
    function __construct($code)
    {
        $this->code = $code;
        $this->wx_app_id = Env::get('WX.APP_ID');
        $this->wx_app_secret = Env::get('WX.APP_SECRET');

        $this->wx_login_url = sprintf(Env::get('WX.LOGIN_URL'),
        $this->wx_app_id,
        $this->wx_app_secret,
        $this->code);
    }

    /**
     * 获取用户token
     *
     * 向微信服务器发送http请求，获取openid和session_key\
     *
     * @see sprintf
     * @return string $token 令牌
     */
    public function get()
    {
        //向微信服务器发起请求
        $result = curl_get($this->wx_login_url);
        //将返回的字符串封装成数组形式的json数据
        $wx_result = json_decode($result, true);
        //返回结果为空， 应该归类于系统内部错误，不传递给客户端
        if(empty($wx_result)) throw new \think\Exception('获取session_key及openid时异常，微信内部错误');
        //调用服务器成功，但有错误发生
        if(array_key_exists('errcode',$wx_result)) $this->processLoginError($wx_result);
        //获取令牌
        $token = $this->grantToken($wx_result);

        return $token;
    }


    /**
     * 调用微信api时返回错误码抛出异常
     *
     * @param object $wx_result 微信返回的信息
     * @return null
     */
    private function processLoginError($wx_result)
    {
        throw new WeChatException([
            'message' => $wx_result['errmsg'],
            'error_code' => $wx_result['errcode']
        ]);
    }

    /**
     * 授权接口，生成令牌
     *
     * 获取openid;
     * 检测openid是否存在于数据库中,如果不存在，新增记录;
     * 生成令牌，准备缓存数据;
     * 将缓存数据写入缓存;
     * 将令牌返回给客户端;
     *
     * @param object $wx_result 调用微信服务器返回的数据
     * @return string $token 生成的令牌
     */
    private function grantToken($wx_result)
    {
        //key:令牌
        //value:wx_result, u_id, scope-决定用户身份，用于完善用户权限

        //获取openid
        $openid = $wx_result['openid'];
        //检测openid是否存在于数据库中，如果不存在，新增记录
        $user = UserModel::getByOpenID($openid);
        if($user) $u_id = $user->id;
        else      $u_id = $this->newUser($openid);
        //准备缓存数据，
        $cached_value = $this->prepareCachedValue($wx_result,$u_id);
        //将缓存数据写入缓存同时生成令牌
        $token = $this->saveToCache($cached_value);

        return $token;
    }

    /**
     * 将获取的openid插入数据库中
     *
     * @param string $openid 微信openid
     * @return string $user->id 用户id
     */
    private function newUser($openid)
    {
        $user = UserModel::create(['openid' => $openid]);
        return $user->id;
    }

    /**
     * 准备缓存数据
     *
     * @param object $wx_result 从微信服务器端获取的数据
     * @param string $u_id 用户在数据库中的id
     * @return object 准备好的缓存值
     */
    private function prepareCachedValue($wx_result, $u_id)
    {
        $cachedValue = $wx_result;
        $cachedValue['u_id'] = $u_id;
        //scope = 16 代表App用户的权限数值
        $cachedValue['scope'] = ScopeEnum::USER;

        return $cachedValue;
    }

    /**
     * 将token键值对写入缓存
     *
     * @param object $catched_value 生成的缓存value
     * @return string $key 缓存的key
     */
    private function saveToCache($cached_value)
    {
        //获取缓存的key
        $key = self::generateToken();
        //格式化缓存的value
        $value = json_encode($cached_value);
        //获取缓存有效期
        $expire_in = Env::get('TOKEN.EXPIRE_IN');
        //写入缓存
        $request = cache($key, $value, $expire_in);
        //写入失败时抛出异常
        if(!$request) throw new TokenException([
            'message' => '服务器缓存异常',
            'error_code' => 10005
        ]);

        return $key;
    }
















}
