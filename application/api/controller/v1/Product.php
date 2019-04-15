<?php
namespace app\api\controller\v1;

use app\api\validate\Count;
use app\api\model\ProductModel;
use app\lib\exception\ProductException;
use app\api\validate\IDMustBePostiveInt;

class Product {

    /**
     * 获取新品
     *
     * @param int $count 获取新品的数量
     * @return object $products 返回产品集合
     */
    public function getRecent($count = 16)
    {
        //验证参数
        (new Count())->checkRequestId();
        $products = ProductModel::getMostRecent($count);
        if(!$products) throw new ProductException();
        //使用数据集临时隐惨字段
        $collection = collection($products);
        $products = $collection->hidden(['summary']);

        return $products;
    }

    /**
     * 获取指定分类下的所有产品
     *
     * @param int $count 分类的id
     * @return object $products 产品集合
     */
    public function getAllInCategory($id)
    {
        //验证参数
        (new IDMustBePostiveInt())->checkRequestId();
        //获取产品
        $products = ProductModel::getListInCategory($id);
        //验证
        if(!$products) throw new ProductException();
        //使用数据集临时隐藏数据
        $products = collection($products)->hidden(['summary']);

        return $products;
    }

    /**
     * 获取单个产品的细节
     *
     * @param int $id 产品的id
     * @return object $detail 产品细节
     */
     public function productDetail($id)
     {
         //验证参数id
         (new IDMustBePostiveInt())->checkRequestId();
         //获取数据
         $product = ProductModel::getDetail($id);
         //验证
         if(!$product) throw new ProductException();

         return $product;
     }

     //删除产品
     public function deleteOne()
     {

     }


}
