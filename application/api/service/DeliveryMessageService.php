<?php
namespace app\api\service;


class DeliveryMessageService extends WxMessageService {
    const DELIVERY_MSG_ID = 'your template id';
    /**
     *
     * 发送模板消息
     *
     * @param object $order 订单实体模型
     * @param string $template_jump_page 发送小程序跳转页面
     * @return bool 发送结果
     */
    public function sendDeliveryMessage($order, $template_jump_page = '')
    {
        if(!$order) throw new OrderException();
        $this->tplID = self::DELIVERY_MSG_ID;
        $this->formID = $order->prepay_id;
        $this->page = $template_jump_page;
        $this->prepareMessageData($order);
        $this->emphasisKeyWord='keyword2.DATA';

        return parent::sendMessage($this->getUserOpenID($order->user_id));
    }
    /**
     *
     * 格式化模板消息
     * @param object $order order模型
     * @return
     */
     private function prepareMessageData($order)
     {
         $dt = \DateTIme();
         $data = [
             'keyword1' => [
                 'value' => '顺丰速运'
             ],
             'keyword2' => [
                 'value' => $order->snap_name,
                 'color' => '#247088'
             ],
             'keyword1' => [
                 'value' => $order->snap_no
             ],
             'keyword2' => [
                 'value' => $order->$dt->format("Y-m-d H:i")
             ]
         ];

         $this->data = $data;
     }
     /**
      *
      * 获取openid
      *
      * @param int $u_id 用户id
      * @return string openid
      */
      private function getUserOpenID($u_id)
      {
          $user = UserModel::get($u_id);
          if(!$user) throw new UserException();

          return $user->openid;
      }
}
