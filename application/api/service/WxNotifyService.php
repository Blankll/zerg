<?php
namespace app\api\service;

use think\{Loader, Db};
Loader::import('WxPay.WxPay', EXTEND_PATH, '.Api.php');

class WxNotifyService extends \WxPayNotify {

    /**
     *
     * 重写 WxPayNotify 中的 NotifyProcess
     *
     * 1. 检测库存
     * 2. 更新订单状态order.status
     * 3. 更新库存
     * 4, 若成功处理，向微信服务器发送成功处理消息,否则发送没有成功处理信息
     * 5, 微信以post携带xml参数请求接口
     * @param array $data 微信发送给接口的数据
     * @param       &$msg
     * @return bool 返回给微信服务器的状态
     */
    public function NotifyProcess($data, &$msg)
    {
        //支付失败,表示服务器已经知道支付失败消息，不需要微信再次调用函数回传支付信息
        if($data['result_code'] != 'SUCCESS') return true;
        //获取订单号
        $order_no = $data['out_trade_no'];
        //更新数据库
        try
        {
            //启用事务，防止多次操作库存
            Db::startTrans();
            $order = OrderModel::whrere('order_no', '=', $order_no)->find();
            //处于未支付
            if($order->status == OrderStatusEnum::PAID)
            {
                //检测库存
                $service = new OrderService();
                $stock_status = $service->checkOrderStock($order->id);
                //库存通过
                if($stock_status['pass'])
                {
                    //更新订单支付状态
                    $this->updateOrderStatus($order_id, true);
                    //减少库存
                    $this->reduceStock($stock_status);
                }
                else
                {
                    //支付成功，库存不足
                    $this->updateOrderStatus($order->id, false);
                }
            }
            Db::commit();
        }
        catch(Exception $e)
        {
            //事务异常，回滚数据
            Db::rollback();
            //数据库操作失败
            Log::error($e);

            return false;
        }

        return true;
    }

    /**
     *
     * 更新订单支付状态
     *
     * @param int $order_id 支付的订单的id
     * @param bool $order_status 库存状态是否允许下单
     *
     */
    private function updateOrderStatus(int $order_id, bool $order_status)
    {
        //确定库存信息
        $state = $order_status ? OrderStatusEnum::PAID : OrderStatusEnum::PAID_UNDERSTOCK;
        //更新数据库
        OrderModel::where('id', '=', $order_id)->update(['status' => $state]);
    }

    /**
     *
     * 减少商品库存
     *
     * @param array $stock_status 订单库存信息
     */
     private function reduceStock($stock_status)
     {
         foreach($stock_status as $item)
         {
             ProductModel::where('id', '=', $item['id'])->setDec('stock', $item['count']);
         }
     }









}
