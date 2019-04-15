<?php
namespace app\api\controller\v1;

use app\api\validate\IDMustBePostiveInt;
use app\api\model\BannerModel;
use app\lib\exception\BannerMissException;

class Banner {
    /**
     * 获取指定id所属的banner信息
     * @url banner/：id
     * @http GET
     * @id banner 的id
     *
     */
     public function getBanner($id)
     {
          /**
           * thinkPHP验证
           * 独立验证
           */
        /**********************************************************************
        |    data = ['name' => 'seven', 'email' => 'zilisheng@126.com'];
        +
        |    $validate = new Validate([
        +        'name' => 'require|max30',
        |        'email' => 'require|eamil'
        +    ])
        |    //逐个验证，只要有一个不满足便返回false
        +    $result = $validate->check($data);
        |    //全部验证，将所有不满足的信息返回
        +    $result = $validate->batch()->check($data);
        |***********************************************************************/
         /**
          * thinkPHP验证器验证
          */
        /***********************************************************************
        |     $data = ['id' => $id];
        +     $validate = new IDMustBePostiveInt();
        |     $result = $validate->batch()->check($data);
        +     var_dump($result);
        ************************************************************************/
        //验证id的正确性
        (new IDMustBePostiveInt())->checkRequestId();
        //获取banner数据
        //$banner = BannerModel::getBannerByID($id);
        /**
         * items 关联方法
         * items.image 当前模型(BannerModel)关联的模型(BannerItemModel)关联模型(ImageModel)的方法
         * $id 当前模型的id组件
         */
        //$banner = BannerModel::with(['items','items.image'])->find($id);
        //封装了嵌套模型关联的getBannerById()
        $banner = BannerModel::getBannerByID($id);
        //检测是否查询到banner信息
        if(!$banner) throw new BannerMissException();
        //返回banner数据
        return $banner;
     }
     public function test()
     {
         return 233;
     }
}
