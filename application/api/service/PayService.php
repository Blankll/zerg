<?php
namespace app\api\service;


use think\{Exception, Loader, Log};
use app\api\service\{BaseToken, OrderService};
use app\lib\exception\{OrderException, TokenException};
use app\lib\enum\OrderStatusEnum;
use app\api\model\OrderModel;
//加载微信sdk
Loader::import('WxPay.WxPay', EXTEND_PATH, 'Api.php');

class PayService {
    //订单id
    private $order_id;
    //订单号
    private $order_no;

    public function __construct($order_id)
    {
        if(!$order_id) throw new Exception('订单号不允许为NULL');
        $this->order_id = $order_id;
    }
    /**
     *
     * 拉起微信支付
     *
     * @return array $result 返回给客户端的签名
     *
     *
     *
     */
    public function pay()
    {
        //检测订单号是否存在
        $order_exists = $this->checkOrderExists($this->order_id);
        //下订单用户是否和当前操作用户匹配[下单的和付款的是不是同一个人]
        $user_match = $this->checkUserMatch($order_u_id);
        //检测当前订单是否支付已
        $payment_state = checkPaymentStat($this->order_id);
        //进行库存量检测
        $status = OrderService::checkOrderStock($this->order_id);
        //库存条件不满足
        if(!$status['pass']) return $status;
        //生成预订单
        $pre_order = $this->makeWXPreOrder($this->order_no, $status['order_price']);
        //请求微信服务器
        $result = $this->getPaySinature($pre_order);

        return $result;
    }
    /**
     * 检测订单是否存在
     *
     * @param int $order_id 客户端传递的订单id
     * @return boolean
     */
    private function checkOrderExists($order_iwd)
    {
        //从数据库查询订单
        $order = OrderModel::find($order_id);
        //订单不存在抛出异常
        if(!$order) throw new OrderException();

        return true;
    }
    /**
     * 检测下单用户和支付用户是否匹配
     *
     * @param int $order_u_id 生成订单用户
     * @return boolean
     */
     private function checkUserMatch($order_u_id)
     {
         //检测下单用户和支付用户是否匹配
         $result = BaseToken::checkOrderUser($order_u_id);
         //不匹配时抛出异常
         if(!$result) throw new TokenException([
             'message' => '订单与用户不匹配',
             'error_code' => 10003
         ]);

         return true;
     }
     /**
      * 检测订单的支付状态
      *
      * @param int $order_id 订单id
      * @param int $order->status 支付状态
      */
      private function checkPaymentStat($order_id)
      {
          //获取订单数据
          $order = OrderModel::find($order_id);
          //订单不存在抛出异常
          if(!$order) throw new OrderException();
          //检测订单支付状态
          if($order['status'] != OrderStatusEnum::UNPAID) throw new OrderException([
              'message' => '订单已支付',
              'error_code' => 80003,
              'code' => 4000
          ]);
          $this->$order_no = $order->order_no;

          return true;
      }
      /**
       * 生成预订单
       *
       * @param string $order_no 订单号
       * @param double $total_price 订单总价
       * @param object $wx_order_data 生成的预订单
       */
      private function makeWXPreOrder($order_no, $total_price)
      {

          //获取当前用户openid
          $openid = BaseToken::getCurrentTokenValue('openid');
          if(!$openid) throw new TokenException();
          //调用微信sdk
          $wx_order_data = new \WxPayUnifiedOrder();
          //订单号
          $wx_order_data->SetOut_trade_no($order_no);
          //交易类型
          $wx_order_data->SetTrade_type('JSAPI');
          //订单总金额[单位为分]
          $wx_order_data->SetTotal_fee($total_price * 100);
          //商品简要描述
          $wx_order_data->SetBody('零食商贩');
          //openid
          $wx_order_data->SetOpenid($openid);
          //设置回调接收接口
          $wx_order_data->SetNotify_url('');

          return $wx_order_data;
      }
      /**
       *
       * 将预订单发送给微信服务器
       *
       * @param object $wx_order_data 生成的预订单
       * @return array $result_data 客户端拉起支付需要的参数
       */
       private function getPaySinature($wx_order_data)
       {
           //进行预支付
           $wx_order = \WxPayApi::unifiedOrder($wx_order_data);
           //预支付失败，写入日志
           if($wx_order['return_code'] != 'SUCCESS' || $wx_order['result_code'] != 'SUCCESS')
           {
               Log::record($wx_order, 'error');
               Log::record('获取预支付订单失败', 'error');
           }
           //向数据库中的订单插入prepay_id
           $this->recordPreOrder($this->order_id, $wx_order['prepay_id']);
           //生成发送给客户端的数据
           $signature = $this->sign($wx_order['prepay_id']);

           return $signature;
       }

       //保存prepay_id
       private function recordPreOrder($order_id, $prepay_id)
       {
           OrderModel::where('id', '=', $order_id)->update(['prepay_id' => $prepay_id]);
       }

       /**
        * 生成微信客户端拉起支付需要的参数
        *
        * @param int $prepay_id 预支付订单id
        *@return array $result_data 生成参数的数组
        */
       private function sign($prepay_id)
       {
           //官方sdk方法
           $js_api_pay_data = new \WxPayJsApiPay();
           //appid
           $js_api_pay_data->SetAppid(\think\Env::get('WX.APP_ID'));
           //时间戳 string
           $js_api_pay_data->SetTimeStamp((string)time());
           //随机字符串
           $js_api_pay_data->SetNonceStr(md5(time().mt_rand(0, 1000)));
           //prepay_id 格式'prepay_id='.$prepay_id
           $js_api_pay_data->SetPackage('prepay_id='.$prepay_id);
           //加密方法
           $js_api_pay_data->SetSignType('md5');

           //生成签名
           $sign = $js_api_pay_data->MakeSign();
           //获取对象的值[array]
           $result_data = $js_api_pay_data->GetValues();
           //添加签名字段
           $result_data['paySign'] = $sign;
           //删除数组中的appid
           unset($result_data['appId']);

           return $result_data;
       }
}
