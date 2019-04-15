<?php

namespace app\api\model;

use app\api\model\BaseModel;

class ImageModel extends BaseModel {
    //模型对应的数据表名
    protected $table = 'image';
    //ORM查询时隐藏的字段
    protected $hidden = ['id', 'from', 'delete_time', 'update_time'];
    //读取器
    public function getUrlAttr($value, $data)
    {
        return $this->prefixImgUrl($value, $data);
    }
}
