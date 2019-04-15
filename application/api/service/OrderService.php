<?php
namespace app\api\service;

use app\api\model\{ProductModel, OrderModel, OrderProductModel, UserAddressModel};
use app\lib\exception\{OrderException, UserException};
use think\{Exception, Db};

class OrderService {
    //从数据库中查询的到的数据
    protected $product_db;
    //从客户端得到的订单数据
    protected $product_client;
    //用户id
    protected $u_id;

    //下单
    public function place($u_id, $product_client)
    {
        //用户id
        $this->u_id = $u_id;
        //来自客户端的订单数据
        $this->product_client = $product_client;
        //从数据库中比对订单数据
        $this->product_db = $this->getProductsByOrder($product_client);
        //获取订单详情和状况
        $status = $this->getOrderStatus($this->product_client, $this->product_db);
        //订单库存未通过
        if(!$status['pass'])
        {
            $status['order_id'] = -1;
            return $status;
        }
        //创建订单快照
        $order_snap = $this->snapOrder($status);
        //生成订单
        $order = $this->createOrder($order_snap);
        //订单状态
        $order['pass'] = true;

        return $order;
    }
    /**
     * 进行库存量检测
     *
     * @param int $order_id 订单的id
     * @return array $status 订单的状态
     */
      public static function checkOrderStock(int $order_id)
     {
         //获取订单商品信息
         $order = OrderProductModel::where('order_id', '=', $order_id)->select();
         //获取订单中对应商品在商品数据库中的详情
         $order_db = self::getProductsByOrder($order);
         //获取订单状态
         $status = self::getOrderStatus($order, $order_db);

         return $status;
     }

    /**
     * 根据客户端的订单从数据库查询商品
     * @param array $product_client 客户端的订单商品详情
     * @return array $products 从数据库中查询到的对应商品的详情
     */
    private function getProductsByOrder($product_client)
    {
        //保存所有客户端传递的商品id；
        $product_ids = [];
        foreach($product_client as $item) array_push($product_ids, $item['product_id']);
        $products = ProductModel::all($product_ids);
        //通过集合指定要显示的数据
        $products = collection($products)
            ->visible(['id', 'price', 'stock', 'name', 'main_img_url'])
            ->toArray();

        return $products;
    }
    /**
     * 获取订单状态-综合库存信息确定是否允许下单
     *
     *
     *
     *
     */
    private function getOrderStatus($product_client, $product_db)
    {
        //订单组
        $status = [
            //是否满足下单条件
            'pass' => true,
            //订单总价
            'order_price' => 0.00,
            //订单商品总量
            'total_count' => 0,
            //商品种类总量
            'product_count' => 0,
            //单品详情
            'order_items' =>[]
        ];

        foreach($product_client as $item)
        {
            //获取单品详情
            $temp = $this->getProductStatus($item['product_id'], $item['count'], $product_db);
            //检查单品库存是否满足下单
            if(!$temp['stock']) $status['pass'] = false;
            //统计订单价格
            $status['order_price'] += $temp['total_price'];
            //统计商品数量
            $status['total_count'] += $temp['count'];
            //将单品加入订单组中
            array_push($status['order_items'], $temp);
        }
        //统计商品种类数量
        $status['product_count'] = count($status['order_items']);

        return $status;
    }

    /**
     * 格式化单品信息，检查单品是否允许下单
     *
     * @param int $product_id 订单中产品在product表中的id
     * @param int $product_count 单个产品的购买数量
     * @param array $products 从数据库中查询到的客户端订单对应的单品信息
     * @return mixed 失败抛出异常或者成功返回单品详细
     */
    private function getProductStatus($product_id, $product_count, $products)
    {
        $index = -1;
        //单品详情
        $status = [
            //商品id
            'id' => null,
            //库存是否满足订单需求量
            'stock' => false,
            //单品数量
            'count' => 0,
            //单品名称
            'name' => '',
            //单品单价
            'price' => 0.00,
            //主图
            'main_img_url' => '',
            //总价格
            'total_price' => 0.00
        ];
        //检测单品是否存在
        foreach($products as $key =>$value)
        {
            if($product_id == $value['id']) $index = $key;
        }
        //若单品不存在便抛出异常
        if(-1 == $index) throw new OrderException([
            'message' => 'id为'.$product_id.'的商品不存在，创建订单失败'
        ]);
        //格式化单品信息
        $product = $products[$index];
        $status['id'] = $product['id'];
        $status['name'] = $product['name'];
        $status['count'] = $product_count;
        $status['price'] = $product['price'];
        $status['main_img_url'] = $product['main_img_url'];
        $status['total_price'] = $product['price'] * $product_count;
        //检测是否满足下单条件
        if($product['stock'] - $product_count >= 0) $status['stock'] = true;

        return $status;
    }
    /**
     * 生成订单快照
     *
     * @param array $status 订单详情
     * @param array $snap 订单快照
     */
     private function snapOrder($status)
     {
         //保存快照数组
         $snap = [
             //订单总价
             'order_price' => 0.00,
             //商品总数量
             'total_count' => 0,
             //商品种类总数量
             'product_count' => 0,
             //订单地址快照
             'snap_address' => '',
             //快照名称
             'snap_name' => '',
             //快照主图
             'snap_image' => '',
             //快照商品
             'snap_items' => []
         ];

         $snap['order_price'] = $status['order_price'];
         $snap['total_count'] = $status['total_count'];
         $snap['product_count'] = $status['product_count'];
         $snap['snap_address'] = $this->getUserAddress($this->u_id);
         $snap['snap_name'] = $this->product_db[0]['name'];
         $snap['snap_image'] = $this->product_db[0]['main_img_url'];
         $snap['snap_items'] = $status['order_items'];
         if(count($this->product_db) > 1) $snap['snap_name'] .= '等';

         return $snap;
     }

