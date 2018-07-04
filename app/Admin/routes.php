<?php

use Illuminate\Routing\Router;


Admin::registerAuthRoutes();
//Route::pattern('id', '[0-9]+');

Route::group([
    'prefix' => config('admin.route.prefix'),
    'namespace' => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
], function (Router $router) {

    $router->group([], function ($router) {

        /* @var \Illuminate\Routing\Router $router */
        $router->resource('users', 'UserController');
        //优惠券
        $router->resource('coupon', 'couponController');
        //拍卖师
        $router->resource('auctioneer', 'AuctioneerController');
        //产品分类
        $router->resource('product-type', 'ProductTypeController');
    });

    $router->get('/', 'HomeController@index');

    //用户中心
    $router->any('users', 'UserController@index');
    $router->get('users/{id}/edit', 'UserController@edit');
    $router->get('users/create', 'UserController@create');


    //图片管理
    $router->get('image', 'ImageController@index');
    $router->get('image/{id}/edit', 'ImageController@edit');
    $router->get('image/create', 'ImageController@create');
    


});
