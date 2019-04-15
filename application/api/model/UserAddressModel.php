<?php

namespace app\api\model;

use app\api\model\BaseModel;

class UserAddressModel extends BaseModel {
    //定义数据表
    protected $table = 'user_address';
    //隐藏字段
    protected $hidden = ['id', 'delete_time', 'update_time', 'user_id'];
}
