<?php

namespace App\Api\Controllers;

use App\Helpers\Helper;
use App\Helpers\WXBizDataCrypt;
use App\Http\Requests\BindMobilePost;
use App\Models\City;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\RegisterUserPost;
use App\User;
use App\Api\components\WebController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Ramsey\Uuid\Uuid;

class UserController extends WebController
{
//    public function __construct(Request $request){
//        parent::init();
//        if (empty($this->userId)) {
//            self::needLogin();
//        }
//    }


    public function index()
    {
        $data = [
            'title' => '注册成功',
            'desc' => '您已成功注册成为爱谛沉香会员!',
            'btn' => '观看视频',
            'url' => '../article'
        ];
        self::showMsg($data);
    }


    /** 注册 */
    public function registerView(Request $request)
    {

        list($info, $status) = $this->userInfo();
        if ($status) {
            return redirect()->action('UserController@center');
        }
        $invite = DB::table('invite')->where('user_id', session('user_id'))->first();
        if (!empty($invite)) {
            $inviteUserInfo = DB::table('user_info')->where('user_id', $invite->level_1)->first();
        }
        return view('api.user.register', [
            'data' => session('_old_input'),
            'invite_user_mobile' => empty($inviteUserInfo->bind_mobile) ? '' : $inviteUserInfo->bind_mobile,
            'codeError' => $request->input('codeError')
        ]);
    }

    public function login()
    {

    }


    /**
     * @param Request $request
     * @SWG\Get(path="/api/user/info",
     *   tags={"用户中心"},
     *   summary="",
     *   description="Author: OYYM",
     *   @SWG\Parameter(name="code", in="query", default="", description="code", required=true,
     *     type="string",
     *   ),
     *   @SWG\Parameter(name="nickname", in="query", default="佚名", description="用户昵称", required=true,
     *     type="string",
     *   ),
     *   @SWG\Parameter(name="avatar", in="query", default="default_user_photo10.png", description="头像地址", required=true,
     *     type="string",
     *   ),
     *   @SWG\Response(
     *       response=200,description="successful operation"
     *   )
     * )
     */
    public function info(Request $request)
    {
        $res = $this->weixin($request->code);
        if (!empty($res)) {
            $result = json_decode($res, true);
            $model = DB::table('users')->where(['open_id' => $result['openid'], 'is_real' => User::TYPE_REAL_PERSON])->first();
            $token = md5(md5($result['openid'] . $result['session_key']));
            list($province, $city) = City::randCity();
            $data = [
                'session_key' => $result['session_key'],
                'open_id' => $result['openid'],
                'email' => rand(1000000, 999999999) . '@163.com',
                'nickname' => $request->nickname ?: '佚名',
                'name' => $request->nickname ?: '佚名',
                'avatar' => $request->avatar ?: $this->getImage('default_user_photo10.png'),
                'is_real' => USER::TYPE_REAL_PERSON,
                'token' => $token,
                'province' => $province,
                'city' => $city
            ];
            if ($model) {
                Redis::hdel('token', $model->token);
                DB::table('users')->where('id', $model->id)->update($data);
            } else {
                $model = new User();
                $model->saveData($data);
            }
            Redis::hset('token', $token, $model->id);
            self::showMsg(['token' => $token]);
        }
    }

    /**
     * 批量注册用户
     */
    public function batchRegister()
    {
        $model = new User();
        for ($i = 0; $i < 1000; $i++) {
            $model->rebotRegister();
        }
    }

    /** 修改用户信息 */
    public function update()
    {
        list($info, $status) = $this->userInfo();
        foreach ($info as $key => $item) {
            $data[$key] = $item;
        }
        $user = DB::table('users')->where('id', $info->user_id)->first();
        $data['email'] = $user->email;
        return view('api.user.update', ['data' => $data]);
    }

    /** 修改用户信息表单提交 */
    public function updatePost(Request $request)
    {
        $this->weixinWebOauth(); // 需要网页授权登录
        // file_put_contents('/tmp/test.log', '授权登录成功' . PHP_EOL, FILE_APPEND);
        $user = new User();
        list($info, $status) = $this->userInfo();
        $openId = session('wechat_user')['id'];
        if ($user->userUpdate($request->input(), session('user_id'))) {
            foreach ($info as $key => $item) {
                $data[$key] = $item;
            }
            $user = DB::table('users')->where('id', $info->user_id)->first();
            $data['email'] = $user->email;
            return view('api.user.update', ['status' => 1, 'data' => $data]);
        }
    }

    /** 绑定手机号 */
    public function binddingMobile()
    {
        list($info, $status) = $this->userInfo();
        $data = [
            'bind_mobile' => $info->bind_mobile

        ];
        return view('api.user.binding_mobile', ['oldPut' => session('_old_input'), 'data' => $data]);
    }

    /** 绑定手机号提交表单 */
    public function binddingMobilePost(BindMobilePost $request)
    {
        $user = new User();
        list($msg, $status) = $user->bindMobile($request->input(), session('user_id'));
        if ($status < 0) {
            return redirect()->action('UserController@binddingMobile', ['data' => $request->input(), 'codeError' => $msg]);
        }
        return view('api.user.bindmobile_success');
    }

