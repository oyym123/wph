<?php
/**
 * Api数据接口路由
 */
use App\Models\Auctioneer;
use Illuminate\Support\Facades\Route;

Route::any('server', 'ServerController@index'); // 这个要放到中间件的外面

Route::group(['prefix' => 'swagger'], function () {
    Route::get('json', 'SwaggerController@getJSON');
    Route::get('my-data', 'SwaggerController@getMyData');
});


Route::get('/auctioneer', function () {
    return new \App\Http\Resources\AuctioneerCollection(Auctioneer::paginate());
});

Route::group(['middleware' => 'web'], function () {

    //新手指引banner链接
    Route::get('/newbie-guide', function () {
        return view('api.home.newbie_guide');
    });

    Route::get('auctioneer', 'AuctioneerController@index');

    // 测试
    Route::get('server/test', 'ServerController@test');
    Route::get('user/index2', 'UserController@index');

    Route::any('user/get-invite-qr-code', 'UserController@getInviteQrCode');
    Route::get('home/success', 'HomeController@success');
    Route::get('home/success-view', 'HomeController@successView');

    //首页
    Route::get('home', 'HomeController@index');
    Route::get('home/banner', 'HomeController@banner');
    Route::get('home/deal-end', 'HomeController@dealEnd');
    Route::get('home/hot-auction', 'HomeController@hotAuction');

    //最新成交
    Route::get('latest-deal', 'LatestDealController@index');

    //产品
    Route::get('product', 'ProductController@index');
    Route::get('product/type', 'ProductController@type');
    Route::get('product/detail', 'ProductController@detail');
    Route::get('product/bid-record', 'ProductController@bidRecord');
    Route::get('product/bid-rules', 'ProductController@bidRules');
    Route::get('product/past-deals', 'ProductController@pastDeals');
    Route::get('product/share-order', 'ProductController@shareOrder');

    /** 用户中心 */
    Route::get('user/shipping-address', 'UserController@shippingAddress');//用户注册视图
    Route::get('user/my-auction', 'UserController@MyAuction');//用户注册视图

    //收藏
    Route::get('collection', 'CollectionController@index');


    Route::get('user/register-view', 'UserController@registerView');//用户注册视图
    Route::get('user/register', 'UserController@register');//用户注册提交表单
    Route::post('user/update-post', 'UserController@updatePost');//用户注册提交表单
    //Route::get('user/register-success', 'UserController@registerSuccess');//视图

    Route::any('user/update', 'UserController@update'); //用户修改（个人中心）
    Route::any('user/binding-mobile', 'UserController@binddingMobile'); //绑定手机号（个人中心）
    Route::post('user/binding-mobile-post', 'UserController@binddingMobilePost'); //绑定手机号（个人中心）
    Route::any('wechat', 'WechatController@server');
    Route::any('test', 'UserController@test');
    Route::any('user/center', 'UserController@center');
    /** 推广 */
    Route::any('invite', 'InviteController@index');
    Route::any('invite/qrcode', 'InviteController@qrcode');
    Route::any('invite/view', 'InviteController@view');


    /** 会员卡与积分 */
    Route::any('user-point-card', 'UserPointCardController@index');
    Route::any('user/member-card', 'UserController@memberCard');


    Route::any('weixin-oauth-callback', 'WeixinOauthCallbackController@index');
    Route::any('weixin-oauth-callback/test', 'WeixinOauthCallbackController@test');

    /** 积分兑换 */
    Route::any('point/exchange', 'PointController@exchange');
    /** 发送短信验证码 */
    Route::any('sms/send', 'SmsController@send');
    /** 文章 会员视频 */
    Route::any('article', 'ArticleController@index');
});