     /**
      * 获取订单快照地址
      *
      * @param array $u_id 订单用户id
      * @param array $u_address 用户地址
      */
     private function getUserAddress($u_id)
     {
         //获取地址
         $u_address = UserAddressModel::where('user_id', '=', $u_id)->find();
         //地址不存在
         if(!$u_address) throw new UserException([
             'message' => '用户收货地址不存在，下单失败',
             'error_code' => 60001
         ]);

         return $u_address;
     }

     /**
      * 生成订单
      *
      * 将订单信息插入order表和order_product中间表
      * 为保证order表与order_product表的数据一致性，启用事务
      * @param array $snap 订单快照
      *
      */
     private function createOrder($snap)
     {
         try{
             //启用事务
             Db::startTrans();
             //生成订单编号
             $order_code = $this->makeOrderNo();
             //保存订单数据到数据库
             $order = new OrderModel();
             $order->user_id = $this->u_id;
             $order->order_no = $order_code;
             $order->total_price = $snap['order_price'];
             $order->total_count = $snap['total_count'];
             $order->snap_img = $snap['snap_image'];
             $order->snap_name = $snap['snap_name'];
             $order->snap_address = $snap['snap_address'];
             $order->snap_items = json_encode($snap['snap_items']);
             $order->save();

             //向中间表插入数据
             $order_id = $order->id;
             $create_time = $order->create_time;
             $products = $this->product_client;

             foreach($products as &$item) $item['order_id'] = $order_id;
             //ORM插入数据
             $order_product = new OrderProductModel();
             $order_product->saveAll($products);
             Db::commit();

         }catch(Exception $e){
             //出现异常启用事务回滚
             Db::rollback();
             //抛出异常
             throw $e;
         }

         return [
             'order_no' => $order_code,
             'order_id' => $order_id,
             'create_time' => $create_time
         ];
     }
     /**
      * 生成订单号
      *
      * @see intval
      * @see date
      * @see strtoupper
      * @see substr
      * @see dechex
      * @see time
      * @see microtime
      * @see sprintf
      * @see rand
      * @return string $$order_code 订单号
      */
      public static function makeOrderNo()
      {
          $Y_CODE = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
          //生成订单编号节点
          $year = $Y_CODE[intval(date('Y')) - 2017];
          $month = strtoupper(dechex(date('m'))).date('d');
          $timestamp = substr(time(), -5).substr(microtime(), 2, 5).sprintf('%02d', rand(0,99));
          //组合生成订单编号
          $order_code = $year.$month.$timestamp;

          return $order_code;
      }
      /**
       *
       * 发送模板消息
       *
       * @param int $orderID 订单id
       * @param string $jump_page 小程序跳转页面
       * @return bool 发送结果
       */
       public function delivery($orderID, $jump_page = '')
       {
           $osrder = OrderModel::where('id', '=', $orderID)->find();
           if(!$order) throw new OrderException();
           //只有在已支付状态时才可以发送
           if($order->status != OrderStatusEnum::PAID) throw new OrderException([
               'message' => '订单还未付款或者订单已经更新',
               'error_code' => 800002,
               'code' => 403
           ]);
           //更新订单状态
           $order->status = OrderStatusEnum::DELIVERED;
           $order->save();
           //发送消息模板
           $message = new DeliveryMessageService();
           $result = $message->sendDeliveryMessage($order, $jump_page);

           return $result;
       }

}
