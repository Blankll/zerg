<?php

namespace app\api\model;

use app\api\model\BaseModel;


class ProductModel extends BaseModel{
    //指定数据表名
    protected $table = 'product';
    //隐藏字段
    protected $hidden = [
        'delete_time', 'update_time', 'main_img_id', 'pivot', 'from',
        'category_id', 'create_time', 'update_time'
    ];
    //外键关联 hasMany('关联模型名','外键名','主键名',['模型别名定义']);
    public function imgs()
    {
        return $this->hasMany('ProductImageModel', 'product_id', 'id');
    }
    public function properties()
    {
        return $this->hasMany('ProductPropertyModel', 'product_id', 'id');
    }

    /**
     * 通过修改器修改前缀[getMainImgUrlAttr--固定格式]
     *
     * @param string $value 读取器获取的目标字段的值保存在该变量中
     * @param object $data 模型对应的表的所有字段的值以数组的形式保存在$data中
     * @return string $value 修改后的路径
     */
    public function getMainImgUrlAttr($value, $data)
    {
        return $this->prefixImgUrl($value, $data);
    }

    /**
     * 获取最新产品
     *
     * @param int $count 产品数量
     * @return object $products 产品集合
     */
    public static function getMostRecent($count)
    {
        $products = self::limit($count)->order('create_time desc')->select();
        return $products;
    }

    /**
     * 获取分类下的产品
     *
     * @param int $id 分类id
     * @return object $products 产品集合
     */
    public static function getListInCategory($id)
    {
        $products = self::where('category_id', '=', $id)->select();
        return $products;
    }

    /**
     * 获取产品的详情
     *
     * @param int $id 产品id
     *@return object $product 产品详情集合
     */
     public static function getDetail($id)
     {
         /***************************************************
         |
         + 通过闭包对关联模型的子模型进行排序
         |
         ***************************************************/
         $product = self::with(['imgs'])
             ->with(['imgs' => function($query){
                 $query->with(['imageUrl'])
                    ->order('order','asc');
             }])
             ->with(['properties'])
             ->find($id);

         return $product;
     }



}
