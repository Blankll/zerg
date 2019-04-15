<?php

namespace app\api\model;

use think\Model;

class BannerItemModel extends Model {
    //模型对应的表名
    protected $table = 'banner_item';
    //ORM 查询时隐藏的字段
    protected $hidden = ['id', 'img_id', 'update_time', 'delete_time', 'banner_id'];
    /**
     * 建立模型关联
     * $this 当前模型
     * ImageModel 关联模型名称
     * img_id 关联模型外键名
     * id 当前模型的id组件
     */
    public function image()
    {
        return $this->belongsTo('ImageModel','img_id','id');
    }
}
