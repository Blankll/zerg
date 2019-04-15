<?php
namespace app\api\controller\v1;

use app\api\{
    validate\AddressValidate,
    service\BaseToken,
    model\UserModel,
    model\UserAddressModel,
    controller\BaseController
};
use app\lib\exception\{UserException,SuccessMessage,TokenException,ForbiddenException};
use app\lib\enum\ScopeEnum;

class Address extends BaseController {
    //通过前置方法检测权限
    protected $beforeActionList = [
        'checkPrimayScope' => ['only' => 'createOrUpdate,getUserAddress']
    ];

    /**
     * 添加或创建地址
     *
     * @return object 成功信息
     */
    public function createOrUpdate()
    {
        //验证请求参数
        $validate = new AddressValidate();
        $validate->checkRequestId();
        // 根据请求Token来获取u_id
        $u_id = BaseToken::getCurrentUid();
        //根据u_id来查找用户数据
        $user = UserModel::get($u_id);
        if(!$user) throw new UserException();
        //获取用户从客户端提交的地址信息
        $request_data = $validate->getDataByRule(input('post.'));
        //根据用户地址信息是否存在，添加或更新地址
        $address = $user->address;
        if(!$address) $user->address()->save($request_data);
        else $user->address->save($request_data);

        return json(new SuccessMessage("用户地址操作成功"),201);
    }
    /**
     * 获取用户的地址
     *
     * @return object $address;
     */
     public function getUserAddress()
     {
         // 根据请求Token来获取u_id
         $u_id = BaseToken::getCurrentUid();
         //查询数据库
         $u_address = UserAddressModel::where('user_id', '=', $u_id)->find();
         //检验地址信息是否存在
         if(!$u_address) throw new UserException([
             'message' => '用户地址不存在',
             'error_code' => 60001
         ]);

         return $u_address;
     }
}
