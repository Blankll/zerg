<?php
namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\validate\IDMustBePostiveInt;
use app\api\service\{PayService, WxNotifyService};

class Pay extends BaseController {
    //验证调用权限
    protected $beforeActionList =[
        'checkExclusiveScope' => ['only' => 'getPreOrder']
    ];
    /**
     * 进行预支付
     *
     * 微信服务器端生成订单并进行预支付，从微信服务器获取prepay_id,
     * 微信客户端可使用该prepay_id拉起微信支付
     *
     * @param int $id 订单的id
     * @param object $result 返回微信接口返回来的prepay_id
     */
    public function getPreOrder($id = '')
    {
        //验证参数
        (new IDMustBePostiveInt())->checkRequestId();
        //预支付
        $pay = new PayService($id);
        $result = $pay->pay();

        return $result;
    }
    /**
     *
     * 微信异步回调接口
     *
     * 1. 检测库存
     * 2. 更新订单状态order.status
     * 3. 更新库存
     * 4, 若成功处理，向微信服务器发送成功处理消息,否则发送没有成功处理信息
     * 5, 微信以post携带xml参数请求接口
     */
     public function receiveNotify()
     {
         $notify = new WxNotifyService();
         $notify->Handle();
     }
}