    /**
     * /**
     * @SWG\Get(path="/api/user/center",
     *   tags={"用户中心"},
     *   summary="用户中心",
     *   description="Author: OYYM",
     *   @SWG\Parameter(name="name", in="query", default="", description="",
     *     type="string",
     *   ),
     *   @SWG\Response(
     *       response=200,description="successful operation"
     *   )
     * )
     */
    public function center()
    {
        $data = array(
            'id' => 1328115335,
            'avatar' => 'https://qnimg.gogobids.com/avatar/default_user_avatar.png',
            'nickname' => '131****7904',
            'mobile_num' => '13161057904',
            'create_time' => 1529628433047.0,
            'type' => 0,
            'status' => 1,
            'app_id' => 1003,
            'register_type' => 1,
            'real_bids' => 0,
            'voucher_bids' => 0,
            'shop_bids' => 0,
            'integration' => 5,
            'is_black' => NULL,
            'qq_num' => '1052156115',
            'discounts' => 0,
        );
        self::showMsg($data);
    }

    /**
     * @SWG\Get(path="/api/user/my-auction",
     *   tags={"用户中心"},
     *   summary="我的竞拍",
     *   description="Author: OYYM",
     *   @SWG\Parameter(name="name", in="query", default="", description="",
     *     type="string",
     *   ),
     *   @SWG\Response(
     *       response=200,description="
     *              period_id => 期数id
     *              product_id => 产品id
     *              period_code => 期数代码
     *              title => 标题
     *              img_cover => 封面url地址
     *              bid_step => 竞拍步骤
     *              product_type => 产品类型
     *              is_purchase_enable => 是否可加价
     *              sell_price => 售价
     *              num => 数量
     *              settlement_bid_price => 成交价
     *              pay_real_price => 真正成交价格
     *              end_time => 结束时间
     *              is_long_history => 是否很长时间
     *              id => 订单id
     *              bid_type => 竞拍类型 （0 = 正常竞拍 , 1 = 差价购买）
     *              order_type => 订单类型 （ 10 = 未支付 , 15 = 已付款 ,20 = 待发货 , 25 = 已发货 , 50 = 买家已签收 , 100 = 已完成）
     *              result_status => 结果类型 [根据这个判断在哪块显示]（0 = 我在拍 , 1= 我拍中 , 2 = 差价购 , 3= 待付款 , 4 = 待签收 , 5 = 待晒单）
     *              pay_status => 支付状态 （0=>未支付 , 1=已支付）
     *              pay_time => 支付时间
     *              pay_price => 支付价格
     *              pay_shop_bids => 支付商铺竞价
     *              order_time => 订单时间
     *              check_status => 检查状态
     *              return_voucher_bids => 返还的购物币
     *              used_voucher_bids => 使用的真实购物币
     *              nickname => 昵称
     *              delivery_id => 交货id
     *              delivery_mode => 交货模式
     *              delivery_code => 交货代码
     *              delivery_state => 交货状态
     *              delivery_state_desc => 交货详情
     *              show_confirm_trans => 是否展示确认收货按钮
     *     "
     *   )
     * )
     */
    public function MyAuction()
    {
        $data = array(
            'period_id' => 4372346,
            'product_id' => 626,
            'period_code' => '201806210002',
            'title' => 'Apple iPhone 8 Plus 256G 颜色随机',
            'img_cover' => '1505284333822',
            'bid_step' => 1,
            'product_type' => 0,
            'is_purchase_enable' => 1,
            'sell_price' => '8787.00',
            'num' => 1,
            'settlement_bid_price' => '862.00',
            'pay_real_price' => '862.00',
            'end_time' => 1529635328574.0,
            'is_long_history' => 0,
            'id' => 'b1b7b2ae63f70914016424fa59fc0d53',
            'bid_type' => 0,
            'order_type' => 4,
            'result_status' => 2,
            'pay_status' => 1,
            'pay_time' => NULL,
            'pay_price' => 0,
            'pay_shop_bids' => 0,
            'order_time' => NULL,
            'check_status' => 0,
            'return_voucher_bids' => 0,
            'used_voucher_bids' => 5,
            'user_id' => 1328115335,
            'nickname' => '一心一意',
            'delivery_id' => NULL,
            'delivery_mode' => NULL,
            'delivery_code' => NULL,
            'delivery_state' => NULL,
            'delivery_state_desc' => NULL,
            'delivery_title' => '',
            'show_confirm_trans' => 0,
        );
        self::showMsg($data);

    }

    /**
     * @SWG\Get(path="/api/user/shipping-address",
     *   tags={"用户中心"},
     *   summary="收货地址",
     *   description="Author: OYYM",
     *   @SWG\Parameter(name="offset", in="query", default="", description="",
     *     type="string",
     *   ),
     *   @SWG\Response(
     *       response=200,description="successful operation"
     *   )
     * )
     */
    public function shippingAddress()
    {
        $data = array(
            'id' => 61303,
            'user_id' => 1328115335,
            'true_name' => '张三',
            'address' => '青海 果洛州 甘德县',
            'address_street' => '江千乡',
            'address_detail' => '四巷胡同1008号',
            'mobile_num' => '18779284965',
            'alipay_name' => '张文吉',
            'alipay_num' => '187792948@163.com',
            'qq_num' => '1052156115',
            'is_default' => 1,
            'remark' => '胡同',
            'address_type' => 2,
            'province_code' => '29',
            'city_code' => '2605',
            'district_code' => '2607',
            'street_code' => '16694',
        );
        self::showMsg($data);
    }

}
