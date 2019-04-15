<?php
namespace app\api\model;

use app\api\model\BaseModel;

class OrderModel extends BaseModel {
    //指定数据表
    protected $table = 'order';
    //隐藏字段
    protected $hidden = ['user_id', 'delete_time', 'update_time'];
    //开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    //读取器 预处理图片地址格式
    public function getSnapItemsAttr($value)
    {
        return serializationJSON($value);
    }
    //读取器 预处理地址格式
    public function getSnapAddressAttr($value)
    {
        return serializationJSON($value);
    }

    /**
     *
     * 获取用户订单分页
     *
     * @param int $u_id 用户id
     * @param int $page 当前页码
     * @param int $size 单页数据条数
     * @return object $paging_data 页面数据
     */
     public static function  getSummaryByUser($u_id, $page = 1, $size = 15)
     {
         //订单分页
         $paging_data = slef::where('user_id', '=', $u_id)
            ->order('create_time desc')
            ->paginate($size, true, ['page' => $page]);

            return $paging_data;
     }
     /**
      *
      * 获取所有订单
      *
      * @param int $page 当前页码
      * @param int $size 单页数据条数
      * @return object $paging_data 页面数据
      */
      public static function getSummaryByPage($page = 1, $size = 20)
      {
          $paging_data = self::order('create_time desc')->paginate($size, true, ['page' => $page]);

          return $paging_data;
      }
}
