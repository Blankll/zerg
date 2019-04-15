<?php

namespace app\api\model;

use app\api\model\BaseModel;

class ProductImageModel extends BaseModel {
    //指定数据表名
    protected $table = 'product_image';
    //隐藏字段
    protected $hidden = ['img_id', 'product_id', 'delete_time', 'update_time'];
    //关联模型 hasMany('关联模型名','外键名','主键名',['模型别名定义']);
    public function imageUrl()
    {
        return $this->belongsTo('ImageModel', 'img_id', 'id');
    }


}
