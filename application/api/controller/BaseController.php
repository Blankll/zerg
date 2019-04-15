<?php
namespace app\api\controller;

use think\Controller;
use app\api\service\BaseToken;

class BaseController extends Controller {

    /**
     * 通用权限
     *
     * 验证scope权限是否满足[用户和管理员都可以访问的接口权限]
     *
     * @return boolean
     */
    protected function checkPrimayScope()
    {
        BaseToken::needPrimaryScope();
    }
    /**
     * user only
     *
     * 验证scope权限是否满足[只有用户level才可以访问的权限]
     *
     * @return boolean
     */
    protected function checkExclusiveScope()
    {
        BaseToken::needExclusiveScope();
    }

}
