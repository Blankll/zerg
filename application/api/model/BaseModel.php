<?php

namespace app\api\model;

use think\{Model, Env};

class BaseModel extends Model{
    /**
     * 设置图片资源的url前缀地址
     *
     * getUrlAttr($value, $data) 读取器的特定格式，获取url字段的值
     * $value 读取器获取的目标字段的值保存在该变量中
     * $data 模型对应的表的所有字段的值以数组的形式保存在$data中
     * $data['from'] 通过该字段确定图片保存的地址是在服务器本地还是专有图片服务器上
     */
    protected function prefixImgUrl($value, $data)
    {
        $final_url = $value;
        $prefix = Env::get('IMG_URL_PREFIX');
        if($data['from'] == 1) $final_url = $prefix.$value;

        return $final_url;
    }
    /**
     *
     * 将json字符串转化为对象数组
     *
     * @param string $value json字符串
     * @return json 对象
     */
    protected function serializationJSON($value)
    {
        if(empty($value)) return null;

        return json_decode($value);
    }
}
