<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;
/**
 *  Route::rule('路由表达式','路由地址','请求类型','路由参数(数组)','变量规则(数组)');
 *  三段式路由地址表达式： 模块名/控制器名/操作方法名
 *
 *
 */

Route::get('/','Index/index');
Route::get('banner/test','api/v1.Banner/test');

Route::get('api/:version/banner/:id', 'api/:version.Banner/getBanner');

Route::get('api/:version/theme', 'api/:version.Theme/getSimpleList');
Route::get('api/:version/theme/:id', 'api/:version.Theme/getComplexOne');

// Route::get('api/:version/product/recent', 'api/:version.Product/getRecent');
// Route::get('api/:version/product/by_category', 'api/:version.Product/getAllInCategory');
// Route::get('api/:version/product/detail/:id', 'api/:version.Product/productDetail');

Route::group('api/:version/product', function(){
    Route::get('/recent', 'api/:version.Product/getRecent');
    Route::get('/by_category', 'api/:version.Product/getAllInCategory');
    Route::get('/detail/:id', 'api/:version.Product/productDetail');
});
Route::get('api/:version/category/all', 'api/:version.Category/getAllCategories');

Route::post('api/:version/token/user', 'api/:version.Token/getToken');
Route::post('api/:version/token/verify', 'api/:version.Token/verifyToken');
Route::post('api/:version/token/app', 'api/:version.Token/getAppToken');

Route::post('api/:version/address', 'api/:version.Address/createOrUpdate');
Route::get('api/:version/address', 'api/:version.Address/getUserAddress');

Route::post('api/:version/order','api/:version.Order/placeOrder');
Route::post('api/:version/order/by_user', 'api/:version.Order/getSummaryByUser');
Route::get('api/:version/order/detail/:id','api/:version.Order/getDetail');
Route::get('api/:version/order/paginate', 'api/:version.Order/getSummary');
Route::put('api/:version/order/delivery', 'api/:verion.Order/delivery');

Route::post('api/:version/pay/pre_order', 'api/:version.Pay/getPreOrder');
Route::post('api/:version/pay/notify', 'api/:version.Pay/receiveNotify');
