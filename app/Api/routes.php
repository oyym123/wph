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
    Route::get('home/get-period', 'HomeController@getPeriod');
    Route::get('home/deal-end', 'HomeController@dealEnd');

    //最新成交
    Route::get('latest-deal', 'LatestDealController@index');

    //产品
    Route::get('product', 'ProductController@index');
    Route::get('product/type', 'ProductController@type');
    Route::get('product/detail', 'ProductController@detail');

    Route::get('product/bid-rules', 'ProductController@bidRules');
    Route::get('product/past-deals', 'ProductController@pastDeals');
    Route::get('product/period', 'ProductController@period');
    Route::get('product/history-trend', 'ProductController@historyTrend');
    Route::get('product/shop-list', 'ProductController@shopList');
    Route::get('product/shop-detail', 'ProductController@shopDetail');
    Route::get('product/history-trend', 'ProductController@historyTrend');
    Route::get('product/past-deal', 'ProductController@pastDeal');


    //竞拍
    Route::post('bid/bidding', 'BidController@bidding');
    Route::get('bid/record', 'BidController@record');
    Route::get('bid/auto', 'BidController@auto');
    Route::post('bid/newest-bid', 'BidController@newestBid');
    Route::get('bid/auto-info', 'BidController@autoInfo');

    /** 用户中心 */
    Route::post('user/address', 'UserController@address'); //用户收货地址

    Route::get('user/property', 'UserController@property'); //我的竞拍
    Route::get('user/batch-register', 'UserController@batchRegister');//批量用户注册
    Route::get('user/shopping-currency', 'UserController@shoppingCurrency');//批量用户注册
    Route::get('user/evaluate', 'UserController@evaluate');//批量用户注册
    Route::get('/balance-desc', function () {
        return view('api.user.balance-desc');
    });


    /** 订单中心 */
    Route::get('order/my-auction', 'OrderController@MyAuction'); //我的竞拍
    Route::get('order/confirm-receipt', 'OrderController@confirmReceipt'); //确认收货
    Route::get('order/transport-detail', 'OrderController@transportDetail'); //运输详情


    /** 晒单 */
    Route::post('evaluate/submit', 'EvaluateController@submit'); //提交晒单
    Route::get('evaluate', 'EvaluateController@index'); //晒单列表

    //收藏
    Route::get('collection/collect', 'CollectionController@collect');


    Route::get('user/register-view', 'UserController@registerView');//用户注册视图
    Route::get('user/info', 'UserController@info');//用户注册提交表单
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

