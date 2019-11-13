<?php
namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\validate\{
    OrderPlaceValidate,
    PagingParameterValidate,
    IDMustBePostiveInt};
use app\api\service\{BaseToken, OrderService};
use app\lib\exception\{
    SuccessMessage,
    OrderException
};
use app\api\model\{OrderModel};

/**
 * 完成下单支付操作
 *
 * 1,用户在选择商品后，向API提交包含订单信息的request
 * 2,API接收到信息后，需要检查订单相关商品的库存
 * 3,若库存满足，将订单写入数据库中，下单成功，返回给客户端提示支付
 * 4,调用支付接口，进行支付
 * 5,再次进行库存检测
 * 6,服务器端调用微信支付接口进行支付
 * 7,小程序根据服务器端返回的结果拉起微信支付
 * 8,微信返回支付结果[服务器端异步]
 * 9,成功，再次进行库存检查
 * 10,满足条件进行库存扣除
 */
class Order extends BaseController {
    //前置， 检测权限
    protected $beforeActionList = [
        'checkExclusiveScope' => ['only' => 'placeOrder'],
        'checkPrimayScope' => ['only' => 'getDetail, getSummaryByUser']
    ];

    public function placeOrder()
    {
        //验证参数
        (new OrderPlaceValidate())->checkRequestId();
        //获取post的数据[products字段的所有数据]
        $products = input('post.products/a');
        //获取用户id
        $u_id = BaseToken::getCurrentUid();
        //创建订单
        $order = new OrderService();
        $result = $order->place($u_id, $products);

        return $result;
    }

    /**
     *
     * 验证历史订单数据
     *
     * @param int $page 当前页码
     * @param int $size 每页的数据量
     * @return array $result 分页数据
     */
    public function getSummaryByUser($page = 1, $size)
    {
        //验证参数
        (new PagingParameterValidate())->checkRequestId();
        //获取当前用户id
        $u_id = BaseToken::getCurrentUid();
        //获取分页
        $paging = OrderModel::getSummaryByUser($u_id, $page, $size);
        $result = array('data' => [], 'current_page' => $paging->getCurentPage());
        //处理分页数据
        if(!$paging->isEmpty()) $result['data'] = $paging->hidden([
            'sanp_items', 'snap_address', 'prepay_id'
            ])->toArray();

        return $result;
    }

    /**
     *
     * 获取订单详情
     *
     * @param int $id 订单id
     * @return
     */
     public function getDetail($id)
     {
         //验证参数
         (new IDMustBePostiveInt())->checkRequestId();
         //获取订单信息
         $order = OrderModel::get($id);
         //订单不存在
         if(!$order) throw new OrderException();
         //临时隐藏数据
         $order = collection($order)->hidden(['prepay_id'])->toArray();

         return $order;
     }
     /**
      *
      * 获取订单全部信息[分页]
      *
      * @param int $page 当前页码
      * @param int $size 每页的数据量
      * @return array $result 分页数据
      */
      public function getSummary($page = 1, $size = 20)
      {
          //验证参数
          (new PagingParameterValidate())->checkRequestId();
          //获取分页
          $paging_orders = OrderModel::getSummaryByPage($page, $size);
          $result = ['current_page' =>  $page, 'data' => []];
          if(! $paging_orders) $result['data'] = $paging_orders
          ->hidden(['snap_items', 'snap_address'])->toArray();

          return $result;
      }
      /**
       *
       * 发送模板消息
       * @param int $id 操作的订单号
       * @return
       */
       public function delivery($id)
       {
           (new IDMustBePostiveInt())->checkRequestId();
           $order = new OrderService();
           $result = $order->delivery($id);
           if($result) return new SuccessMessage();
       }
}
