<?php
namespace app\api\controller\v1;

use app\api\model\CategoryModel;
use app\lib\exception\CategoryException;


class Category {

    public function getAllCategories()
    {
        /**
         * all([], 'img') []表示查询所有数据， 'img' 表示关联模型
         */
        $categories = CategoryModel::all([], 'img');
        if(!$categories) throw new CategoryException();
        return $categories;
    }
}
