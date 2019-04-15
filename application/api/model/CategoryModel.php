<?php

namespace app\api\model;

use think\Model;

class CategoryModel extends Model
{
    //数据表名
    protected $table = 'category';
    //隐藏字段
    protected $hidden = ['delete_time', 'update_time', 'create_time'];
    //关联模型
    public function img()
    {
        //一对一模型关系
        return $this->belongsTo('ImageModel', 'topic_img_id', 'id');
    }
}
