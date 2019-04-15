<?php

namespace app\api\model;

use app\api\model\BaseModel;

class ProductPropertyModel extends BaseModel{
    //指定数据表
    protected $table = 'product_property';
    //隐藏字段
    protected $hidden = ['product_id', 'delete_time', 'id'];
    
}
