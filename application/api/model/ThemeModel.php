<?php

namespace app\api\model;

use think\Model;

class ThemeModel extends Model{
    //指定数据表名
    protected $table = 'theme';
    //隐藏字段
    protected $hidden = ['delete_time', 'update_time',
                         'topic_img_id', 'head_img_id'];
    //关联模型
    public function topicImg()
    {
        //Theme主题与topicimage 属于一对一的关系
        return $this->belongsTo('ImageModel', 'topic_img_id', 'id');
    }
    public function headImg()
    {
        //Theme主题与headimage 属于一对一的关系
        return $this->belongsTo('ImageModel', 'head_img_id', 'id');
    }
    /**
     *建立多对多的模型关联
     *
     * ProductModel 关联模型名
     * theme_product 中间表名[是表名，不是模型名]
     * product_id 中间表关联从表外键名
     * theme_id 关联表关联主表外键名
     */
    public function products()
    {
        return $this->belongsToMany('ProductModel', 'theme_product', 'product_id', 'theme_id');
    }
    /**
     * 获取themeid对应的产品列表和头图
     *
     * @return array()
     */
    public static function getThemeWithProducts($id)
    {
        return self::with(['topicImg', 'headImg', 'products'])->find($id);
    }
}
