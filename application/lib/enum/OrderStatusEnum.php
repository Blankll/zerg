<?php
namespace app\lib\enum;

class OrderStatusEnum {
    //待支付
    const UNPAID = 1;
    //已支付，未发货
    const PAID = 2;
    //已支付，已发货
    const DELIVERED = 3;
    //已支付，库存不足
    const PAID_UNDERSTOCK = 4;
}
