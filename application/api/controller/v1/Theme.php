<?php

namespace app\api\controller\v1;

use think\Controller;
use app\api\validate\IDCollection;
use app\api\model\ThemeModel;
use app\lib\exception\ThemeException;
use app\api\validate\IDMustBePostiveInt;

class Theme extends Controller{
    /**
     * @url /theme?ids=id1,id2,id3...
     * @return ThemeModel
     */
    public function getSimpleList($ids='')
    {
        //验证参数
        (new IDCollection())->checkRequestId();
        //拆分数值
        $ids = explode(',', $ids);
        $result = ThemeModel::with(['topicImg', 'headImg'])->select($ids);
        if(!$result) throw new ThemeException();
        return $result;
    }
    public function getComplexOne($id)
    {
        //验证id
        (new IDMustBePostiveInt())->checkRequestId();
        //获取数据
        $theme = ThemeModel::getThemeWithProducts($id);
        //验证数据
        if(!$theme) throw new ThemeException();

        return $theme;
    }
}
