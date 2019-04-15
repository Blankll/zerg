<?php
namespace app\api\validate;

use app\lib\exception\ParameterException;
use app\api\validate\BaseValidate;

class OrderPlaceValidate extends BaseValidate {
    // //客户端请求服务器参数样例
    // protected $products = [
    //     [
    //         'product_id' =>1,
    //         'count' =>2
    //     ],
    //     [
    //         'product_id' =>2,
    //         'count' =>3
    //     ],
    //     [
    //         'product_id' =>3,
    //         'count' =>4
    //     ]
    // ];

    //验证规则
    protected $rule = [
        'products' => 'checkProducts'
    ];
    //嵌套下的验证规则
    protected $single_rule = [
        'product_id' => 'require|isInt',
        'count' => 'require|isInt'
    ];

    /**
     * 通过嵌套的validate实现二位数组的验证
     *
     * @param array $products 产品列表
     * @return boolean 结果
     */
    protected function checkProducts($products)
    {
        if(!is_array($products)) throw new ParameterException([
            'message' => '商品参数格式不正确'
        ]);
        if(empty($products)) throw new ParameterException([
            'message' => '商品参数不能为空'
        ]);
        if(count($products) == count($products, 1)) throw new ParameterException([
            'message' => '商品参数格式不正确'
        ]);
        //验证第二维的值
        foreach($products as $key => $value) $this->checkProduct($value);

        return true;
    }

    /**
     * 嵌套验证第二维参数
     *
     * @param array $product
     * @return boolean 结果
     */
    private function checkProduct($product)
    {
        //建立验证机制
        $validate = new BaseValidate($this->single_rule);
        $result = $validate->check($product);
        //验证结果
        if(!$result) throw new ParameterException([
            'message' => '产品参数列表错误'
        ]);

        return true;
    }
}
